<?php
require_once(__DIR__ .'/vendor/autoload.php');
require_once(__DIR__ .'/src/main.php');
require_once(__DIR__ .'/src/const.php');

$discord = new \Discord\Discord([
    'token' => TOKEN,
]);

$discord->on('ready', function ($discord) {
    echo "Bot is ready.", PHP_EOL;

    $discord->on('message', function ($message) {

        if ( preg_match('/しか(好き|すき)じゃ(ねえ|ない|ねぇ)/', $message->content) ) {
            $db = new main;
            $usr = $message->author->user->username;
            $messageContent = $message->content;
            $db->create($usr, $messageContent);
        }

    });
});


$discord->run();
