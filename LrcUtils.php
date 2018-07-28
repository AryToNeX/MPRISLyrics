<?php

class LrcUtils{

    public static function textArr($rawText){
        if(is_null($rawText)) return null;
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

    public static function currentLine($rawText, $position){
        $line = count($rawText) - 1; // set this to last verse to compensate the next checks
        for($i = 0; $i < count($rawText); $i++){
            if(isset($rawText[$i+1]["timestamp"]) && $position < $rawText[$i+1]["timestamp"]){
                $line = $i;
                break;
            }
        }
        return $line;
    }

}