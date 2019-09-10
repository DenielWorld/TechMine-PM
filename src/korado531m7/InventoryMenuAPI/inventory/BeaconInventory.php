<?php
namespace korado531m7\InventoryMenuAPI\inventory; 

use pocketmine\block\BlockIds;

class BeaconInventory extends MenuInventory{
    public function __construct(){
        parent::__construct();
    }
    
    public function getDefaultSize() : int{
        return 1;
    }
    
    public function getNetworkType() : int{
        return self::BEACON;
    }
    
    public function getBlockId() : int{
        return BlockIds::BEACON;
    }
}