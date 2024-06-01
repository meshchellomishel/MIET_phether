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
        $now = Carbon::now()->setTimezone('MSK');
        $users = DB::table('user__settings')
            ->select('tgUsers.tg_id AS tg_id', 'user__settings.city_name',
                'user__settings.notify_time AS time', 'user__settings.user_id AS user_id')
            ->join('tgUsers', 'tgUsers.id', '=', 'user__settings.user_id')
            ->where('user__settings.mute', '=', false)
            ->where('user__settings.notified', '=', false)
            ->where('user__settings.notify_time', '<', $now)
            ->get();

        $now = Carbon::now();
        foreach ($users as &$user) {
            $userTimeZone = 'Europe/Moscow';
            $user_time = Carbon::parse($user->time, $userTimeZone);
            info($now->diffInMinutes($user_time) . ' ' . $now . ' ' . $user_time);
            if ($now->diffInHours($user_time) > 1)
                continue;

            info($user->tg_id);
            $chat_id = DB::table('telegraph_chats')
                ->where('chat_id', '=', $user->tg_id)
                ->first()->id;

            $chat = TelegraphChat::find($chat_id);
            $weather = DB::table('weatherStatus')
                ->where('city', '=', $user->city_name)
                ->get();

            $message = "***Zelenograd***\n\n";
            $message .= "time           temp   mm\n";
            foreach ($weather as &$weth) {
                $message .= (
                    $weth->time . "\t\t\t\t" . $weth->temp . "\t\t\t\t\t" . $weth->mm . "\n"
                );
            }
            $chat->message($message)->send();
            DB::table('user__settings')
                ->where('city_name', '=', $user->city_name)
                ->where('user_id', '=', $user->user_id)
                ->update([
                    'notified' => true,

                    'updated_at' => Carbon::now()
                ]);
        }
    }
}
