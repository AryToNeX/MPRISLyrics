<?php

namespace AryToNeX\MPRISLyrics\providers;

use AryToNeX\MPRISLyrics\OfflineHelper;

abstract class Provider{

    public const PROVIDER_PRIORITY = 1;

    /** @var OfflineHelper $offlineHelper */
    protected $offlineHelper;

    public function setOfflineHelper(OfflineHelper $offlineHelper) : void{
        $this->offlineHelper = $offlineHelper;
    }

    abstract public function fetchLyrics(string $artist, string $title) : ?string;
}