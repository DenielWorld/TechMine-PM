<?php
namespace korado531m7\InventoryMenuAPI\inventory; 

use pocketmine\block\BlockIds;

class DoubleChestInventory extends MenuInventory{
    public function __construct(){
        parent::__construct();
    }
    
    public function getDefaultSize() : int{
        return 54;
    }
    
    public function getNetworkType() : int{
        return self::CONTAINER;
    }
    
    public function getBlockId() : int{
        return BlockIds::CHEST;
    }
    
    public function isDouble() : bool{
        return true;
    }
}