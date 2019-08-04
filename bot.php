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

        $usr = $message->author->user->username;
        $messageContent = $message->content;

        if ( preg_match('/しか(好き|すき)じゃ(ねえ|ない|ねぇ)/', $messageContent) ) {

            $db = new OnlyLoveYou;
            $db->create($usr, $messageContent);

        } elseif ( preg_match('/(おれ|俺|お！れ！)の/', $messageContent) ) {

            $VoiceActorOwnership = new VoiceActorOwnership;
            $VoiceActorOwnership->create($usr, $messageContent);

        }

    });
});


$discord->run();
