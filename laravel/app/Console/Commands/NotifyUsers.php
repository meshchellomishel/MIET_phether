<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use DefStudio\Telegraph\Models\TelegraphChat;

class NotifyUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-users';

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
        $changed_ids = array();
        $now = Carbon::now()->setTimezone('MSK');
        $users = DB::table('user__settings')
            ->select('tg_id', 'user__settings.city_name',
                'user__settings.notify_time AS time', 'user__settings.user_id AS user_id',
                'user__settings.change_notify', 'notified')
            ->join('tgUsers', 'tgUsers.id', '=', 'user__settings.user_id')
            ->where('user__settings.mute', '=', false)
//            ->where('user__settings.notified', '=', false)
//            ->orWhere('user__settings.change_notify', '=', true)
            ->whereRaw('(change_notify=true OR notified=false)')
            ->where('user__settings.notify_time', '<', $now)
            ->get();

        $now = Carbon::now();
        foreach ($users as &$user) {
            $weather_changed = false;
            $userTimeZone = 'Europe/Moscow';
            $user_time = Carbon::parse($user->time, $userTimeZone);
            $need_skip_by_diff = $now->diffInHours($user_time) > 1;

            if ($need_skip_by_diff && !$user->change_notify)
                continue;

            info($user->tg_id);
            $chat_id = DB::table('telegraph_chats')
                ->where('chat_id', '=', $user->tg_id)
                ->first()->id;

            $chat = TelegraphChat::find($chat_id);
            $weather = DB::table('weatherStatus')
                ->where('city', '=', $user->city_name)
                ->get();

            $message = "";
            $changed = "";
            foreach ($weather as &$weth) {
                if ($user->notified && !$weth->weather_changed)
                    continue;
                else if ($weth->weather_changed &&
                        Carbon::parse($weth->time, $userTimeZone)->gt($now)) {
                    $weather_changed = true;
                    $changed_ids []= $weth->id;
                }

                $message .= (
                    $weth->time . "\t\t\t\t" . $weth->temp . "\t\t\t\t\t" . $weth->mm . "\n"
                );
            }
            if ($message == "" ||
                ($need_skip_by_diff && !$weather_changed))
                return;
            if ($weather_changed)
                $changed = "***Changed***";

            $message = (
                "***" . $user->city_name . "***\n\n" .
                "time           temp   mm\n" .
                $message . "\n" . $changed
            );

            $chat->message($message)->send();
            DB::table('user__settings')
                ->where('city_name', '=', $user->city_name)
                ->where('user_id', '=', $user->user_id)
                ->update([
                    'notified' => true,

                    'updated_at' => Carbon::now()
                ]);

            foreach ($changed_ids as &$id) {
                DB::table('weatherStatus')
                    ->where('id', '=', $id)
                    ->update([
                        'weather_changed' => false,

                        'updated_at' => Carbon::now()
                    ]);
            }
        }
    }
}
