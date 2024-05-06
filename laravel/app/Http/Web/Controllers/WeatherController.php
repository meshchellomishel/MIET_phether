<?php

namespace App\Http\Web\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Response;

class WeatherController
{
    public function store()
    {
        $all = request()->json()->all();
        $status = $all['weatherStatus'];
        if ($status == null) {
            return Response::json([
                'code' => 404,
                'message' => 'There are no filled fields'
            ], 404);
        }

        foreach ($status as $request) {
            info($request);

            if ($request['time'] == null ||
                $request['city'] == null ||
                $request['temp'] == null ||
                $request['mm'] == null) {
                return Response::json([
                    'code' => 404,
                    'message' => 'There are no filled fields'
                ], 404);
            }

            $parsed_time = date("H:i:s", strtotime($request['time']));
            $temp = DB::table('weatherStatus')
                ->where('time', $parsed_time)
                ->where('city', $request['city'])
            ->first();

            if ($temp == null) {

                DB::table('weatherStatus')->insert([
                    'temp' => $request['temp'],
                    'city' => $request['city'],
                    'mm' => $request['mm'],
                    'time' => date("H:i:s", strtotime($request['time'])),

                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                return Response::json([
                    'code' => 200,
                    'message' => 'ok'
                ]);

            } else {
                DB::table('weatherStatus')
                    ->where('id', $temp->id)
                    ->update([
                    'temp' => $request['temp'],
                    'mm' => $request['mm'],

                    'updated_at' => Carbon::now(),
                ]);

                return Response::json([
                    'code' => 200,
                    'message' => 'ok'
                ]);
            }
        }
    }
}