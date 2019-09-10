<?php
namespace korado531m7\InventoryMenuAPI\inventory; 

use pocketmine\block\BlockIds;

class ChestInventory extends MenuInventory{
    public function __construct(){
        parent::__construct();
    }
    
    public function getDefaultSize() : int{
        return 27;
    }
    
    public function getNetworkType() : int{
        return self::CONTAINER;
    }
    
    public function getBlockId() : int{
        return BlockIds::CHEST;
    }
}