<?php

class LrcUtils{

    public static function textArr(string $rawText) : ?array{
        if(is_null($rawText)) return null;
        // remove LRC tags
        $rawText = trim(preg_replace(["(\[ti:.*\])", "(\[ar:.*\])", "(\[au:.*\])", "(\[al:.*\])", "(\[re:.*\])", "(\[ve:.*\])", "(\[by:.*\])", "(\[length:.*\])", "(\[offset:.*\])"], "", $rawText));
        // remove enhanced LRC format
        $rawText = preg_replace("(<\d{2}:\d{2}\.\d{2}> )", "", $rawText);
        $rawText = explode("\n", $rawText);
        $newText = array();
        foreach($rawText as $line){
            $time = explode(":", str_replace(".", ":", substr($line, 1, 8)));
            $time = /*($time[2] / 100) + */ $time[1] + ($time[0] * 60);
            $verse = trim(substr($line, 10));
            $newText[] = array("time" => substr($line, 1, 8), "timestamp" => $time, "verse" => $verse);
        }

        return $newText;
    }

    public static function currentLine(array $arrayText, int $position) : int{
        $line = count($arrayText) - 1; // set this to last verse to compensate the next checks
        for($i = 0; $i < count($arrayText); $i++){
            if(isset($arrayText[$i+1]["timestamp"]) && $position < $arrayText[$i+1]["timestamp"]){
                $line = $i;
                break;
            }
        }
        return $line;
    }

}