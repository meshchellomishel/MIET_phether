<?php

namespace App\Telegram;

use AllowDynamicProperties;
use App\Setting;
use App\tg_User;
use App\User;
use Carbon\Carbon;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use \Illuminate\Support\Stringable;

class Handler extends WebhookHandler
{
    private function get_current_user_id(): int
    {
        $user_id = $this->chat->storage()->get('user_id');
        if ($user_id == null) {
            $chat_id = $this->chat->chat_id;
            $user_id = DB::table('tgUsers')->where('tg_id', $chat_id)->first()->user_id;
        }

        if ($user_id == null) {
            return -1;
        }

        return $user_id;
    }
    private function get_setting_string(\stdClass $value)
    {
        return $value->city_name . ' ' . $value->notify_time . ' ' .
        ($value->change_notify ? "🔔notify" : "🔕noNotify") . ' ' .
        ($value->mute ? "🔕muted" : "🔊unmuted") . "\n";
    }
    private function get_last_setting()
    {
        $setting = $this->chat->storage()->get('lastSetting');
        if ($setting == null) {
            Telegraph::message(
                "Sorry, bot lost your deleted setting"
            )->send();

            return null;
        }

        return $setting;
    }

    public function start(): void
    {
        $this->reply('Welcome to Phether!');
        TelegraphBot::find(1)->registerCommands([
            'hello' => 'Say hello',
            'help' => 'Show functional',
            'show_settings' => 'Show your setting',
            'new_setting' => 'Add new setting',
            'change_setting' => 'Change your existing setting',
        ])->send();

//        $user_id = $this->chat->storage()->get('user_id');
//        if ($user_id != null) {
//            return;
//        }

        $chat_id = $this->chat->chat_id;
        $tg_user = DB::table('tgUsers')->where('tg_id', $chat_id)->first();
        if ($tg_user != null) {
            return;
        }

        $user_id = DB::table('users')->insertGetId([
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('tgUsers')->insertGetId([
            'user_id' => $user_id,
            'tg_id' => $chat_id,

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->chat->storage()->set('user_id', $user_id);
    }

    public function help(): void
    {
        $this->reply(
            "*Phether* notify about weather:\n\n" .
            "- You can set time when bot should send weather for your city list to you\n" .
            "- Parameter *notify* mean that bot should notify you when weather going to change a lot\n" .
            "- Parameter *mute* mean that you don\`t want to be notified by this setting"
        );
    }
    public function new_setting(string $cmd): void
    {
        $user_id = $this->get_current_user_id();
        if ($user_id == -1) {
            Telegraph::message(
                "[ERROR]: Cannot find user id"
            )->send();
            return;
        }

        $parsed_cmd = explode(' ', $cmd);
        if (count($parsed_cmd) > 4) {
            Telegraph::message(
                "Please add arguments to your command"
            )->send();
            return;
        }

        $city_data = explode(',', $parsed_cmd[0]);
        if (count($city_data) > 3) {
            Telegraph::message(
                "Please try again with different command"
            )->send();
            return;
        }

        $city_name = "";
        $city_state = "";
        $city_country = "";
        if (count($city_data) == 1) {
            $city_name = $city_data[0];
        } else if (count($city_data) == 2) {
            $city_state = $city_data[0];
            $city_name = $city_data[1];
        } else if (count($city_data) == 3) {
            $city_country = $city_data[0];
            $city_state = $city_data[1];
            $city_name = $city_data[2];
        }

        $city = DB::table('cities')->where('city_name', $city_name)->first();
        if ($city == null) {
            $api_key = env('CITY_API_KEY');

            $response = Http::withHeaders([
                'X-Api-Key' => $api_key
            ])->get('https://api.api-ninjas.com/v1/geocoding',[
                'city' => $city_name,
                'state' => $city_state,
                'country' => $city_country,
            ])->json();

            if (count($response) == 0 || $response[0] == null) {
                Telegraph::message(
                    "Your city not supported"
                )->send();
                return;
            }

            DB::table('cities')->insertGetId([
                    'city_name' => $response[0]['name'],
                    'state' => $response[0]['state'],
                    'country' => $response[0]['country'],
                    'longitude' => $response[0]['longitude'],
                    'latitude' => $response[0]['latitude'],


                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
            ]);
        }
        $city = DB::table('cities')->where('city_name', $city_name)->first();
        $city_id = $city->id;
        $city_name = $city->city_name;

        $check = DB::table('user__settings')->where('city_id', $city_id)->first();

        if ($check != null) {
            Telegraph::message(
                "You already have setting for this city, you must change it"
            )->send();

            return;
        }

        DB::table('user__settings')->insertGetId([
            "user_id" => $user_id,
            "city_id" => $city_id,
            "city_name" => $city_name,
            "notify_time" => (count($parsed_cmd) > 1) ? date("H:i:s", strtotime($parsed_cmd[1])) : date("H:i:s", strtotime("12:00:00")),
            "change_notify" => (count($parsed_cmd) > 2) ? $parsed_cmd[2] : true,
            "mute" => (count($parsed_cmd) > 3) ? $parsed_cmd[3] : false,

            "notified" => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->reply("Setting created successfully");
    }

    public function change_setting(string $cmd): void
    {
        $parsed_cmd = explode(' ', $cmd);

        $text = "Your command: \n\n" . implode(' ', $parsed_cmd);

        $this->reply($text);
    }

    public function show_settings(): void
    {
        $user_id = $this->get_current_user_id();
        if ($user_id == -1) {
            Telegraph::message(
                "[ERROR]: Cannot find user id"
            )->send();
            return;
        }

        $settings = DB::table('user__settings')->where('user_id', $user_id)->get();

        if (count($settings) != 0) {
            $buttons = array();
            $i = 1;
            $msg = "Your settings:\n\n";

            foreach ($settings as &$value) {
                $msg .= $i . '. ' . $this->get_setting_string($value);
                $i += 1;
                $buttons[] = Button::make($value->city_name)->action('menu')->param('id', $value->id);
            }

            Telegraph::message(
                $msg
            )->keyboard(
                Keyboard::make()->buttons($buttons)
            )->send();

            return;
        }

        Telegraph::message(
            "You don\`t have any settings yet"
        )->send();
    }
    public function menu(mixed $id): void
    {
        $setting = DB::table('user__settings')->find($id);
        $this->chat->storage()->set('lastSetting', $setting);

        Telegraph::message(
            "Current setting:\n\n" .
            $this->get_setting_string($setting)
        )->keyboard(
            Keyboard::make()->buttons([
                Button::make("✍ change")->action('change'),
                Button::make("❌ delete")->action('delete'),
            ])
        )->send();
    }

    public function change(): void
    {
        $setting = $this->get_last_setting();
        if ($setting == null) {
            return;
        }

        Telegraph::message(
            "Select what you are want to change"
        )->keyboard(
            Keyboard::make()->buttons([
                Button::make("🏢 City")->action('change_city'),
                Button::make("🕒 Time")->action('change_time'),
                Button::make($setting['change_notify'] ? "🔕 No Notify" : "🔔 Notify")->action('change_notify'),
                Button::make($setting['change_notify'] ? "🔊 Unmute" : "🔇 Mute")->action('change_mute'),
            ])
        )->send();
    }

    public function change_mute()
    {
        $setting = $this->get_last_setting();
        if ($setting == null) {
            return;
        }

        DB::table('user__settings')
            ->where('id', $setting['id'])
            ->update([
                "mute" => !($setting['mute'] != 0),

                'updated_at' => Carbon::now()
            ]);

        $this->reply("Changed");
    }
    public function change_notify()
    {
        $setting = $this->get_last_setting();
        if ($setting == null) {
            return;
        }

        DB::table('user__settings')
            ->where('id', $setting['id'])
            ->update([
                "change_notify" => !($setting['change_notify'] != 0),

                'updated_at' => Carbon::now()
            ]);

        $this->reply("Changed");
    }
    public function change_city(): void
    {
        $this->chat->storage()->set('cityStartChange', true);
        $this->reply("Send city in new message:");
    }

    public function change_time(): void
    {
        $this->chat->storage()->set('timeStartChange', true);
        $this->reply("Send time in new message:");
    }

    public function delete(): void
    {
        $setting = $this->get_last_setting();
        if ($setting == null) {
            return;
        }

        DB::table('user__settings')->delete($setting['id']);

        Telegraph::message(
            "Setting was deleted"
        )->keyboard(
            Keyboard::make()->buttons([
                Button::make("↩️ Cancel")->action('cancel'),
            ])
        )->send();
    }

    public function cancel(): void
    {
        $lastSetting = $this->chat->storage()->get('lastSetting');
        if ($lastSetting == null) {
            Telegraph::message(
                "Sorry, bot lost your deleted setting"
            )->send();

            return;
        }

        DB::table('user__settings')->insertGetId([
            "user_id" => $lastSetting['user_id'],
            "city_id" => $lastSetting['city_id'],
            "city_name" => $lastSetting['city_name'],
            "notify_time" => $lastSetting['notify_time'],
            "change_notify" => $lastSetting['change_notify'],
            "mute" => $lastSetting['mute'],

            "notified" => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Telegraph::message(
            "Canceled"
        )->send();
    }
    public function hello(): void
    {
        $this->reply('Hello! This is Phether!');
    }

    protected function handleUnknownCommand(Stringable $text): void
    {
        Log::debug('[TELEGRAM]: Receive unknown command: ' . json_encode($this->message->toArray(), JSON_UNESCAPED_UNICODE));
        $this->reply('I don\`t know this command, please retry');
    }

    protected function handleChatMessage(Stringable $text):void
    {
        $timeStarted = $this->chat->storage()->get('timeStartChange');
        $cityStarted = $this->chat->storage()->get('cityStartChange');
        if ($text == 'cancel') {
            $this->chat->storage()->set('timeStartChange', false);
            $this->chat->storage()->set('cityStartChange', false);
            return;
        }

        $lastSetting = $this->chat->storage()->get('lastSetting');

        Log::debug('[TELEGRAM]: Received message: ' . json_encode($this->message->toArray(), JSON_UNESCAPED_UNICODE));
        if ($cityStarted) {
            $city = DB::table('cities')->where('city_name', $text)->first();
            if ($city == null) {
                Telegraph::message(
                    "Your city not supported"
                )->send();
                return;
            }

            DB::table('user__settings')
                ->where('user_id', '=', $lastSetting['user_id'])
                ->where('city_name', '=', $lastSetting['city_name'])
                ->update([
                    "city_name" => $text,
                    'notified' => false,

                    'updated_at' => Carbon::now()
                ]);

            Telegraph::message("City was updated on " . $text)->send();
            $this->chat->storage()->set('cityStartChange', false);
        }
        if ($timeStarted) {
            DB::table('user__settings')
                ->where('user_id', '=', $lastSetting['user_id'])
                ->where('city_name', '=', $lastSetting['city_name'])
                ->update([
                    "notify_time" => date("H:i:s", strtotime($text)),
                    'notified' => false,

                    'updated_at' => Carbon::now()
                ]);

            Telegraph::message("Time was updated on " . $text)->send();
            $this->chat->storage()->set('timeStartChange', false);
        }
    }
}