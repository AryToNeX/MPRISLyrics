<?php

namespace AryToNeX\MPRISLyrics\providers;

class Xiami extends Provider{

    public const PROVIDER_PRIORITY = 7;

    private const URL = "http://api.xiami.com/web?v=2.0&key={query}&limit={limit}&page=1&r=search/songs&app_key=1";

    public function fetchLyrics(string $artist, string $title) : ?string{
        $query = $this->queryXiami($artist, $title);
        if(isset($query) && !empty($query) && isset($query[0])){
            if(isset($query[0]["lyric"]) && $query[0]["lyric"] !== "")
                $lyrics = file_get_contents($query[0]["lyric"]);
            else
                $lyrics = null;

            if(is_string($lyrics) && $lyrics !== ""){
                if(isset($this->offlineHelper))
                    $this->offlineHelper->saveLyrics($artist, $title, $lyrics);
                return $lyrics;
            }
        }
        return null;
    }

    private function queryXiami(string $artist, string $title) : ?array{
        $ch = curl_init(str_replace(["{query}", "{limit}"], [urlencode($artist . " " . $title), "10"], self::URL));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.75 Safari/537.36");
        curl_setopt($ch, CURLOPT_REFERER, "http://h.xiami.com/");

        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true)["data"]["songs"] ?? null;
    }

}