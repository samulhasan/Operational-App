<?php

namespace App\Http\Controllers;
use App\Models\Device;
use App\Models\Screenshot;
use App\Models\DeviceStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DeviceController extends Controller
{
    public function updateScreenshot(Request $request)
    {
        Log::info('Request received', $request->all());

        try {
            // Validasi input
            $request->validate([
                'device_id' => 'required|string',
                'screenshot' => 'required|file|mimes:png,jpg,jpeg'
            ]);

            // Simpan file screenshot
            $path = $request->file('screenshot')->store('public/screenshots');

            // Perbarui atau buat perangkat
            $device = Device::updateOrCreate(
                ['device_id' => $request->device_id],
                ['is_online' => true]
            );

            // Buat entri screenshot
            $screenshot = Screenshot::create([
                'device_id' => $device->id,
                'screenshot_path' => $path,
            ]);

            // Perbarui status perangkat
            DeviceStatus::updateOrCreate(
                ['device_id' => $device->id],
                ['is_online' => true, 'updated_at' => Carbon::now()]
            );

            // Hapus gambar lama jika lebih dari 3
            $this->deleteOldScreenshots($device);

            return response()->json(['message' => 'Screenshot updated successfully']);
        } catch (\Exception $e) {
            Log::error('Error updating screenshot: ' . $e->getMessage());
            return response()->json(['error' => 'Error updating screenshot'], 500);
        }
    }

    private function deleteOldScreenshots(Device $device)
    {
        $screenshots = $device->screenshots()->orderBy('created_at', 'desc')->get();

        if ($screenshots->count() > 3) {
            $screenshotsToDelete = $screenshots->slice(3);

            foreach ($screenshotsToDelete as $screenshot) {
                Storage::delete($screenshot->screenshot_path);
                $screenshot->delete();
            }
        }
    }

    public function getLatestScreenshot()
    {
        try {
            $devices = Device::with('screenshots', 'status')->get();
            $screenshots = [];

            foreach ($devices as $device) {
                $latestScreenshot = $device->screenshots()->latest()->first();
                $screenshots[$device->device_id] = $latestScreenshot ? [
                    'url' => Storage::url($latestScreenshot->screenshot_path),
                    'updated_at' => $latestScreenshot->updated_at->toDateTimeString(),
                    'is_online' => $device->status ? $device->status->is_online : false
                ] : null;
            }

            return response()->json(['screenshots' => $screenshots]);
        } catch (\Exception $e) {
            Log::error('Error fetching latest screenshots: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching latest screenshots'], 500);
        }
    }

    public function showDisplay()
    {
        try {
            $devices = Device::with('screenshots', 'status')->get();
            $screenshots = [];

            foreach ($devices as $device) {
                $latestScreenshot = $device->screenshots()->latest()->first();
                $screenshots[$device->device_id] = $latestScreenshot ? [
                    'url' => Storage::url($latestScreenshot->screenshot_path),
                    'updated_at' => $latestScreenshot->updated_at->toDateTimeString(),
                    'is_online' => $device->status ? $device->status->is_online : false
                ] : null;
            }

            return view('display', ['screenshots' => $screenshots]);
        } catch (\Exception $e) {
            Log::error('Error displaying screenshots: ' . $e->getMessage());
            return response()->json(['error' => 'Error displaying screenshots'], 500);
        }
    }
}