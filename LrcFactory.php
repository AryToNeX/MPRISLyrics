<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 28/07/18
 * Time: 2.57
 */

class LrcFactory{

    private $providers = array();

    public function __construct($offlineHelper){

        // constitute informations of various providers
        $providersInfo = array();
        foreach(glob("providers/*.php") as $providerClass){
            $className = pathinfo($providerClass, PATHINFO_FILENAME);
            $providersInfo[] = array(
                "name" => $className,
                "priority" => constant("$className::PROVIDER_PRIORITY")
            );
        }

        // sort informations by priority
        usort($providersInfo, function ($item1, $item2) {
            return $item2['priority'] <=> $item1['priority'];
        });

        // instantiate all providers
        foreach($providersInfo as $provider){
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