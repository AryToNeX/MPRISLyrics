<?php

class LrcFactory{

    private $providers = array();

    public function __construct(OfflineHelper $offlineHelper){

        // constitute informations of various providers
        $providersInfo = array();
        foreach(glob("providers/*.php") as $providerClass){
            $className = pathinfo($providerClass, PATHINFO_FILENAME);
            try {
                $isAbstract = (new ReflectionClass($className))->isAbstract();
            } catch (ReflectionException $e){
                $isAbstract = true; // we don't know so we assume it's abstract for security purposes
            }
            $providersInfo[] = array(
                "name" => $className,
                "priority" => constant("$className::PROVIDER_PRIORITY"),
                "is_abstract" => $isAbstract
            );
        }

        // sort informations by priority
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

    public function fetchLyrics(string $artist, string $title) : ?Lyrics{
        foreach($this->providers as $provider){
            echo "Trying " . get_class($provider) . "...";
            $response = $provider->fetchLyrics($artist, $title);
            echo "\033[2K\r";
            if(isset($response) && $response !== ""){
                $lyrics = new Lyrics($response);
                return $lyrics;
            }
        }
        return null;
    }

}