<?php

/*
 * One thing before you read this code:
 * I don't even know why this works. I am coding this
 * night by night, till the end of August, I suppose.
 * 
 * There are bugs in the way the lyrics are displayed.
 * All these bugs are related to MPRIS not telling this
 * lyrics displayer the microseconds in the position of
 * a track. So please don't laugh at me, I tried to do
 * my best.
 *
 * As always thanks to StackOverflow for this project.
 */

use AryToNeX\MPRISLyrics\Display;
use AryToNeX\MPRISLyrics\LrcFactory;
use AryToNeX\MPRISLyrics\OfflineHelper;
use AryToNeX\MPRISLyrics\Options;
use AryToNeX\MPRISLyrics\PlayerCtl;
use AryToNeX\MPRISLyrics\Status;
use AryToNeX\MPRISLyrics\Versioning;

// version check
if(version_compare(PHP_VERSION, "7.1.0") < 0){
    echo "Your PHP version is outdated, please install PHP >= 7.1.0 to use this program.\n";
    exit(-1);
}
if(!function_exists("curl_version")){
    echo "PHP cURL extension is not installed/enabled. You must have it installed and enabled to use this program.\n";
    exit(-1);
}
if(exec("which playerctl 2> /dev/null") == ""){
    echo "Playerctl is not installed on this system. Please install it to use this program.\n";
    exit(-1);
}

// Handle Ctrl + C (SIGINT)
pcntl_async_signals(true);
pcntl_signal(SIGINT, function(){
    echo "\033[2J\033[H";
    exit(0);
});

$opts = new Options(array(
    "d" => "required",
    "h" => "novalue",
    "l" => "required",
    "o" => "required",
    "p" => "required",
    "r" => "required"
));

if($opts->getOption("h")){
    echo "MPRISLyrics by AryToNeX - version ".Versioning::getVersion()."
USAGE: <MPRISLyricsPath> [-d mode] [-r number of rows]
                         [-p player] [-l path] [-o ms]

OPTION         VALUES
    -d         singleline: displays the current verse on a line, then keeps that line
                           updated.
               linebyline: prints the lyrics as the song keeps playing, outputting
                           them line by line.
               rows:       prints N (usually 5) rows of lyrics, with the one in the
                           center being in bold character format.
    -l         Path where you want your lyrics to be saved. It can be relative to workdir
               or absolute path.
               Note: Lyrics will always be placed in a 'lyrics' subfolder in that path.
    -o         Position offset in milliseconds (you can also use negative values).
    -p         Name of the player that you want MPRISLyrics to listen to.
               Note: this is helpful only if at startup there are more than one player
               opened and MPRISLyrics defaults to the 'wrong' one.
    -r         Number of rows in the 'row' display mode. If an even number is
               provided, then 1 (one) is added to that number." . PHP_EOL;
    exit(0);
}

$player = new PlayerCtl();
if(($p = $opts->getOption("p")) !== null){
    foreach($player->getPlayers() as $pl){
        if($pl == $p){
            $player->setActivePlayer($pl);
            break;
        }
    }
}

$lrc = new LrcFactory(new OfflineHelper($opts->getOption("l") ?? getcwd()));
$status = new Status();
$status->setStopped(false);

if(!is_null($opts->getOption("o"))){
    echo floatval($opts->getOption("o"));
    $status->setOffset(floatval($opts->getOption("o")));
}

while(true){
    if (empty($player->getPlayers())) {
        echo "\033[2J\033[H";
        echo "It seems that there are no MPRIS-capable music players opened. Waiting...\n";
        while(empty($player->getPlayers())){
            sleep(1); // delay polling of one second because we don't want to spam commands
        };
        echo "Found a player: " . $player->getPlayers()[0] . "\n";
        $player->setActivePlayer($player->getPlayers()[0] ?? null);
        sleep(1);
    }

    try{
        if($player->getStatus() == "Stopped"){
            if(!$status->isStopped()){
                echo "\033[2J\033[H";
                echo "Music player is stopped; please play some music.\n";
            }
            $status->setStopped(true);
            continue;
        }else{
            $status->setStopped(false);
        }
    }catch (Exception $e){
        // why would this happen? anyway here's some dumb code
        $player->setActivePlayer($player->getPlayers()[0] ?? null);
        continue;
    }

    try{
        $newInfo = array($player->getArtist(), $player->getTitle());
    }catch (Exception $e){
        // why would this happen? anyway here's some dumb code
        $player->setActivePlayer($player->getPlayers()[0] ?? null);
        continue;
    }

    if(array_diff($newInfo, array($status->getArtist(), $status->getTitle())) !== array()){
        echo "\033[2J\033[H";
        echo "Now playing: " . $newInfo[0] . " - " . $newInfo[1] . "\n";
        if($player->getActivePlayer() == "spotify"){
            echo "WARNING: Spotify doesn't tell MPRIS2 the position of the track, so you'll experience static lyrics.\n";
            echo "This issue must be fixed on Spotify itself and there's nothing MPRISLyrics can do to work around this.\n";
        } // TODO: Write a proper warning handler for unsupported / partly supported players
        echo "\n";
        $status->setLyrics($lrc->fetchLyrics($newInfo[0], $newInfo[1]));
        $status->setTrackInfo($newInfo[0], $newInfo[1]);
        $status->setLastLinePosition(-1);
        $status->setLastPosition(0);
        if(is_null($status->getLyrics()))
            echo "No lyrics | service unavailable.\n";
    }
    if(is_null($status->getLyrics())) continue;

    try {
        $position = $player->getPosition();
        $position += $status->getOffset();
    }catch (Exception $e){
        // why would this happen? anyway here's some dumb code
        $player->setActivePlayer($player->getPlayers()[0] ?? null);
        continue;
    }

    switch($opts->getOption("d")){
        case "singleline":
            // This display method displays the current verse on a line, then keeps that line updated.
            Display::displaySingleLine($position, $status);
            break;
        case "linebyline":
            // This display method prints the lyrics as the song keeps playing, outputting them line by line.
            Display::displayWriteTextProcedurally($position, $status);
            break;
        case "rows":
        default:
            // This display method prints X rows of lyrics, with the one in the center being in bold character format.
            Display::displayRows($position, $status, $opts->getOption("r") ?? 5);
            break;
    }

    $status->setLastPosition($position);
}