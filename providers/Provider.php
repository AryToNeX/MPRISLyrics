<?php

abstract class Provider{

    const PROVIDER_PRIORITY = 1;

    /** @var OfflineHelper $offlineHelper */
    protected $offlineHelper;

    public function setOfflineHelper($offlineHelper){
        $this->offlineHelper = $offlineHelper;
    }

    abstract public function fetchLyrics($artist, $title);
}