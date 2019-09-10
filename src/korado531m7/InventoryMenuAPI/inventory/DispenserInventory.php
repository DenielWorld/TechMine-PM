<?php
namespace korado531m7\InventoryMenuAPI\inventory; 

use pocketmine\block\BlockIds;

class DispenserInventory extends MenuInventory{
    public function __construct(){
        parent::__construct();
    }
    
    public function getDefaultSize() : int{
        return 9;
    }
    
    public function getNetworkType() : int{
        return self::DISPENSER;
    }
    
    public function getBlockId() : int{
        return BlockIds::DISPENSER;
    }
}