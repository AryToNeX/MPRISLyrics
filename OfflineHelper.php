<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 28/07/18
 * Time: 3.34
 */

class OfflineHelper{

    public function checkLyrics($artist, $title){
        return is_file("lyrics/$artist/$title.lrc");
    }

    public function saveLyrics($artist, $title, $lyrics, $overwrite = false){
        @mkdir("lyrics");
        @mkdir("lyrics/$artist");
        if(!$this->checkLyrics($artist, $title) || $overwrite)
            file_put_contents("lyrics/$artist/$title.lrc", $lyrics);
    }

}