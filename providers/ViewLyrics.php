<?php

class ViewLyrics extends Provider{

    public const PROVIDER_PRIORITY = 49;
    private const URL = "http://search.crintsoft.com/searchlyrics.htm";
    private const SERVER_URL = "http://minilyrics.com/";
    private const QUERY = "<?xml version='1.0' encoding='utf-8' standalone='yes' ?><searchV1 client=\"ViewLyricsOpenSearcher\" artist=\"{artist}\" title=\"{title}\" OnlyMatched=\"1\" />";

    public function fetchLyrics(string $artist, string $title) : ?string{
        $ly = $this->queryViewLyrics($artist, $title)["fileinfo"];
        foreach($ly as $item) {
            if(pathinfo($item["@attributes"]["link"], PATHINFO_EXTENSION) == "lrc") {
                $lyrics = file_get_contents(self::SERVER_URL . $item["@attributes"]["link"]);

                if(is_string($lyrics) && $lyrics !== "") {
                    $this->offlineHelper->saveLyrics($artist, $title, $lyrics);
                }

                return $lyrics;
            }
        }

        return null;
    }

    private function queryViewLyrics(string $artist, string $title) : ?array{
        $ch = curl_init(self::URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "MiniLyrics");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Connection: Keep-Alive',
            'Expect: 100-continue',
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            $this->encode(
                str_replace(["{artist}", "{title}"], [$artist, $title], self::QUERY),
                "Mlv1clt4.0"
            )
        );
        $result = curl_exec($ch);
        curl_close($ch);

        if(isset($result)) return json_decode(json_encode(new SimpleXMLElement($this->decode($result))), true);
        return null;
    }

    private function encode(string $data, string $md5_extra) : string{
        $hash = pack("H*", md5($data . $md5_extra));
        $j = 0;
        for($i = 0; $i < strlen($data); $i++)
            $j += ord($data[$i]);

        $key = chr(round($j / strlen($data)));
        $enc = $data;
        for($i = 0; $i < strlen($data); $i++)
            $enc[$i] = ($data[$i] ^ $key);

        return "\x02" . $key . "\x04\x00\x00\x00" . $hash . $enc;
    }

    private function decode(string $data) : string{
        $key = $data[1];
        $result = "";
        for($i = 22; $i < strlen($data); $i++)
            $result .= ($data[$i] ^ $key);
        return $result;
    }

}