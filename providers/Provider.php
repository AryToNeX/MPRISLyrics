<?php

abstract class Provider{

    public const PROVIDER_PRIORITY = 1;

    /** @var OfflineHelper $offlineHelper */
    protected $offlineHelper;

    public function setOfflineHelper(OfflineHelper $offlineHelper){
        $this->offlineHelper = $offlineHelper;
    }

    abstract public function fetchLyrics(string $artist, string $title) : string;
}