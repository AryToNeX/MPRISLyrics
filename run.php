<?php

/*
 * One thing before you read this code:
 * I don't even know why this works. This was coded
 * at night from 11 PM to 5 AM and I was a lot confused and tired.
 *
 * Keep in mind I did this as a Proof of Concept, not as
 * a ready-made CLI lyrics displayer you can use out-of-the-box.
 * 
 * There are bugs in the way the lyrics are displayed.
 * All these bugs are related to MPRIS not telling this
 * lyrics displayer the microseconds in the position of
 * a track. So please don't laugh at me, I tried to do
 * my best.
 *
 * As always thanks to StackOverflow for this project.
 */

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

include_once __DIR__ . "/Display.php";
include_once __DIR__ . "/LrcUtils.php";
include_once __DIR__ . "/LrcFactory.php";
include_once __DIR__ . "/OfflineHelper.php";
include_once __DIR__ . "/PlayerCtl.php";
include_once __DIR__ . "/providers/Provider.php";
include_once __DIR__ . "/providers/Musixmatch.php";
include_once __DIR__ . "/providers/Baidu.php";
include_once __DIR__ . "/providers/OfflineProvider.php";
include_once __DIR__ . "/providers/ViewLyrics.php";

$player = new PlayerCtl();
$lrc = new LrcFactory(new OfflineHelper(__DIR__)); // TODO: Custom lyrics path

$oldInfo = array(null, null);
$text = null;
$noLyrics = false;
$lastline = -1;
$lastposition = 0;
$isStopped = false;

// TODO: A method to calculate microseconds

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
            if(!$isStopped){
                echo "\033[2J\033[H";
                echo "Music player is stopped; please play some music.\n";
            }
            $isStopped = true;
            continue;
        }else{
            $isStopped = false;
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

    if(array_diff($newInfo, $oldInfo) !== array()){
        echo "\033[2J\033[H";
        echo "Now playing: " . $newInfo[0] . " - " . $newInfo[1] . "\n";
        if($player->getActivePlayer() == "spotify"){
            echo "WARNING: Spotify doesn't tell MPRIS2 the position of the track, so you'll experience static lyrics.\n";
            echo "This issue must be fixed on Spotify itself and there's nothing MPRISLyrics can do to work around this.\n";
        }
        echo "\n";
        $noLyrics = false;
        $text = LrcUtils::textArr($lrc->fetchLyrics($newInfo[0], $newInfo[1]));
        $oldInfo = $newInfo;
        $lastline = -1;
        $lastposition = 0;
        if(!isset($text) || $text == ""){
            echo "No lyrics | service unavailable.\n";
            $noLyrics = true;
        }
    }
    if($noLyrics) continue;

    try {
        $position = $player->getPosition();
    }catch (Exception $e){
        // why would this happen? anyway here's some dumb code
        $player->setActivePlayer($player->getPlayers()[0] ?? null);
        continue;
    }


    // CHOOSE ONE OF THE BROKEN DISPLAY METHODS

    /*
     * This display method displays the current verse on a line, then keeps that line updated.
     */
    //Display::displaySingleLine($position, $text, $lastline);

    /*
     * This display method prints the lyrics as the song keeps playing, outputting them line by line.
     */
    //Display::displayWriteTextProcedurally($position, $text, $lastline, $lastposition);

    /*
     * This display method prints X rows of lyrics, with the one in the center being in bold character format.
     */
    Display::displayRows($position, $text, $lastline, 5);

    $lastposition = $position;
}