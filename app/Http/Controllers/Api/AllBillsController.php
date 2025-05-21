<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use App\Models\ServiceRequest;

use App\Models\Bill;

use Illuminate\Http\Request;

use App\Models\Contract; // Added Contract model

class AllBillsController extends Controller

{

    public function getUnpaidCount()

    {

        $user = auth()->user();

        $ownerId = $user->owner->id;  // Get the ApartmentOwner ID through the relationship



        // Get count from service_requests table

        $serviceRequestsCount = ServiceRequest::where('owner_id', $ownerId)

            ->where('payment_status', 'unpaid')

            ->where('status', 'completed')

            ->count();

        

        // Get count from bills table

        $billsCount = Bill::where('owner_id', $ownerId)

            ->where('status', 'pending')

            ->count();

        

        // Return combined count

        return response()->json([

            'count' => $serviceRequestsCount + $billsCount

        ]);

    }




    public function getBills()

    {

        $user = auth()->user();

        $ownerId = $user->owner->id;  // Get the ApartmentOwner ID through the relationship

        

        // Get regular bills

        $bills = Bill::where('owner_id', $ownerId)->get();

        

        // Get service request bills

        $serviceRequests = ServiceRequest::where('owner_id', $ownerId)

            ->with('service')

            ->get();

        

        // Calculate totals

        $billsTotal = $bills->sum('due_amount');

        $serviceRequestsTotal = $serviceRequests->sum('due_price');

        

        return response()->json([

            'bills' => $bills,

            'service_requests' => $serviceRequests,

            'bills_total' => $billsTotal,

            'service_requests_total' => $serviceRequestsTotal,

            'total' => $billsTotal + $serviceRequestsTotal

        ]);

    }




    public function getServiceRequests()

    {

        $user = auth()->user();

        $ownerId = $user->owner->id;

        

        $requests = ServiceRequest::where('owner_id', $ownerId)

            ->where('payment_status', 'unpaid')

            ->with('service')

            ->get();

        

        $total = $requests->sum('due_price');


        // Add debugging information
        $debug = [
            'count' => $requests->count(),
            'owner_id' => $ownerId,
            'has_requests' => $requests->isNotEmpty(),
            'first_request' => $requests->first(),
        ];
        
        return response()->json([
            'requests' => $requests,
            'total' => $total,
            'debug' => $debug
        ]);

    }




    // Optional: If you want combined total

    public function getTotalAmount()

    {

        $user = auth()->user();

        $ownerId = $user->owner->id;

        

        $billsTotal = Bill::where('owner_id', $ownerId)->sum('due_amount');

        $requestsTotal = ServiceRequest::where('owner_id', $ownerId)->where('status', 'completed')->where('payment_status', 'unpaid')->sum('due_price');

        

        return response()->json([

            'total' => $billsTotal + $requestsTotal,

            'bills_total' => $billsTotal,

            'requests_total' => $requestsTotal

        ]);

    }




   

}