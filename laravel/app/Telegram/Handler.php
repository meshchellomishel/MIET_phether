<?php

namespace App\Telegram;

use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Support\Facades\Log;
use \Illuminate\Support\Stringable;

class Handler extends WebhookHandler
{
    public function start(): void
    {
        $this->reply('Welcome to Phether!');
        TelegraphBot::find(1)->registerCommands([
            'hello' => 'Say hello',
            'help' => 'Show functional',
            'show_settings' => 'Show your setting',
            'new_setting' => 'Add new setting'
        ])->send();
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
        $parsed_cmd = explode(' ', $cmd);

        $text = "Your command: \n\n" . implode(' ', $parsed_cmd);

        $this->reply($text);
    }

    public function show_settings(): void
    {
        Telegraph::message(
            "Your settings:\n\n" .
            "*1.* Zelenograd 12:00 notified unmuted"
        )->keyboard(
            Keyboard::make()->buttons([
                Button::make("1️⃣")->action('menu')->param('choose', 1),
            ])
        )->send();
    }
    public function menu(int $choose): void
    {
        Telegraph::message(
            "Current setting:\n\n" .
            "\tZelenograd 12:00 notified unmuted"
        )->keyboard(
            Keyboard::make()->buttons([
                Button::make("✍ change")->action('change')->param('id', $choose),
                Button::make("❌ delete")->action('delete')->param('id', $choose),
            ])
        )->send();
    }

    public function change(int $id): void
    {
        Telegraph::message(
            "Setting was successfully changed"
        )->keyboard(
            Keyboard::make()->buttons([
                Button::make("↩️ Cancel")->action('cancel')->param('id', $id),
            ])
        )->send();
    }

    public function delete(int $id): void
    {
        Telegraph::message(
            "Setting was successfully deleted"
        )->keyboard(
            Keyboard::make()->buttons([
                Button::make("↩️ Cancel")->action('cancel')->param('id', $id),
            ])
        )->send();
    }

    public function cancel(int $id): void
    {
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
}