<?php

/*
 * TODO: Use native dbus-send
 * This means we have to parse GVariant
 * I don't know how to parse GVariant in PHP.
 * Screw it, I should have used Java or C++ to do this.
 * WHY AM I SO DUMB
 */

class PlayerCtl{
    
    private $binary;
    private $player;
    
    public function __construct(string $player = null, string $PATH = "/usr/bin"){
        $this->binary = $PATH . "/playerctl";
        if(isset($player)) $this->player = $player;
        else $this->player = $this->getPlayers()[0] ?? null;
    }

    /** @throws Exception */
    public function getPosition() : int{
        if(is_null($this->player)) throw new Exception("No music player was set!");
        return intval(exec($this->binary . " -p " . $this->player . " position 2>/dev/null")) ?? null;
    }

    /** @throws Exception */
    public function getStatus() : string{
        if(is_null($this->player)) throw new Exception("No music player was set!");
        return trim(strval(exec($this->binary . " -p " . $this->player . " status 2>/dev/null"))) ?? null;
    }

    /** @throws Exception */
    public function getArtist() : string{
        if(is_null($this->player)) throw new Exception("No music player was set!");
        return strval(exec($this->binary . " -p " . $this->player . " metadata artist 2>/dev/null")) ?? null;
    }

    /** @throws Exception */
    public function getTitle() : string{
        if(is_null($this->player)) throw new Exception("No music player was set!");
        return strval(exec($this->binary . " -p " . $this->player . " metadata title 2>/dev/null")) ?? null;
    }

    public function getPlayers() : array{
        exec($this->binary . " -l 2>/dev/null", $output);
        return $output;
    }

    public function getActivePlayer() : ?string{
        return $this->player;
    }

    public function setActivePlayer(?string $player) : void{
        $this->player = $player;
    }
}