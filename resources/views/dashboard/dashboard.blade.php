@extends('dashboard.layout')

@section('content')
<div class="container mt-4">
    <h1>Dashboard Overview</h1>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Projects</h5>
                    <p class="card-text display-4">{{ $projects }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Buildings</h5>
                    <p class="card-text display-4">{{ $buildings }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Apartments</h5>
                    <p class="card-text display-4">{{ $apartments }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Contracts</h5>
                    <p class="card-text display-4">{{ $contracts }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection