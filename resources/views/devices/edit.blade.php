@extends('layouts.app')
@section('title', 'Edit Device')
@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('devices.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-surface-50 hover:bg-primary-50 text-muted hover:text-primary-600 transition-colors duration-150 cursor-pointer border border-primary-100/60">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="section-title">Edit Device</h2>
            <p class="section-subtitle">{{ $device->name }}</p>
        </div>
    </div>
    <div class="card p-6">
        <form action="{{ route('devices.update', $device) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-text mb-1.5">Device Name</label>
                <input type="text" name="name" value="{{ $device->name }}" class="input-field" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1.5">Device Code</label>
                <input type="text" name="device_code" value="{{ $device->device_code }}" class="input-field font-mono" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1.5">Location</label>
                <input type="text" name="location" value="{{ $device->location }}" class="input-field">
            </div>
            <div class="flex items-center gap-3">
                 <input type="hidden" name="is_active" value="0">
                 <input type="checkbox" name="is_active" value="1" id="is_active" {{ $device->is_active ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-primary-300 text-primary-600 focus:ring-primary-200 cursor-pointer">
                 <label for="is_active" class="text-sm text-text cursor-pointer">Active Status</label>
            </div>
            <div class="pt-4 border-t border-primary-100/60 flex justify-end gap-3">
                <a href="{{ route('devices.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" class="w-4 h-4"></i> Update Device
                </button>
            </div>
        </form>
    </div>
</div>
@endsection



