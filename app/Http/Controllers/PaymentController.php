<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function updateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,payment_pending,completed'
        ]);

        $serviceRequest->update([
            'status' => $validated['status']
        ]);

        return redirect()->back()->with('success', 'تم تحديث الحالة بنجاح');
    }
}
