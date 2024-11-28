<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Device;
use App\Models\DeviceStatus;
use App\Models\DeviceLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateDeviceStatus extends Command
{
    protected $signature = 'device:update-status';
    protected $description = 'Update device status to offline if no new screenshot in the last 1 minute';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('UpdateDeviceStatus command started.');

        $fiveMinuteAgo = Carbon::now()->subMinutes(5);

        $devices = Device::all();

        foreach ($devices as $device) {
            $isOnline = $device->status && $device->status->updated_at >= $fiveMinuteAgo;

            // Update device status
            DeviceStatus::updateOrCreate(
                ['device_id' => $device->id],
                ['is_online' => $isOnline, 'updated_at' => Carbon::now()]
            );

            // Log device status
            DeviceLog::create([
                'device_id' => $device->id,
                'is_online' => $isOnline,
                'created_at' => Carbon::now()
            ]);
        }

        Log::info('Device statuses updated and logged.');

        $this->info('Device statuses updated and logged successfully.');
    }
}