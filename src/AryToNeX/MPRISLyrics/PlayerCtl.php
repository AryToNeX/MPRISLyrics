<?php

namespace AryToNeX\MPRISLyrics;

/*
 * TODO: Use native dbus-send
 * This means we have to parse Variant
 * I don't know how to parse Variant in PHP.
 * Screw it, I should have used Java or C++ to do this.
 * WHY AM I SO DUMB
 */

class PlayerCtl{
    
    private $binary;
    private $player;
    
    public function __construct(string $PATH = "/usr/bin"){
        $this->binary = $PATH . "/playerctl";
    }

    /** @throws \Exception */
    public function getPosition() : ?float{
        if(is_null($this->player)) throw new \Exception("No music player was set!");
        return floatval(exec($this->binary . " -p " . $this->player . " position 2>/dev/null")) ?? null;
    }

    /** @throws \Exception */
    public function getStatus() : ?string{
        if(is_null($this->player)) throw new \Exception("No music player was set!");
        return trim(strval(exec($this->binary . " -p " . $this->player . " status 2>/dev/null"))) ?? null;
    }

    /** @throws \Exception */
    public function getArtist() : ?string{
        if(is_null($this->player)) throw new \Exception("No music player was set!");
        return strval(exec($this->binary . " -p " . $this->player . " metadata artist 2>/dev/null")) ?? null;
    }

    /** @throws \Exception */
    public function getTitle() : ?string{
        if(is_null($this->player)) throw new \Exception("No music player was set!");
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
