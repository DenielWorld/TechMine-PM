<?php
namespace korado531m7\InventoryMenuAPI\task;

use korado531m7\InventoryMenuAPI\InventoryMenu;
use korado531m7\InventoryMenuAPI\task\InventoryTask;
use korado531m7\InventoryMenuAPI\task\Task;
use korado531m7\InventoryMenuAPI\utils\InventoryMenuUtils;
use korado531m7\InventoryMenuAPI\utils\TemporaryData;
use korado531m7\InventoryMenuAPI\inventory\VillagerInventory;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\scheduler\Task as BaseTask;

class InventorySendTask extends BaseTask{
    private $player, $inventory;
    
    public function __construct(Player $player, TemporaryData $tmpData){
        $this->player = $player;
        $this->temp = $tmpData;
        
        $this->inventory = $tmpData->getMenuInventory();
        InventoryMenuUtils::sendFakeBlock($player, $this->inventory->getPosition(), $this->inventory->getBlock());
        if($this->inventory->isDouble())
            InventoryMenuUtils::sendPairData($player, $this->inventory->getPosition(), $this->inventory->getBlock());
        $tag = new CompoundTag();
        $tag->setString('CustomName', $this->inventory->getName());
        InventoryMenuUtils::sendTagData($player, $tag, $this->inventory->getPosition());
        $tmpData->setItems($player->getInventory()->getContents());
    }
    
    public function onRun(int $tick) : void{
        InventoryMenu::setData($this->player, $this->temp);
        if($this->inventory instanceof VillagerInventory){
            $this->inventory->setId(Entity::$entityCount++);
            $this->inventory->sendTradingData($this->player);
        }else{
            $this->player->addWindow($this->inventory);
            $task = $this->inventory->getTask();
            if($task instanceof Task){
                $scheduler = InventoryMenu::getPluginBase()->getScheduler();
                $inventoryTask = $task->getInventoryTask();
                $inventoryTask->setInventory($this->inventory);
                switch($task->getType()){
                    case Task::TASK_NORMAL:
                        $scheduler->scheduleTask($inventoryTask);
                    break;
                    
                    case Task::TASK_REPEATING:
                        $scheduler->scheduleRepeatingTask($inventoryTask, $task->getPeriod());
                    break;
                    
                    case Task::TASK_DELAYED:
                        $scheduler->scheduleDelayedTask($inventoryTask, $task->getDelay());
                    break;
                    
                    case Task::TASK_DELAYED_REPEATING:
                        $scheduler->scheduleDelayedRepeatingTask($inventoryTask, $task->getDelay(), $task->getPeriod());
                    break;
                }
            }
        }
    }
}
