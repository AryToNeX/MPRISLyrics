<?php

class Options{

    private $options;

    public function __construct(array $options){
        $optline = "";
        foreach($options as $option => $priority){
            switch ($priority){
                case "required":
                    $optline .= $option . ":";
                    break;
                case "optional":
                    $optline .= $option . "::";
                    break;
                case "novalue":
                    $optline .= $option;
                    break;
            }
        }
        $this->options = getopt($optline);
    }

    public function getOption(string $option){
        if(!isset($this->options[$option])) return null;
        if($this->options[$option] === false) return true; // WHY DOES GETOPT RETURN FALSE ON NO-VALUE OPTIONS?!
        return $this->options[$option];
    }

}