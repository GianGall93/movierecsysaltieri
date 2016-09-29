<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

$API_KEY = '297809022:AAHaM0c6-mE2PvrFlEnV7JeHnKXor7JCSgM';
$BOT_NAME = 'MovieRecSysBot';
try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($API_KEY, $BOT_NAME);

    // Unset webhook
    $result = $telegram->unsetWebHook();

    if ($result->isOk()) {
        echo $result->getDescription();
    }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    echo $e;
}
