@extends('layouts.app')
@section('title', 'Register Device')
@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('devices.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-50 hover:bg-primary-50 text-muted hover:text-primary-600 transition-colors duration-150 cursor-pointer border border-primary-100/60">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="section-title">Register New Device</h2>
            <p class="section-subtitle">Add a new scanning device to the system</p>
        </div>
    </div>
    <div class="card p-6">
        <form action="{{ route('devices.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-text mb-1.5">Device Name</label>
                <input type="text" name="name" class="input-field" required placeholder="e.g. Front Door">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1.5">Device Code (Unique ID)</label>
                <input type="text" name="device_code" class="input-field font-mono" required placeholder="e.g. DEV-001">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1.5">Location</label>
                <input type="text" name="location" class="input-field" placeholder="e.g. Lobby">
            </div>
            <div class="pt-4 border-t border-primary-100/60 flex justify-end gap-3">
                <a href="{{ route('devices.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" class="w-4 h-4"></i> Save Device
                </button>
            </div>
        </form>
    </div>
</div>
@endsection



