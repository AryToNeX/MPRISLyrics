<?php

namespace AryToNeX\MPRISLyrics;

class OfflineHelper{

    private $workdir;

    public function __construct(string $workdir){
        $this->workdir = $workdir;
        if(!is_dir($workdir)) mkdir($workdir);
    }

    public function checkLyrics(string $artist, string $title) : bool{
        return is_file($this->workdir . "/lyrics/".$this->sanitizeName($artist)."/".$this->sanitizeName($title).".lrc");
    }

    public function getLyrics(string $artist, string $title) : ?string{
        if($this->checkLyrics($artist, $title))
            return file_get_contents($this->workdir . "/lyrics/".$this->sanitizeName($artist)."/".$this->sanitizeName($title).".lrc");
        return null;
    }

    public function saveLyrics(string $artist, string $title, string $lyrics, bool $overwrite = false) : void{
        @mkdir($this->workdir . "/lyrics");
        @mkdir($this->workdir . "/lyrics/" . $this->sanitizeName($artist));
        if(!$this->checkLyrics($artist, $title) || $overwrite)
            file_put_contents($this->workdir . "/lyrics/".$this->sanitizeName($artist)."/".$this->sanitizeName($title).".lrc", $lyrics);
    }

    private function sanitizeName(string $unsanitizedName) : string{
        $unsanitizedName = htmlentities($unsanitizedName, ENT_QUOTES, 'UTF-8');
        $unsanitizedName = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', $unsanitizedName);
        $unsanitizedName = html_entity_decode($unsanitizedName, ENT_QUOTES, 'UTF-8');
        $unsanitizedName = preg_replace("(<|>|\||\?|\*|\\$|:|\\\\|[\/]|\"|\\0|[\\1-\\37])", "_", $unsanitizedName);

        return trim($unsanitizedName);
    }

}