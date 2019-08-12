<?php

namespace AryToNeX\MPRISLyrics;

class Status{

    private $player;
    private $artist;
    private $title;
    private $lyrics;
    private $lastLinePosition;
    private $lastPosition;
    private $offset;
    private $isStopped;

    public function setPlayer(?string $player) : void{
        $this->player = $player;
    }
    
    public function getPlayer() : ?string{
        return $this->player;
    }
    
    public function setTrackInfo(string $artist, string $title) : void{
        $this->setArtist($artist);
        $this->setTitle($title);
    }

    public function setArtist(string $artist) : void{
        $this->artist = $artist;
    }

    public function getArtist() : string{
        return $this->artist ?? "";
    }

    public function setTitle(string $title) : void{
        $this->title = $title;
    }

    public function getTitle() : string{
        return $this->title ?? "";
    }

    public function setLyrics(?Lyrics $lyrics) : void{
        $this->lyrics = $lyrics;
    }

    public function getLyrics() : ?Lyrics{
        return $this->lyrics;
    }

    public function setLastLinePosition(int $pos) : void{
        $this->lastLinePosition = $pos;
    }

    public function getLastLinePosition() : int{
        return $this->lastLinePosition ?? -1;
    }

    public function setLastPosition(int $pos) : void{
        $this->lastPosition = $pos;
    }

    public function getLastPosition() : int{
        return $this->lastPosition ?? 0;
    }

    public function setStopped(bool $isStopped) : void{
        $this->isStopped = $isStopped;
    }

    public function setOffset(float $offset) : void{
        $this->offset = $offset;
    }

    public function getOffset() : float{
        return $this->offset ?? 0;
    }

    public function isStopped() : bool{
        return $this->isStopped ?? true;
    }

}
