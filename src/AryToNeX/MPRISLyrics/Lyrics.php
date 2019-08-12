<?php

namespace AryToNeX\MPRISLyrics;

class Lyrics{
    
    private $lyrics = array();
    private $synced;
    
    public function __construct(string $lrcData, bool $accurateTiming = false){
        $encoding = Utils::detect_utf_encoding($lrcData);
        if(isset($encoding)) $lrcData = mb_convert_encoding($lrcData, "UTF-8", $encoding);
        $this->parseLrc($this->sanitizeLrc($lrcData), $accurateTiming);
    }

    public function getLineIndexAt(int $position) : int{
        $line = count($this->lyrics) - 1; // set this to last verse to compensate the next checks
        for($i = 0; $i < count($this->lyrics); $i++){
            if(isset($this->lyrics[$i+1]["timestamp"]) && $position < $this->lyrics[$i+1]["timestamp"]){
                $line = $i;
                break;
            }
        }
        return $line;
    }

    public function getLineVerseAt(int $position) : string{
        $line = count($this->lyrics) - 1; // set this to last verse to compensate the next checks
        for($i = 0; $i < count($this->lyrics); $i++){
            if(isset($this->lyrics[$i+1]["timestamp"]) && $position < $this->lyrics[$i+1]["timestamp"]){
                $line = $this->lyrics[$i]["verse"];
                break;
            }
        }
        return $line;
    }
    
    public function isSynced() : bool{
        return $this->synced ?? false;
    }

    public function asArray() : array{
        return $this->lyrics;
    }

    public function asPlainText() : string{
        $str = "";
        foreach($this->lyrics as $line) $str .= $line["verse"] . PHP_EOL;
        return $str;
    }
    
    private function sanitizeLrc(string $unsanitizedLrc) : string{
        // remove LRC tags
        $unsanitizedLrc = trim(preg_replace(["(\[id:.*\])", "(\[ti:.*\])", "(\[ar:.*\])", "(\[au:.*\])", "(\[al:.*\])", "(\[re:.*\])", "(\[ve:.*\])", "(\[by:.*\])", "(\[length:.*\])", "(\[offset:.*\])"], "", $unsanitizedLrc));
        // remove enhanced LRC format
        $unsanitizedLrc = preg_replace(array("(<\d{2}:\d{2}\.\d+>)", "(<\d{2}:\d{2}>)", "(<\d+>)"), "", $unsanitizedLrc);
        // extend compressed time tags (ex. [01:30] becomes [01:30.00])
        $unsanitizedLrc = preg_replace("/\[(\d{2}):(\d{2})\]/", "[$1:$2.00]", $unsanitizedLrc);
        // extend compressed LRC (ex. [00:02.30][00:30.45]Same verse repeated two times in a song )
        $unsanitizedLrc = explode("\n", $unsanitizedLrc);
        
        // assume lyrics are in sync
        $this->synced = true;
        // make an array of ordered and synced lyrics
        $newly = array();
        foreach($unsanitizedLrc as $line){
            // detect verse
            $verse = trim(preg_replace("(\[\d{2}:\d{2}\.\d+\])", "", $line));
            // remove multiple whitespaces in verse
            $verse = preg_replace("(\s+)", " ", $verse);
            
            // detect timestamps
            preg_match_all("(\[\d{2}:\d{2}\.\d+\])", $line, $matches);
            // attach timestamps
            for($i = 0; $i < count($matches[0]); $i++){
                $newly[] = $matches[0][$i] . " " . $verse;
            }
        }
        
        // sort by timestamps
        usort($newly, function ($item1, $item2){
            preg_match("(\[\d{2}:\d{2}\.\d+\])", $item1, $match);
            $item1 = explode(":", str_replace(["[", "]"], "", str_replace(".", ":", $match[0])));
            preg_match("(\[\d{2}:\d{2}\.\d+\])", $item2, $match);
            $item2 = explode(":", str_replace(["[", "]"], "", str_replace(".", ":", $match[0])));
            $time1 = ($item1[2] / 100) + $item1[1] + ($item1[0] * 60);
            $time2 = ($item2[2] / 100) + $item2[1] + ($item2[0] * 60);
            return $time1 <=> $time2;
        });
        
        // if lyrics are not in sync, give them to parseLrc as they are
        if(empty($newly)){
            foreach($unsanitizedLrc as $line) $newly[] = $line;
            // set synced to false
            $this->synced = false;
        }
        
        return implode("\n", $newly);
    }

    private function parseLrc(string $formattedLrc, bool $precise = false) : void{
        if($this->synced){
            $formattedLrc = explode("\n", $formattedLrc);
            $parsedLrc = array();
            foreach($formattedLrc as $line){
                preg_match("/\[(\d{2}:\d{2}\.\d+)\]/", $line, $match);
                $time = explode(":", str_replace(".", ":", $match[1]));
                if($precise) $time = ($time[2] / 100) + $time[1] + ($time[0] * 60);
                else $time = $time[1] + ($time[0] * 60);
                $verse = trim(preg_replace("(\[\d{2}:\d{2}\.\d+\])", "", $line));
                $parsedLrc[] = array("timestamp" => $time, "verse" => $verse);
            }

            $this->lyrics = $parsedLrc;
        }else{
            $this->lyrics = array(["timestamp" => 0, "verse" => $formattedLrc]);
        }
    }

}
