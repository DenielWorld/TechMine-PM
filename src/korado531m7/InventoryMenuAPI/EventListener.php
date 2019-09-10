<?php
namespace korado531m7\InventoryMenuAPI;

use korado531m7\InventoryMenuAPI\InventoryMenu;
use korado531m7\InventoryMenuAPI\event\InventoryClickEvent;
use korado531m7\InventoryMenuAPI\event\InventoryCloseEvent;
use korado531m7\InventoryMenuAPI\utils\InventoryMenuUtils;
use korado531m7\InventoryMenuAPI\inventory\VillagerInventory;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;

class EventListener implements Listener{
    public function __construct(){
    }
    
    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        if(InventoryMenu::isOpeningInventoryMenu($player)){
            InventoryMenu::unsetData($player);
        }
    }
    
    public function onReceive(DataPacketReceiveEvent $event){
        $pk = $event->getPacket();
        $player = $event->getPlayer();
        $tmpData = InventoryMenu::getData($player);
        if($tmpData === null) return;
        $inventory = $tmpData->getMenuInventory();
        switch(true){
            case $pk instanceof ActorEventPacket && $pk->event === ActorEventPacket::COMPLETE_TRADE:
                $tmpData->setPage($pk->data);
            break;
            
            case $pk instanceof ContainerClosePacket:
                $ev = new InventoryCloseEvent($player, $inventory, $pk->windowId);
                $ev->call();
                if($ev->isCancelled()){
                    InventoryMenuUtils::removeBlock($player, $inventory->getPosition(), $inventory->isDouble());
                    $inventory->send($player);
                }else{
                    $inventory->doClose($player);
                }
                $callable = $inventory->getCallable($inventory::CALLBACK_CLOSED);
                if($callable !== null){
                    call_user_func_array($callable, [$player, $inventory]);
                }
            break;
            
            case $pk instanceof InventoryTransactionPacket && isset($pk->actions[0]):
                $action = $pk->actions[0];
                if($inventory->isReadonly()){
                    $inventory->doClose($player);
                    $event->setCancelled();
                }
                $item = $action->oldItem->getId() === Item::AIR ? $action->newItem : $action->oldItem;
                $callable = $inventory->getCallable($inventory::CALLBACK_CLICKED);
                if($callable !== null){
                    call_user_func_array($callable, [$player, $inventory, $item]);
                }
                $ev = new InventoryClickEvent($player, $item, $pk, $inventory);
                $ev->call();
                if($inventory instanceof VillagerInventory){
                    if($tmpData->getPage() !== null){
                        $recipe = $inventory->getRecipes()[$tmpData->getPage()];
                        $player->getInventory()->addItem($recipe->getResult());
                        $player->getInventory()->removeItem($recipe->getIngredient());
                        $ig2 = $recipe->getIngredient2();
                        if($ig2 instanceof Item){
                            $player->getInventory()->removeItem($ig2);
                        }
                    }
                }
            break;
        }
    }
}