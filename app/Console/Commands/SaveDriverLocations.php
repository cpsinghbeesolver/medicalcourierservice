<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Models\DriverProfile;
use Illuminate\Support\Facades\Log;

class SaveDriverLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:save-driver-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::emergency('Location updated');
        $locations = Redis::hgetall('driver_locations');

        foreach ($locations as $driverId => $location) {

            // $location = json_decode($location, true);
            // DriverProfile::where('user_id', $driverId)
            // ->update([
            //     'current_latitude'  => $location['lat'],
            //     'current_longitude' => $location['lng'],
            // ]);
        }
    }
}
