<?php

class OfflineProvider extends Provider{

    const PROVIDER_PRIORITY = 100;

    public function fetchLyrics($artist, $title){
        return $this->offlineHelper->getLyrics($artist, $title);
    }

}