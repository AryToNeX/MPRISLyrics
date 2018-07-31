<?php

class LrcUtils{

    public static function textArr(?string $rawText) : ?array{
        if($rawText == "") return null;
        // remove LRC tags
        $rawText = trim(preg_replace(["(\[id:.*\])", "(\[ti:.*\])", "(\[ar:.*\])", "(\[au:.*\])", "(\[al:.*\])", "(\[re:.*\])", "(\[ve:.*\])", "(\[by:.*\])", "(\[length:.*\])", "(\[offset:.*\])"], "", $rawText));
        // remove enhanced LRC format
        $rawText = preg_replace("(<\d{2}:\d{2}\.\d{2}>)", "", $rawText);
        // format crappy LRC (when you want to save lines but I DON'T, OK?)
        $rawText = self::formatCrappyLrc($rawText);
        $rawText = explode("\n", $rawText);
        $newText = array();
        foreach($rawText as $line){
            $time = explode(":", str_replace(".", ":", substr($line, 1, 8)));
            $time = /*($time[2] / 100) + */ $time[1] + ($time[0] * 60);
            $verse = trim(substr($line, 10));
            // remove multiple whitespaces
            $verse = preg_replace("(\s+)", " ", $verse);
            $newText[] = array("time" => substr($line, 1, 8), "timestamp" => $time, "verse" => $verse);
        }

        return $newText;
    }

    public static function formatCrappyLrc(string $ly) : string{
        $ly = explode("\n", $ly);
        $newly = array();
        foreach($ly as $line){
            $verse = trim(preg_replace("(\[\d{2}:\d{2}\.\d{2}\])", "", $line));
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

        $newly = implode("\n", $newly);

        return $newly;
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