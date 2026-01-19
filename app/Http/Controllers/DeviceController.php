<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::latest()->paginate(10);
        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        return view('devices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'device_code' => 'required|string|unique:devices,device_code|max:50',
            'location' => 'nullable|string|max:100',
        ]);

        $data = $request->all();
        // สร้าง API Token อัตโนมัติ (ไว้ให้เครื่องใช้ยิง API)
        $data['api_token'] = Str::random(32);
        
        Device::create($data);

        return redirect()->route('devices.index')->with('success', 'Device registered successfully.');
    }

    public function edit(Device $device)
    {
        return view('devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'device_code' => 'required|string|max:50|unique:devices,device_code,' . $device->id,
            'location' => 'nullable|string|max:100',
        ]);

        $device->update($request->all());

        return redirect()->route('devices.index')->with('success', 'Device updated successfully.');
    }

    public function destroy(Device $device)
    {
        $device->delete();
        return redirect()->route('devices.index')->with('success', 'Device deleted successfully.');
    }
}