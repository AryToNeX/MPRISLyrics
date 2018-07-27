<?php

/**
 * TODO: Use native dbus-send
 * This means we have to parse GVariant
 * I don't know how to parse GVariant in PHP.
 * Screw it, I should have used Java or C++ to do this.
 * WHY AM I SO DUMB
 */

class PlayerCtl{
    
    private $binary;
    
    public function __construct($PATH = "/usr/bin"){
        $this->binary = $PATH . "/playerctl";
    }
    
    public function getPosition(){
        return intval(exec($this->binary . " position")) ?? null;
    }

    public function getArtist(){
        return strval(exec($this->binary . " metadata artist")) ?? null;
    }

    public function getTitle(){
        return strval(exec($this->binary . " metadata title")) ?? null;
    }

}