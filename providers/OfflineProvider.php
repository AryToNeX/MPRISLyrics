<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 28/07/18
 * Time: 3.02
 */

class OfflineProvider{

    const PROVIDER_PRIORITY = 100;

    /** @var OfflineHelper $offlineHelper */
    private $offlineHelper;

    public function setOfflineHelper($offlineHelper){
        $this->offlineHelper = $offlineHelper;
    }

    public function fetchLyrics($artist, $title){
        return $this->offlineHelper->getLyrics($artist, $title);
    }

}