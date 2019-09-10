<?php
namespace korado531m7\InventoryMenuAPI\task;

class Task{
    const TASK_NORMAL = 0;
    const TASK_REPEATING = 1;
    const TASK_DELAYED = 2;
    const TASK_DELAYED_REPEATING = 3;

    private $task;
    private $delay;
    private $period;
    private $type;
    
    public function __construct(){
        
    }
    
    public function setInventoryTask(InventoryTask $task){
        $this->task = $task;
    }
    
    public function setDelay(int $tick) : Task{
        $this->delay = $tick;
        return $this;
    }
    
    public function setPeriod(int $period) : Task{
        $this->period = $period;
        return $this;
    }
    
    public function setType(int $type) : Task{
        $this->type = $type;
        return $this;
    }
    
    public function getInventoryTask() : InventoryTask{
        return $this->task;
    }
    
    public function getDelay() : int{
        return $this->delay;
    }
    
    public function getPeriod() : int{
        return $this->period;
    }
    
    public function getType() : int{
        return $this->type;
    }
}