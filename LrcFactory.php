<?php

class LrcFactory{

    private $providers = array();

    public function __construct($offlineHelper){

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

    public function fetchLyrics($artist, $title){
        foreach($this->providers as $provider){
            $response = $provider->fetchLyrics($artist, $title);
            if(isset($response)) return $response;
        }
        return null;
    }

}