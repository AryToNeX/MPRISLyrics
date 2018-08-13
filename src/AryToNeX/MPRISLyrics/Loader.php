<?php

// include libs
foreach(scandir(__DIR__) as $file)
    if(pathinfo($file, PATHINFO_EXTENSION) == "php" && $file !== "MPRISLyrics.php")
        require_once __DIR__ . "/" . $file;

// include abstract provider class
require_once __DIR__ . "/providers/Provider.php";

// include providers
foreach(scandir(__DIR__ . "/providers") as $file)
    if(pathinfo($file, PATHINFO_EXTENSION) == "php" && $file !== "Provider.php")
        require_once __DIR__ . "/providers/" .  $file;

// include main
include_once __DIR__ . "/MPRISLyrics.php";