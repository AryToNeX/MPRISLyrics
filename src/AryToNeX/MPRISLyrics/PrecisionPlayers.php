<?php

namespace AryToNeX\MPRISLyrics;

class PrecisionPlayers{

    private $data;

    public function __construct(){
        $this->data = json_decode(file_get_contents(__DIR__ . "/resources/precision_players.json"), true);
    }
    
    public function isPrecisionPlayer(string $player): bool{
        return $this->data[$player] ?? false;
    }

}
