@extends('layouts.app')
@section('title', 'Register Device')
@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex justify-between">
        <h2 class="text-xl font-bold text-slate-800">Register New Device</h2>
        <a href="{{ route('devices.index') }}" class="text-slate-500 hover:text-slate-700 text-sm"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form action="{{ route('devices.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Device Name</label>
                <input type="text" name="name" class="w-full rounded-lg border-slate-300 focus:ring-primary-500" required placeholder="e.g. Front Door">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Device Code (Unique ID)</label>
                <input type="text" name="device_code" class="w-full rounded-lg border-slate-300 focus:ring-primary-500" required placeholder="e.g. DEV-001">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Location</label>
                <input type="text" name="location" class="w-full rounded-lg border-slate-300 focus:ring-primary-500" placeholder="e.g. Lobby">
            </div>
            <div class="pt-2 flex justify-end gap-2">
                <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">Save Device</button>
            </div>
        </form>
    </div>
</div>
@endsection