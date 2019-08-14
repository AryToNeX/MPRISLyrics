<?php

namespace AryToNeX\MPRISLyrics;

use AryToNeX\MPRISLyrics\Utils;

class Display{

    public static function displaySingleLine(int $position, int $maxdimens, Status $status) : void{
        $line = $status->getLyrics()->getLineVerseAt($position);
        $linepos = $status->getLyrics()->getLineIndexAt($position);
        if($linepos == $status->getLastLinePosition()) return;

        echo "\033[2K\r";
        echo Utils::ellipsis($line, $maxdimens-1);
        $status->setLastLinePosition($linepos);
    }

    public static function displayWriteTextProcedurally(int $position, int $maxdimens, Status $status) : void{
        $line = $status->getLyrics()->getLineVerseAt($position);
        $linepos = $status->getLyrics()->getLineIndexAt($position);
        if($linepos == $status->getLastLinePosition()) return;

        if($position < $status->getLastPosition()) echo "\n----------\n\n"; // if you go backwards in the track, output lyrics break

        echo wordwrap($line, $maxdimens-2, "\n ", true) . "\n";
        $status->setLastLinePosition($linepos);
    }

    public static function displayRows(int $position, int $maxdimens, Status $status, int $rownum = 5) : void{
        $text = $status->getLyrics()->asArray();
        $linepos = $status->getLyrics()->getLineIndexAt($position);
        if($linepos == $status->getLastLinePosition()) return;

        if($rownum % 2 == 0) $rownum++; // if it's even add 1 so we have an odd number of total lines
        $rowsontop = ($rownum - 1) / 2; // we need to calculate rows on top of the center row, so we subtract 1 and then divide by 2
        // keep in mind that the number of rows on top will also be the number of rows at the bottom.

        echo "\033[0G"; // Set cursor to our first row
        for($i = 0; $i < $rownum; $i++) echo "\033[2K\n"; // clean up rows
        echo "\033[" . $rownum . "A"; // Set cursor up again

        echo "\033[0G"; // Set cursor to first column
        for($i = $rowsontop * -1; $i <= $rowsontop; $i++) {
            if($i === 0)
                echo ($linepos) < 0 || ($linepos) >= count($text) ? "\n" : "\033[1m" . Utils::ellipsis($text[$linepos]["verse"], $maxdimens-1)."\033[0m\n";
            else
                echo ($linepos + $i) < 0 || ($linepos + $i) >= count($text) ? "\n" : Utils::ellipsis($text[$linepos + $i]["verse"], $maxdimens-1) . "\n";
        }
        echo "\033[" . $rownum . "A"; // Set cursor up again

        $status->setLastLinePosition($linepos);
    }

}
