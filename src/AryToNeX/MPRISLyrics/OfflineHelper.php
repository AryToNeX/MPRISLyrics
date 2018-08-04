<?php

namespace AryToNeX\MPRISLyrics;

class OfflineHelper{

    private $workdir;

    public function __construct(string $workdir){
        $this->workdir = $workdir;
        if(!is_dir($workdir)) mkdir($workdir);
    }

    public function checkLyrics(string $artist, string $title) : bool{
        return is_file($this->workdir . "/lyrics/$artist/$title.lrc");
    }

    public function getLyrics(string $artist, string $title) : ?string{
        if($this->checkLyrics($artist, $title))
            return file_get_contents($this->workdir . "/lyrics/$artist/$title.lrc");
        return null;
    }

    public function saveLyrics(string $artist, string $title, string $lyrics, bool $overwrite = false) : void{
        @mkdir($this->workdir . "/lyrics");
        @mkdir($this->workdir . "/lyrics/$artist");
        if(!$this->checkLyrics($artist, $title) || $overwrite)
            file_put_contents($this->workdir . "/lyrics/$artist/$title.lrc", $lyrics);
    }

}