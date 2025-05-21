<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use App\Models\User;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function updateToken(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string|min:32' // FCM tokens are typically long strings
            ]);

            $user = auth()->user();
            
            // Check if token is different before updating
            if ($user->fcm_token !== $request->token) {
                $user->fcm_token = $request->token;
                $user->fcm_token_updated_at = now(); // Add timestamp for token updates
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'FCM token updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Token update failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update FCM token'
            ], 500);
        }
    }

    public function sendToAll(Request $request)
    {
        Log::info('Starting sendToAll notification', [
            'request_data' => $request->except(['password', 'token']) // Exclude sensitive data
        ]);

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:2048',
                'data' => 'array|nullable',
                'priority' => ['string', Rule::in(['normal', 'high'])],
                'click_action' => 'string|nullable'
            ]);

            // Get active users with valid tokens
            $tokens = User::where('is_active', true)
                         ->whereNotNull('fcm_token')
                         ->where('fcm_token', '!=', '')
                         ->pluck('fcm_token')
                         ->unique()
                         ->values()
                         ->toArray();

            if (empty($tokens)) {
                Log::warning('No valid FCM tokens found');
                return response()->json([
                    'success' => false,
                    'message' => 'No devices registered for notifications'
                ], 404);
            }

            // Prepare notification data
            $notificationData = [
                'title' => $validated['title'],
                'body' => $validated['body'],
                'data' => $validated['data'] ?? [],
                'priority' => $validated['priority'] ?? 'normal',
                'click_action' => $validated['click_action'] ?? null
            ];

            // Send in batches of 500 to avoid FCM limits
            $batchSize = 500;
            $success = true;

            foreach (array_chunk($tokens, $batchSize) as $tokenBatch) {
                $batchSuccess = $this->firebaseService->sendToMultiple(
                    $tokenBatch,
                    $notificationData['title'],
                    $notificationData['body'],
                    $notificationData['data'],
                    $notificationData['priority']
                );

                if (!$batchSuccess) {
                    $success = false;
                    Log::error('Batch notification failed', [
                        'batch_size' => count($tokenBatch)
                    ]);
                }
            }

            Log::info('Firebase notification completed', [
                'success' => $success,
                'total_recipients' => count($tokens)
            ]);

            return response()->json([
                'success' => $success,
                'message' => $success ? 'Notifications sent successfully' : 'Some notifications failed to send',
                'recipients_count' => count($tokens)
            ]);

        } catch (\Exception $e) {
            Log::error('Firebase notification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send notifications: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $owners = User::where('role', 'owner')->get();
        $buildings = Building::all();

        return view('dashboard.notifications.index', compact('owners', 'buildings'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => ['required', Rule::in(['all', 'owner', 'tenant', 'admin'])],
                'title' => 'required|string|max:255',
                'content' => 'required|string|max:2048',
                'building_id' => 'nullable|exists:buildings,id',
                'priority' => ['string', Rule::in(['normal', 'high'])]
            ]);

            // Build user query
            $query = User::whereNotNull('fcm_token')
                        ->where('is_active', true);

            // Filter by user type if not 'all'
            if ($validated['type'] !== 'all') {
                $query->where('role', $validated['type']);
            }

            // Filter by building if specified
            if (!empty($validated['building_id'])) {
                $query->whereHas('apartments', function($q) use ($validated) {
                    $q->where('building_id', $validated['building_id']);
                });
            }

            $tokens = $query->pluck('fcm_token')
                           ->unique()
                           ->values()
                           ->toArray();

            if (empty($tokens)) {
                return redirect()->back()
                    ->with('error', 'لا يوجد مستخدمين مسجلين للإشعارات')
                    ->withInput();
            }

            // Send notifications in batches
            $batchSize = 500;
            $success = true;

            foreach (array_chunk($tokens, $batchSize) as $tokenBatch) {
                $batchSuccess = $this->firebaseService->sendToMultiple(
                    $tokenBatch,
                    $validated['title'],
                    $validated['content'],
                    [
                        'type' => $validated['type'],
                        'building_id' => $validated['building_id'] ?? null
                    ],
                    $validated['priority'] ?? 'normal'
                );

                if (!$batchSuccess) {
                    $success = false;
                    Log::error('Batch notification failed in store method', [
                        'batch_size' => count($tokenBatch)
                    ]);
                }
            }

            if ($success) {
                return redirect()->back()->with('success', 'تم إرسال الإشعار بنجاح');
            } else {
                return redirect()->back()->with('error', 'بعض الإشعارات فشلت في الإرسال');
            }

        } catch (\Exception $e) {
            Log::error('Push notification failed in store method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إرسال الإشعار')
                ->withInput();
        }
    }
}