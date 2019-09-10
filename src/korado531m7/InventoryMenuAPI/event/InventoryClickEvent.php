<?php
namespace korado531m7\InventoryMenuAPI\event;

use DenielWorld\Loader;
use korado531m7\InventoryMenuAPI\inventory\MenuInventory;

use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\NetworkInventoryAction;
use pocketmine\Player;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\item\Item;

class InventoryClickEvent extends PluginEvent{
    protected $who;
    protected $item;
    protected $inventory;
    protected $transaction;
    
    /**
     * @param Player                     $who
     * @param Item                       $item
     * @param InventoryTransactionPacket $transaction
     * @param MenuInventory          $inventory
     */
    public function __construct(Player $who, Item $item, InventoryTransactionPacket $transaction, MenuInventory $inventory){
        $this->who = $who;
        $this->item = $item;
        $this->transaction = $transaction;
        $this->inventory = $inventory;
    }

    /**
     * @return Player
     */
    public function getPlayer() : Player{
        return $this->who;
    }
    
    /**
     * @return Item
     */
    public function getItem() : Item{
        return $this->item;
    }
    
    public function getInventory() : MenuInventory{
        return $this->inventory;
    }
    
    /**
     * @return NetworkInventoryAction[]
     */
    public function getActions() : array{
        return $this->transaction->actions;
    }
    
    /**
     * @return int
     */
    public function getTransactionType() : int{
        return $this->transaction->transactionType;
    }
}