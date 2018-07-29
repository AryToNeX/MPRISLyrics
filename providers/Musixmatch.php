<?php

/**
 * This was a rewrite of the Musixmatch integration I found on BreadPlayer.
 * I don't even know how to fix this when it breaks, so as long as it works let's keep this, then switch to LyricWiki.
 */

class Musixmatch{

    const PROVIDER_PRIORITY = 1;

    const URL = "https://apic-desktop.musixmatch.com/ws/1.1/macro.subtitles.get?format=json&q_track={title}&q_artist={artist}&user_language=en&f_subtitle_length=0&f_subtitle_length_max_deviation=0&subtitle_format=lrc&app_id=web-desktop-app-v1.0&guid=e08e6c63-edd1-4207-86dc-d350cdf7f4bc&usertoken=1710144894f79b194e5a5866d9e084d48f227d257dcd8438261277";

    /** @var OfflineHelper $offlineHelper */
    private $offlineHelper;

    public function setOfflineHelper($offlineHelper){
        $this->offlineHelper = $offlineHelper;
    }

    public function fetchLyrics($artist, $title){
        $url = str_replace(["{title}", "{artist}"], [urlencode($title), urlencode($artist)], self::URL);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "Connection: keep-alive",
            "Cookie: x-mxm-user-id=, x-mxm-token-guid=e08e6c63-edd1-4207-86dc-d350cdf7f4bc, mxm-encrypted-token=, AWSELB=55578B011601B1EF8BC274C33F9043CA947F99DCFF6AB1B746DBF1E96A6F2B997493EE03F2DD5F516C3BC8E8DE7FE9C81FF414E8E76CF57330A3F26A0D86825F74794F3C94",
            "Cache-Control: no-cache",
            "Accept: text/html,application/xhtml+xml,application/xml,q=0.9,image/webp,image/apng,*/*,q=0.8",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0, Win64, x64 AppleWebKit/537.36 (KHTML, like Gecko Chrome/61.0.3163.91 Safari/537.36",
            "Upgrade-Insecure-Requests: 1",
            "Accept-Language: en-US,en,q=0.8",
            "Accept-Encoding: gzip, deflate",
            "dnt: 1",
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = gzdecode(curl_exec($ch));
        curl_close($ch);

        $lyrics = json_decode($result, true)["message"]["body"]["macro_calls"]["track.subtitles.get"]["message"]["body"]["subtitle_list"][0]["subtitle"]["subtitle_body"] ?? null;

        if(is_string($lyrics) && $lyrics !== ""){
            if(isset($this->offlineHelper))
                $this->offlineHelper->saveLyrics($artist, $title, $lyrics);
            return $lyrics;
        }

        return null;
    }

}