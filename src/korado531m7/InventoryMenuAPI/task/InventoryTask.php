<?php
namespace korado531m7\InventoryMenuAPI\task;

use korado531m7\InventoryMenuAPI\inventory\MenuInventory;

use pocketmine\scheduler\Task;

/**
 * to set task with setTask, task class need to be inherited this.
 */
abstract class InventoryTask extends Task{    
    protected $inventory;
    
    public function __construct(){
        
    }
    
    /**
     * To access inventory, use this function
     *
     * @return MenuInventory
     */
    final public function getInventory() : MenuInventory{
        return $this->inventory;
    }
    
    /**
     * Don't call
     *
     * @param MenuInventory $inventory
     */
    final public function setInventory(MenuInventory $inventory){
        $this->inventory = $inventory;
        return $this;
    }
}
