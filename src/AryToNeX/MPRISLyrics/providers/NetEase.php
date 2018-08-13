<?php

namespace AryToNeX\MPRISLyrics\providers;

class NetEase extends Provider{

    public const PROVIDER_PRIORITY = 9;

    private const URL = "http://music.163.com/api/search/get/";
    private const LYRICS_URL = "http://music.163.com/api/song/lyric?os=osx&id={id}&lv=-1&kv=-1&tv=-1";
    private const POSTFIELDS = "s={query}&type=1&limit=10&offset=0";

    public function fetchLyrics(string $artist, string $title) : ?string{
        $query = $this->queryNetEase($artist, $title);
        if(isset($query) && !empty($query) && isset($query[0]["id"])) {
            $lyrics = $this->getLyricsFromID($query[0]["id"]);

            if(is_string($lyrics) && $lyrics !== ""){
                if(isset($this->offlineHelper))
                    $this->offlineHelper->saveLyrics($artist, $title, $lyrics);
                return $lyrics;
            }
        }
        return null;
    }

    private function queryNetEase(string $artist, string $title) : ?array{
        $ch = curl_init(self::URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.152 Safari/537.36");
        curl_setopt($ch, CURLOPT_REFERER, "http://music.163.com/search");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: music.163.com"));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, str_replace("{query}", urlencode($artist . " " . $title), self::POSTFIELDS));

        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true)["result"]["songs"] ?? null;
    }

    private function getLyricsFromID(int $id) : ?string{
        $ch = curl_init(str_replace("{id}", strval($id), self::LYRICS_URL));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.152 Safari/537.36");
        curl_setopt($ch, CURLOPT_REFERER, "http://music.163.com/search");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: music.163.com"));

        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true)["lrc"]["lyric"] ?? null;
    }
}