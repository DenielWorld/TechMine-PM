<?php

namespace DenielWorld\TechMinePM;

use DenielWorld\TechMinePM\managers\BlockManager;
use DenielWorld\TechMinePM\managers\Manager;
use DenielWorld\TechMinePM\tasks\CoalGeneratorUpdateTask;
use http\Exception\InvalidArgumentException;
use korado531m7\InventoryMenuAPI\InventoryMenu;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase{

    private $cfg;

    public function onLoad()
    {
        $machine_data = new Config($this->getDataFolder() . "machine_data.yml", Config::YAML);
        $cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->getBlockManager()->init();//registers everything machine related
        //todo craft manager
        //todo load machines function
        foreach ($machine_data->getAll() as $machine) {//I hope that a value with nested values returns an array of nested values
            if ($machine_data->get($machine) == "Coal Generator") {
                $this->getScheduler()->scheduleRepeatingTask(new CoalGeneratorUpdateTask($this, $machine), $cfg->getNested("performance.coal-generator-update"));
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
            $this->cfg = $cfg;//todo move dis to onLoad
            $cfg->save();
        }
        if(!file_exists("machine_data.yml")){
            $machine_data = new Config($this->getDataFolder() . "machine_data.yml", Config::YAML);
            $machine_data->save();
        }
        if($this->cfg->get("data-storing") !== true){
            $this->getServer()->getLogger()->warning("Unable to store any machine data, TechMinePM is being disabled");
            $this->getServer()->getPluginManager()->disablePlugin($this);
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

    //todo remove such methods, as they are pointless \/
    public function getBlockManager(){
        return new BlockManager($this);
    }
}