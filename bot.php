<?php
require_once(__DIR__ .'/vendor/autoload.php');
require_once(__DIR__ .'/src/main.php');
require_once(__DIR__ .'/src/const.php');

$discord = new \Discord\Discord([
    'token' => TOKEN,
]);

$discord->on('ready', function ($discord) {

    $discord->on('message', function ($message) {

        $usr = $message->author->user->username;
        $messageContent = $message->content;
        $guildName = $message->channel->guild->name;

        if ( preg_match('/しか(好き|すき)じゃ(ねえ|ない|ねぇ)/', $messageContent) ) {

            $db = new OnlyLoveYou;
            $db->create($usr, $messageContent, $guildName);

        } elseif ( preg_match('/(おれ|俺|お！れ！)の/', $messageContent) ) {

            $VoiceActorOwnership = new VoiceActorOwnership;
            $VoiceActorOwnership->create($usr, $messageContent, $guildName);

        }

    });
});

$discord->run();
