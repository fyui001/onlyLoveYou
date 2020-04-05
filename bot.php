<?php
require_once(__DIR__ .'/vendor/autoload.php');
require_once(__DIR__ .'/src/main.php');

$conf = parse_ini_file('./config.ini', true);

$discord = new \Discord\Discord([
    'token' => $conf=['BOT']['TOKEN'],
]);

$discord->on('ready', function ($discord) {

    $discord->on('message', function ($message) {

        $insertDataArr = [
            'usr' => $message->author->user->username,
            'msg' => $message->content,
            'guild' => $message->channel->guild->name,
        ];

        if ( preg_match('/しか(好き|すき)じゃ(ねえ|ない|ねぇ)/', $insertDataArr['msg']) ) {

            $OnlyLoveYou = new OnlyLoveYou;
            $OnlyLoveYou->create($insertDataArr);

        } elseif ( preg_match('/(おれ|俺|お！れ！)の/', $insertDataArr['msg']) ) {

            $VoiceActorOwnership = new VoiceActorOwnership;
            $VoiceActorOwnership->create($insertDataArr);

        }

    });
});

$discord->run();
