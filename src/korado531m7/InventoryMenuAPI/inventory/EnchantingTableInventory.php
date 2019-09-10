<?php
namespace korado531m7\InventoryMenuAPI\inventory; 

use pocketmine\block\BlockIds;

class EnchantingTableInventory extends MenuInventory{
    public function __construct(){
        parent::__construct();
    }
    
    public function getDefaultSize() : int{
        return 5;
    }
    
    public function getNetworkType() : int{
        return self::ENCHANTMENT;
    }
    
    public function getBlockId() : int{
        return BlockIds::ENCHANTING_TABLE;
    }
}