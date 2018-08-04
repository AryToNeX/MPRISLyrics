<?php

namespace AryToNeX\MPRISLyrics\providers;

class OfflineProvider extends Provider{

    public const PROVIDER_PRIORITY = 100;

    public function fetchLyrics(string $artist, string $title) : ?string{
        return $this->offlineHelper->getLyrics($artist, $title);
    }

}