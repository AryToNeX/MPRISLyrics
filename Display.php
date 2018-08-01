<?php

class Display{

    public static function displaySingleLine(int $position, Lyrics $lyrics, int &$lastline) : void{
        $line = $lyrics->getLineVerseAt($position);
        $linepos = $lyrics->getLineIndexAt($position);
        if($line == $lastline) return;

        echo "\033[2K\r";
        echo $line;
        $lastline = $linepos;
    }

    public static function displayWriteTextProcedurally(int $position, Lyrics $lyrics, int &$lastline, int &$lastposition) : void{
        $line = $lyrics->getLineVerseAt($position);
        $linepos = $lyrics->getLineIndexAt($position);
        if($linepos == $lastline) return;

        if($position < $lastposition) echo "\n----------\n\n"; // if you go backwards in the track, output lyrics break

        echo $line . "\n";
        $lastline = $linepos;
    }

    public static function displayRows(int $position, Lyrics $lyrics, int &$lastline, int $rownum = 5) : void{
        $text = $lyrics->asArray();
        $linepos = $lyrics->getLineIndexAt($position);
        if($linepos == $lastline) return;

        if($rownum % 2 == 0) $rownum++; // if it's even add 1 so we have an odd number of total lines
        $rowsontop = ($rownum - 1) / 2; // we need to calculate rows on top of the center row, so we subtract 1 and then divide by 2
        // keep in mind that the number of rows on top will also be the number of rows at the bottom.

        echo "\033[0G"; // Set cursor to our first row
        for($i = 0; $i < $rownum; $i++) echo "\033[2K\n"; // clean up rows
        echo "\033[" . $rownum . "A"; // Set cursor up again

        echo "\033[0G"; // Set cursor to first column
        for($i = $rowsontop * -1; $i <= $rowsontop; $i++) {
            if($i === 0)
                echo ($linepos) < 0 || ($linepos) >= count($text) ? "\n" : "\033[1m" . $text[$linepos]["verse"]."\033[0m\n";
            else
                echo ($linepos + $i) < 0 || ($linepos + $i) >= count($text) ? "\n" : $text[$linepos + $i]["verse"] . "\n";
        }
        echo "\033[" . $rownum . "A"; // Set cursor up again

        $lastline = $linepos;
    }

}