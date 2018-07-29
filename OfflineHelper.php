<?php

class OfflineHelper{

    private $workdir;

    public function __construct($workdir){
        $this->workdir = $workdir;
    }

    public function checkLyrics($artist, $title){
        return is_file($this->workdir . "/lyrics/$artist/$title.lrc");
    }

    public function getLyrics($artist, $title){
        if($this->checkLyrics($artist, $title))
            return file_get_contents($this->workdir . "/lyrics/$artist/$title.lrc");
        return null;
    }

    public function saveLyrics($artist, $title, $lyrics, $overwrite = false){
        @mkdir($this->workdir . "/lyrics");
        @mkdir($this->workdir . "/lyrics/$artist");
        if(!$this->checkLyrics($artist, $title) || $overwrite)
            file_put_contents($this->workdir . "/lyrics/$artist/$title.lrc", $lyrics);
    }

}