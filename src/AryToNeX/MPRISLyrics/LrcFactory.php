<?php

namespace AryToNeX\MPRISLyrics;

class LrcFactory{

    private $providers = array();

    public function __construct(OfflineHelper $offlineHelper){
        // constitute information of various providers
        $providersInfo = array();
        foreach(scandir(__DIR__ . "/providers/") as $providerClass){
            $className = pathinfo($providerClass, PATHINFO_FILENAME);
            try {
                $reflectionClass = new \ReflectionClass("AryToNeX\MPRISLyrics\providers\\" . $className);
            } catch (\ReflectionException $e){
                continue;
            }
            $providersInfo[] = array(
                "name" => "AryToNeX\MPRISLyrics\providers\\" . $className,
                "priority" => $reflectionClass->getConstant("PROVIDER_PRIORITY"),
                "is_abstract" => $reflectionClass->isAbstract()
            );
        }

        // sort information by priority
        usort($providersInfo, function ($item1, $item2) {
            return $item2['priority'] <=> $item1['priority'];
        });

        // instantiate all providers
        foreach($providersInfo as $provider){
            if($provider["is_abstract"]) continue; // don't instantiate abstract classes
            $obj = new $provider["name"];
            if(method_exists($obj, "setOfflineHelper"))
                $obj->setOfflineHelper($offlineHelper);
            $this->providers[] = $obj;
        }
    }

    public function fetchLyrics(string $artist, string $title, bool $accurateTiming = false) : ?Lyrics{
        foreach($this->providers as $provider){
            $name = get_class($provider);
            $name = ( ($pos = strrpos($name, "\\")) ? substr($name, $pos + 1) : $pos );
            echo "Trying " . $name . "...";
            $response = $provider->fetchLyrics($artist, $title);
            echo "\033[2K\r";
            if(isset($response) && $response !== ""){
                $lyrics = new Lyrics($response, $accurateTiming);
                return $lyrics;
            }
        }
        return null;
    }

}
