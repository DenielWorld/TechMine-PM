<?php
namespace korado531m7\InventoryMenuAPI\inventory; 

use pocketmine\block\BlockIds;

class DropperInventory extends MenuInventory{
    public function __construct(){
        parent::__construct();
    }
    
    public function getDefaultSize() : int{
        return 9;
    }
    
    public function getNetworkType() : int{
        return self::DROPPER;
    }
    
    public function getBlockId() : int{
        return BlockIds::DROPPER;
    }
}