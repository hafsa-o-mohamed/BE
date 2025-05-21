<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $serviceRequests = ServiceRequest::with(['service', 'owner', 'apartment'])
            ->orderBy('request_date', 'desc')
            ->paginate(10);

        return view('dashboard.services.requests.index', compact('serviceRequests'));
    }

    public function update(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);
        $serviceRequest->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }
}