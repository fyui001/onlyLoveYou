<?php
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/src/main.php');

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$token = $_ENV['BOT_TOKEN'];

$discord = new \Discord\Discord([
    'token' => $token,
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

        } elseif (preg_match('/しか(勝たん|かたん)/',  $insertDataArr['msg'])) {

            $OnlyYouWin = new OnlyYouWin;
            $OnlyYouWin->create($insertDataArr);

        }

    });
});

$discord->run();
