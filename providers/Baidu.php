<?php

class Baidu extends Provider{

    public const PROVIDER_PRIORITY = 10;

    private const QUERY_URL = "http://tingapi.ting.baidu.com/v1/restserver/ting?from=qianqian&version=2.1.0&method=baidu.ting.search.merge&format=json&query={query}&page_no=1&page_size=5&type=-1&data_source=0&use_cluster=1";
    private const LYRICS_URL = "http://tingapi.ting.baidu.com/v1/restserver/ting?from=qianqian&version=2.1.0&method=baidu.ting.song.lry&format=json&songid={songid}";

    public function fetchLyrics(string $artist, string $title) : ?string{
        $query = $this->getSongQuery($artist, $title);
        if(isset($query) && !empty($query)){
            foreach ($query as $result){
                if($result["title"] == $title){
                    $lyrics = $this->getLyricsFromSongId($result["song_id"]);
                    if(is_string($lyrics) && $lyrics !== ""){
                        if(isset($this->offlineHelper))
                            $this->offlineHelper->saveLyrics($artist, $title, $lyrics);
                        return $lyrics;
                    }
                }
            }
        }
        return null;
    }

    private function getSongQuery(string $artist, string $title) : ?array{
        $url = str_replace("{query}", urlencode($title . " " . $artist), self::QUERY_URL);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true)["song_info"]["song_list"] ?? null;
    }

    private function getLyricsFromSongId(string $songId) : ?string{
        $url = str_replace("{songid}", urlencode($songId), self::LYRICS_URL);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true)["lrcContent"] ?? null;
    }

}