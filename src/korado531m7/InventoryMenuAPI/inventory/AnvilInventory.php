<?php
namespace korado531m7\InventoryMenuAPI\inventory; 

use pocketmine\block\BlockIds;

class AnvilInventory extends MenuInventory{
    public function __construct(){
        parent::__construct();
    }
    
    public function getDefaultSize() : int{
        return 27;
    }
    
    public function getNetworkType() : int{
        return self::ANVIL;
    }
    
    public function getBlockId() : int{
        return BlockIds::ANVIL;
    }
}