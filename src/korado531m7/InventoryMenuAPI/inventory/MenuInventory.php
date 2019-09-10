<?php
namespace korado531m7\InventoryMenuAPI\inventory; 

use korado531m7\InventoryMenuAPI\InventoryMenu;
use korado531m7\InventoryMenuAPI\task\InventorySendTask;
use korado531m7\InventoryMenuAPI\task\Task;
use korado531m7\InventoryMenuAPI\utils\InventoryMenuUtils;
use korado531m7\InventoryMenuAPI\utils\TemporaryData;

use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\inventory\BaseInventory;
use pocketmine\inventory\ContainerInventory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

abstract class MenuInventory extends ContainerInventory implements WindowTypes{
    const CALLBACK_CLICKED = 0;
    const CALLBACK_CLOSED = 1;
    
    protected $position;
    
    private $task = null;
    
    private $closeCallable = null;
    private $clickCallable = null;
    
    private $readonly = true;
    
    public function __construct(){
        BaseInventory::__construct([], 0, '');
        $this->setName($this->getBlock()->getName());
        $this->setSize($this->getDefaultSize());
    }
    
    /**
     * Set name to inventory
     *
     * @param string $title
     */
    public function setName(string $title){
        $this->title = $title;
    }
    
    /**
     * Will be called when player clicked item
     *
     * @param callable       $callable
     * @param int            $type
     * 
     * @return MenuInventory
     */
    public function setCallable(callable $callable, int $type = self::CALLBACK_CLICKED){
        if($type === self::CALLBACK_CLICKED)
            $this->clickCallable = $callable;
        elseif($type === self::CALLBACK_CLOSED)
            $this->closeCallable = $callable;
        
        return $this;
    }
    
    /**
     * @param Task $task
     */
    public function setTask(Task $task){
        $this->task = $task;
        return $this;
    }
    
     /**
     * Allow to trade between player and inventory
     *
     * @param bool $value
     *
     * @return InventoryMenu
     */
    public function setReadonly(bool $value){
        $this->readonly = $value;
        return $this;
    }
    
    /**
     * This is for internal only
     */
    private function setPosition(Vector3 $pos){
        $this->holder = $pos;
    }
    
    /**
     * @return bool
     */
    public function isReadonly() : bool{
        return $this->readonly;
    }
    
    /**
     * Get callable
     *
     * @param int $type
     *
     * @return callable|null
     */
    public function getCallable(int $type) : ?callable{
        if($type === self::CALLBACK_CLICKED)
            return $this->clickCallable;
        elseif($type === self::CALLBACK_CLOSED)
            return $this->closeCallable;
    }
    
    /**
     * @return Task|null
     */
    public function getTask() : ?Task{
        return $this->task;
    }
    
    /**
     * @return Vector3
     */
    public function getPosition() : Vector3{
        return $this->holder;
    }
    
    /**
     * @return Block
     */
    public function getBlock() : Block{
        return Block::get($this->getBlockId());
    }
    
    /**
     * @return bool
     */
    public function isDouble() : bool{
        return false;
    }
    
    /**
     * @return string
     */
    public function getName() : string{
        return $this->title;
    }
    
    abstract public function getBlockId() : int;
    
    /**
     * To send inventory, use this function
     *
     * @param Player $player
     */
    public function send(Player $player){ //TODO: queue
        $inventory = clone $this;
        $pos = clone $player->floor()->add(0, 4);
        $inventory->setPosition($pos);
        $tmp = new TemporaryData();
        $tmp->setMenuInventory($inventory);
        InventoryMenu::getPluginBase()->getScheduler()->scheduleDelayedTask(new InventorySendTask($player, $tmp), 4);
    }
    
    public function doClose(Player $player) : void{
        if(!InventoryMenu::isOpeningInventoryMenu($player)) return;
        $tmpData = InventoryMenu::getData($player);
        $inventory = $tmpData->getMenuInventory();
        $task = $inventory->getTask();
        if($task instanceof Task){
            InventoryMenu::getPluginBase()->getScheduler()->cancelTask($task->getInventoryTask()->getTaskId());
        }
        $player->removeWindow($inventory);
        InventoryMenuUtils::removeBlock($player, $inventory->getPosition(), $inventory->isDouble());
        if($this->isReadonly())
            $player->getInventory()->setContents($tmpData->getItems());
        InventoryMenu::unsetData($player);
    }
}