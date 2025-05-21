<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Building;
use App\Models\Contract;
use App\Models\WaterBill;
use App\Models\ElectricityBill;
use App\Models\ServiceRequest;
use App\Models\Suggestion;

class DashboardController extends Controller
{
    public function index()
    {
        $projects = Project::count();
        $buildings = Building::count();
        $contracts = Contract::count();
        
        // Get count of unpaid water bills
        $negativeLatestWaterBills = WaterBill::where('current_balance', '<', 0)
            ->whereIn('id', function($subquery) {
                $subquery->select(\DB::raw('MAX(id)'))
                        ->from('water_bills')
                        ->groupBy('building_id');
            })->count();

        // Get count of unpaid electricity bills
        $negativeLatestElectricityBills = ElectricityBill::where('current_balance', '<', 0)
            ->whereIn('id', function($subquery) {
                $subquery->select(\DB::raw('MAX(id)'))
                        ->from('electricity_bills')
                        ->groupBy('building_id');
            })->count();

        // Get latest 5 records for each table
        $latestContracts = Contract::with('building')
            ->latest()
            ->take(5)
            ->get();

        $latestSuggestions = Suggestion::with('user.owner.building')
            ->latest()
            ->take(5)
            ->get();

        $latestServiceRequests = ServiceRequest::with('user.owner.building')
            ->latest()
            ->take(5)
            ->get();

        $latestWaterBills = WaterBill::with('building')
            ->latest()
            ->take(5)
            ->get();

        $latestElectricityBills = ElectricityBill::with('building')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'projects', 
            'buildings', 
            'contracts',
            'negativeLatestWaterBills',
            'negativeLatestElectricityBills',
            'latestContracts',
            'latestSuggestions',
            'latestServiceRequests',
            'latestWaterBills',
            'latestElectricityBills'
        ));
    }
}