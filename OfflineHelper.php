<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 28/07/18
 * Time: 3.34
 */

class OfflineHelper{

    private $workdir;

    public function __construct($workdir){
        $this->workdir = $workdir;
    }

    public function checkLyrics($artist, $title){
        return is_file($this->workdir . "/lyrics/$artist/$title.lrc");
    }

    public function saveLyrics($artist, $title, $lyrics, $overwrite = false){
        @mkdir($this->workdir . "/lyrics");
        @mkdir($this->workdir . "/lyrics/$artist");
        if(!$this->checkLyrics($artist, $title) || $overwrite)
            file_put_contents($this->workdir . "/lyrics/$artist/$title.lrc", $lyrics);
    }

}