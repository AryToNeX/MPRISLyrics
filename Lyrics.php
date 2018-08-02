<?php

class Lyrics{
    
    private $lyrics = array();
    
    public function __construct(string $lrcData){
        $this->parseLrc($this->sanitizeLrc($lrcData));
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

    public function asArray(){
        return $this->lyrics;
    }

    public function asPlainText(){
        $str = "";
        foreach($this->lyrics as $line) $str .= $line["verse"] . PHP_EOL;
        return $str;
    }
    
    private function sanitizeLrc(string $unsanitizedLrc) : string{
        // remove LRC tags
        $unsanitizedLrc = trim(preg_replace(["(\[id:.*\])", "(\[ti:.*\])", "(\[ar:.*\])", "(\[au:.*\])", "(\[al:.*\])", "(\[re:.*\])", "(\[ve:.*\])", "(\[by:.*\])", "(\[length:.*\])", "(\[offset:.*\])"], "", $unsanitizedLrc));
        // remove enhanced LRC format
        $unsanitizedLrc = preg_replace("(<\d{2}:\d{2}\.\d{2}>)", "", $unsanitizedLrc);
        // extend compressed LRC (ex. [00:02.30][00:30.45]Same verse repeated two times in a song )
        $unsanitizedLrc = explode("\n", $unsanitizedLrc);
        $newly = array();
        foreach($unsanitizedLrc as $line){
            $verse = trim(preg_replace("(\[\d{2}:\d{2}\.\d{2}\])", "", $line));
            // remove multiple whitespaces in verse
            $verse = preg_replace("(\s+)", " ", $verse);
            
            preg_match_all("(\[\d{2}:\d{2}\.\d{2}\])", $line, $matches);
            for($i = 0; $i < count($matches[0]); $i++){
                $newly[] = $matches[0][$i] . " " . $verse;
            }
        }
        
        usort($newly, function ($item1, $item2){
            preg_match("(\[\d{2}:\d{2}\.\d{2}\])", $item1, $match);
            $item1 = explode(":", str_replace(["[", "]"], "", str_replace(".", ":", $match[0])));
            preg_match("(\[\d{2}:\d{2}\.\d{2}\])", $item2, $match);
            $item2 = explode(":", str_replace(["[", "]"], "", str_replace(".", ":", $match[0])));
            $time1 = ($item1[2] / 100) + $item1[1] + ($item1[0] * 60);
            $time2 = ($item2[2] / 100) + $item2[1] + ($item2[0] * 60);
            return $time1 <=> $time2;
        });
        
        return implode("\n", $newly);
    }

    private function parseLrc(string $formattedLrc, bool $precise = false) : void{
        $formattedLrc = explode("\n", $formattedLrc);
        $parsedLrc = array();
        foreach($formattedLrc as $line){
            $time = explode(":", str_replace(".", ":", substr($line, 1, 8)));
            if($precise) $time = ($time[2] / 100) + $time[1] + ($time[0] * 60);
            else $time = $time[1] + ($time[0] * 60);
            $verse = trim(substr($line, 10));
            $parsedLrc[] = array("timestamp" => $time, "verse" => $verse);
        }

        $this->lyrics = $parsedLrc;
    }

}