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

// Handle Ctrl + C (SIGINT)
pcntl_async_signals(true);
pcntl_signal(SIGINT, function(){
    echo "\033[2J\033[H";
    exit(0);
});


include_once "PlayerCtl.php";
include_once "Musixmatch.php";
$player = new PlayerCtl();

$oldInfo = array(null, null);
$text = null;
$noLyrics = false;
$lastline = -1;
$lastposition = 0;
$isStopped = false;

if(empty($player->getPlayers())){
    echo "It seems that there are no MPRIS-capable music players opened. Exiting...\n";
    exit(0);
}

// TODO: A method to calculate microseconds

while(true){
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
        echo "\n";
        $noLyrics = false;
        $text = textArr(Musixmatch::stripSyncedLyrics(Musixmatch::fetchLyrics($newInfo[0], $newInfo[1])));
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
    //displaySingleLine($position, $text, $lastline);

    /*
     * This display method prints the lyrics as the song keeps playing, outputting them line by line.
     */
    //displayWriteTextProcedurally($position, $text, $lastline, $lastposition);

    /*
     * This display method prints X rows of lyrics, with the one in the center being in bold character format.
     */
    displayRows($position, $text, $lastline, 5);

    $lastposition = $position;
}

function textArr($rawText){
    if(!isset($rawText) || $rawText == false || $rawText == null) return null;
    $rawText = explode("\n", $rawText);
    $newText = array();
    foreach($rawText as $index => $line){
        $time = explode(":", str_replace(".", ":", substr($line, 1, 8)));
        $time = /*($time[2] / 100) + */ $time[1] + ($time[0] * 60);
        $verse = trim(substr($line, 10));
        $newText[] = array("time" => substr($line, 1, 8), "timestamp" => $time, "verse" => $verse);
    }

    return $newText;
}

function currentLine($rawText, $position){
    $line = -1;
    for($i = 0; $i < count($rawText); $i++){
        if($rawText[$i]["timestamp"] > $position){
            if(isset($rawText[$i-1]))
                $line = $i - 1;
            break;
        }
    }
    return $line;
}

function displaySingleLine($position, $text, &$lastline){
    $line = currentLine($text, $position);
    if($line == $lastline) return;

    echo "\033[2K\r";
    echo $line == -1 ? "" : $text[$line]["verse"];
    $lastline = $line;
}

function displayWriteTextProcedurally($position, $text, &$lastline, &$lastposition){
    $line = currentLine($text, $position);
    if($line == $lastline) return;

    if($position < $lastposition) echo "\n----------\n\n"; // if you go backwards in the track, output lyrics break

    echo $line == -1 ? "" : $text[$line]["verse"] . "\n";
    $lastline = $line;
}

function displayRows($position, $text, &$lastline, $rownum = 5){
    $line = currentLine($text, $position);
    if($line == $lastline) return;

    if($rownum % 2 == 0) $rownum++; // if it's even add 1 so we have an odd number of total lines
    $rowsontop = ($rownum - 1) / 2; // we need to calculate rows on top of the center row, so we subtract 1 and then divide by 2
    // keep in mind that the number of rows on top will also be the number of rows at the bottom.

    echo "\033[0G"; // Set cursor to our first row
    for($i = 0; $i < $rownum; $i++) echo "\033[2K\n"; // clean up rows
    echo "\033[" . $rownum . "A"; // Set cursor up again

    echo "\033[0G"; // Set cursor to first column
    for($i = $rowsontop * -1; $i <= $rowsontop; $i++) {
        if($i === 0)
            echo ($line) < 0 || ($line) >= count($text) ? "\n" : "\033[1m" . $text[$line]["verse"]."\033[0m\n";
        else
            echo ($line + $i) < 0 || ($line + $i) >= count($text) ? "\n" : $text[$line + $i]["verse"] . "\n";
    }
    echo "\033[" . $rownum . "A"; // Set cursor up again

    $lastline = $line;
}