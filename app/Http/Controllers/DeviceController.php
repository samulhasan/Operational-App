<?php

namespace App\Http\Controllers;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\Screenshot;
use App\Models\DeviceStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function deleteDevice($deviceId)
    {
        try {
            $device = Device::where('device_id', $deviceId)->first();
            if ($device) {
                $device->delete();
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'error' => 'Device not found'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting device: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Error deleting device'], 500);
        }
    }

    public function showDeviceLogs(Request $request)
    {
        try {
            $query = DeviceLog::with('device'); // Load the related device

            if ($request->filled('device_id')) {
                $query->where('device_id', $request->device_id);
            }

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            if ($request->filled('start_time') && $request->filled('end_time')) {
                $query->whereTime('created_at', '>=', $request->start_time)
                      ->whereTime('created_at', '<=', $request->end_time);
            }

            if ($request->filled('sort')) {
                $query->orderBy('created_at', $request->sort);
            } else {
                $query->orderBy('created_at', 'desc'); // Default to 'desc' for latest first
            }

            $perPage = $request->input('per_page', 10); // Default to 10 rows per page
            $deviceLogs = $query->paginate($perPage);
            $devices = Device::all();

            return view('history', ['deviceLogs' => $deviceLogs, 'devices' => $devices]);
        } catch (\Exception $e) {
            Log::error('Error fetching device logs: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching device logs'], 500);
        }
    }

    public function downloadDeviceLogs(Request $request)
    {
        $query = DeviceLog::with('device'); // Load the related device

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('start_time') && $request->filled('end_time')) {
            $query->whereTime('created_at', '>=', $request->start_time)
                  ->whereTime('created_at', '<=', $request->end_time);
        }

        if ($request->filled('sort')) {
            $query->orderBy('created_at', $request->sort);
        } else {
            $query->orderBy('created_at', 'desc'); // Default to 'desc' for latest first
        }

        $deviceLogs = $query->get();

        $response = new StreamedResponse(function() use ($deviceLogs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Device ID', 'Status', 'Created At']);

            foreach ($deviceLogs as $log) {
                fputcsv($handle, [
                    $log->device->device_id,
                    $log->is_online ? 'Online' : 'Offline',
                    $log->created_at
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="device_logs.csv"');

        return $response;
    }

    public function showDashboard()
    {
        $startDate = Carbon::now()->subDay();
        $endDate = Carbon::now();
        $deviceLogs = DeviceLog::with('device') // Load the related device
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($log) {
                return $log->device->device_id; // Group by device_id from the related device
            })
            ->map(function ($logs) {
                $onlineCount = $logs->where('is_online', true)->count();
                $offlineCount = $logs->where('is_online', false)->count();

                return [
                    'online' => $onlineCount,
                    'offline' => $offlineCount
                ];
            });

        Log::info('Device Logs:', $deviceLogs->toArray());

        return view('dashboard', ['deviceData' => $deviceLogs]);
    }
}