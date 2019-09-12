<?php

namespace DenielWorld\TechMinePM;

use DenielWorld\TechMinePM\managers\BlockManager;
use DenielWorld\TechMinePM\managers\Manager;
use http\Exception\InvalidArgumentException;
use korado531m7\InventoryMenuAPI\InventoryMenu;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase{

    private $cfg;

    public function onLoad()
    {
        $machine_data = new Config($this->getDataFolder() . "machine_data.yml", Config::YAML);
        $this->getBlockManager()->init();//registers everything machine related
        //todo load machines function
        if(is_array($machine_data->get("machines"))){
            foreach ($machine_data->get("machines") as $machine) {//I hope that a value with nested values returns an array of nested values
            //$this->getScheduler()->scheduleRepeatingTask(/*todo*/);
            }
        }
    }

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        InventoryMenu::register($this);//registering the virion to plugin
        @mkdir($this->getDataFolder());
        if(!file_exists("config.yml")){
            $cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
            $this->cfg = $cfg;
            $cfg->save();
        }
        if(!file_exists("machine_data.yml")){
            $machine_data = new Config($this->getDataFolder() . "machine_data.yml", Config::YAML);
            $machine_data->save();
        }
    }

    public function getAPI(string $plugin){//used to get other plugins APIs
        return $this->getServer()->getPluginManager()->getPlugin($plugin);
    }

    public function getManager(string $type = "BlockManager"){
        if("type" == "BlockManager"){
            return new BlockManager($this);
        }
        else {
            throw new InvalidArgumentException("Such manager does not exist");
        }
    }

    //todo remove such clases, as they are pointless \/
    public function getBlockManager(){
        return new BlockManager($this);
    }
}