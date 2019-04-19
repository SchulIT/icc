<?php

namespace App\Markdown;

use Emojione\Client;
use Emojione\Ruleset;

class EmojiClientFactory {

    public static function createEmojiClient(string $baseUrl) {
        $client = new Client(new Ruleset());
        $client->imagePathPNG = $baseUrl . 'build/emoji/png/';
        $client->imagePathSVG = $baseUrl . 'build/emoji/svg/';
        $client->imageType = 'svg';

        return $client;
    }
}