@extends('layouts.app')
@section('title', 'Edit Device')
@section('content')
<div class="max-w-xl mx-auto">
    <div class="mb-6 flex justify-between">
        <h2 class="text-xl font-bold text-slate-800">Edit Device</h2>
        <a href="{{ route('devices.index') }}" class="text-slate-500 hover:text-slate-700 text-sm"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form action="{{ route('devices.update', $device) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Device Name</label>
                <input type="text" name="name" value="{{ $device->name }}" class="w-full rounded-lg border-slate-300 focus:ring-primary-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Device Code</label>
                <input type="text" name="device_code" value="{{ $device->device_code }}" class="w-full rounded-lg border-slate-300 focus:ring-primary-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Location</label>
                <input type="text" name="location" value="{{ $device->location }}" class="w-full rounded-lg border-slate-300 focus:ring-primary-500">
            </div>
            <div class="flex items-center gap-2 mt-4">
                 <input type="hidden" name="is_active" value="0">
                 <input type="checkbox" name="is_active" value="1" id="is_active" {{ $device->is_active ? 'checked' : '' }} class="rounded text-primary-600 focus:ring-primary-500">
                 <label for="is_active" class="text-sm text-slate-700">Active Status</label>
            </div>
            <div class="pt-2 flex justify-end gap-2">
                <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">Update Device</button>
            </div>
        </form>
    </div>
</div>
@endsection