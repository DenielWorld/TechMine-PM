<?php
namespace korado531m7\InventoryMenuAPI\inventory; 

use pocketmine\block\BlockIds;

class BrewingStandInventory extends MenuInventory{
    public function __construct(){
        parent::__construct();
    }
    
    public function getDefaultSize() : int{
        return 5;
    }
    
    public function getNetworkType() : int{
        return self::BREWING_STAND;
    }
    
    public function getBlockId() : int{
        return BlockIds::BREWING_STAND_BLOCK;
    }
}