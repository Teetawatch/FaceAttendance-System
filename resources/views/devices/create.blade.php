@extends('layouts.app')
@section('title', 'Register Device')
@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex justify-between">
        <h2 class="text-xl font-bold text-text font-bold font-mono font-mono">Register New Device</h2>
        <a href="{{ route('devices.index') }}" class="text-indigo-600/70 hover:text-text text-sm"> Back</a>
    </div>
    <div class="bg-card rounded-xl shadow-sm border border-slate-200/60 p-6">
        <form action="{{ route('devices.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-text mb-1">Device Name</label>
                <input type="text" name="name" class="w-full rounded-lg border-slate-300 focus:ring-primary-500" required placeholder="e.g. Front Door">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Device Code (Unique ID)</label>
                <input type="text" name="device_code" class="w-full rounded-lg border-slate-300 focus:ring-primary-500" required placeholder="e.g. DEV-001">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Location</label>
                <input type="text" name="location" class="w-full rounded-lg border-slate-300 focus:ring-primary-500" placeholder="e.g. Lobby">
            </div>
            <div class="pt-2 flex justify-end gap-2">
                <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">Save Device</button>
            </div>
        </form>
    </div>
</div>
@endsection



