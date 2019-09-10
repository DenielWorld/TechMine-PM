<?php
namespace korado531m7\InventoryMenuAPI;

use korado531m7\InventoryMenuAPI\inventory\MenuInventory;
use korado531m7\InventoryMenuAPI\utils\TemporaryData;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class InventoryMenu extends PluginBase implements InventoryType{
    private static $tmpInventory = [];
    private static $pluginbase = null;
    
    public function onEnable(){
        self::register($this);
        $this->getLogger()->notice('You are using this api as plugin. We recommend you to use this as virion');
    }
    
    /**
     * You need to call this function statically to use this api
     *
     * @param PluginBase $plugin
     */
    public static function register(PluginBase $plugin) : void{
        if(self::$pluginbase === null){
            self::$pluginbase = $plugin;
            $plugin->getServer()->getPluginManager()->registerEvents(new EventListener(), $plugin);
        }
    }
    
    /**
     * Create inventory instance
     *
     * @param int $type
     *
     * @return InventoryMenu
     */
    public static function createInventory(string $type = self::INVENTORY_TYPE_CHEST) : MenuInventory{
        return new $type();
    }
    
    /**
     * Check whether player is opening inventory menu
     *
     * @param  Player $player
     * @return bool
     */
    public static function isOpeningInventoryMenu(Player $player) : bool{
        return array_key_exists($player->getName(), self::$tmpInventory);
    }
    
    /**
     * this function is for internal use only. Don't call this
     */
    public static function unsetData(Player $player){
        unset(self::$tmpInventory[$player->getName()]);
    }
    
    /**
     * this function is for internal use only. Don't call this
     */
    public static function getData(Player $player) : ?TemporaryData{
        return self::$tmpInventory[$player->getName()] ?? null;
    }
    
    /**
     * this function is for internal use only. Don't call this
     */
    public static function setData(Player $player, TemporaryData $temp){
        self::$tmpInventory[$player->getName()] = $temp;
    }
    
    /**
     * this function is for internal use only. Don't call this
     */
    public static function getPluginBase() : PluginBase{
        return self::$pluginbase;
    }
}