<?php

namespace DenielWorld\TechMinePM;

use korado531m7\InventoryMenuAPI\inventory\HopperInventory;
use korado531m7\InventoryMenuAPI\task\Task;
use pocketmine\block\BlockIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\utils\Config;

class EventListener implements Listener{

    private $plugin;

    //private const COAL_GENERATOR = BlockIds::COMMAND_BLOCK;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    /*public function onTest(PlayerInteractEvent $event){//todo remove
        var_dump($this->plugin->getBlockManager()->getWireCount($event->getBlock()->getLevel()->getBlockAt($event->getBlock()->x, $event->getBlock()->y, $event->getBlock()->y), $event->getBlock(), $event->getBlock()->asVector3()));
    }*/

    public function onPlace(BlockPlaceEvent $event){
        $block = $event->getBlock();
        if($block->getId() == self::COAL_GENERATOR){
            $machine_data = new Config($this->plugin->getDataFolder() . "machine_data.yml", Config::YAML);
            $nested_value = "machines." . $block->x . ":" . $block->y . ":" . $block->z . ":" . $block->getLevel()->getName();
            $machine_data->setNested($nested_value, "Coal Generator");
            $machine_data->setNested($nested_value . ".stored-energy", 0);
            $machine_data->setNested($nested_value . ".stored-coal", 0);
            $machine_data->setNested($nested_value . ".transfer-speed", 10);//probs wont be used for a good while but lets just pick up a spot for it for now
            $machine_data->save();
        }
    }

    public function onBreak(BlockBreakEvent $event){
        $block = $event->getBlock();
        if($block->getId() == 137){
            $machine_data = new Config($this->plugin->getDataFolder() . "machine_data.yml", Config::YAML);
            $nested_value = "machines." . $block->x . ":" . $block->y . ":" . $block->z . ":" . $block->getLevel()->getName();
            if($machine_data->exists($nested_value)){
                $machine_data->remove($nested_value);
                $machine_data->save();
            }
        }
    }

    public function onClick(PlayerInteractEvent $event){
        $block = $event->getBlock();
        $machine_data = new Config($this->plugin->getDataFolder() . "machine_data.yml", Config::YAML);
        $nested_value = $machine_data->getNested("machines." . $block->x . ":" . $block->y . ":" . $block->z . ":" . $block->getLevel()->getName() . ".stored-energy");
        if($event->getBlock()->getId() == self::COAL_GENERATOR and !$event->getPlayer()->isSneaking()){
            $coal_inv = new HopperInventory();
            $coal_inv->setName("Coal Generator");
            $coal_inv->setContents([1 => Item::get(Item::GLASS_PANE, 4, 1)->setCustomName("<= Battery Slot"),
                                    2 => Item::get(465, 0, 1)->setCustomName("Stored Energy: " . $nested_value),
                                    3 => Item::get(Item::GLASS_PANE, 4, 1)->setCustomName("Coal Slot =>")
                                  ]);
            //$task = new Task(); $task->setType(1); $task->setPeriod(20/*probably in ticks*/); $task->setInventoryTask();
            //$coal_inv->setTask($task);
            //todo finish this ltr ^
            $coal_inv->send($event->getPlayer());
        }
    }

    public function onTransaction(InventoryTransactionEvent $event){
        foreach ($event->getTransaction()->getInventories() as $inventory){
            if($inventory->getName() == "Coal Generator"){
                foreach($event->getTransaction()->getActions() as $action){
                    if($action->getTargetItem() === Item::get(Item::GLASS_PANE) or $action->getSourceItem() === Item::get(Item::GLASS_PANE) or $action->getSourceItem()->getId() === Item::get(465) or $action->getTargetItem()->getId() === Item::get(465)){
                        $event->setCancelled();
                    }
                }
            }
        }
    }
}