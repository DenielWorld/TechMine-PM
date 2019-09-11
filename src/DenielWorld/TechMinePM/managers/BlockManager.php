<?php

namespace DenielWorld\TechMinePM\managers;

use DenielWorld\TechMinePM\Main;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\math\Vector3;

class BlockManager{

    private $type;

    private $plugin;

    private const MACHINES = [BlockIds::COMMAND_BLOCK/*Coal Generator*/];

    public function __construct(Main $plugin, string $type = "BlockManager")
    {
        $this->type = "BlockManager";
        $this->plugin = $plugin;
    }

    public function getConnectedWires(Block $block, Vector3 $pos) : Block{//returns where the connected wires lead lel
        //todo have this return an array of connected wires so the wires can connect to multiple machines
        $wires = [$block->getLevel()->getBlockAt($pos->x, $pos->y - 1, $pos->z),
                    $block->getLevel()->getBlockAt($pos->x, $pos->y + 1, $pos->z),
                    $block->getLevel()->getBlockAt($pos->x - 1, $pos->y, $pos->z),
                    $block->getLevel()->getBlockAt($pos->x + 1, $pos->y, $pos->z),
                    $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z - 1),
                    $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z + 1)
                 ];
        //[below block, above block, to the left from block, to the right from block, upper from block, below from block]
        foreach ($wires as $wire){
            if($wire->getId() == Block::END_ROD and $this->hasConnectedWires($wire, $wire->asVector3())){
                $this->getConnectedWires($wire, $wire->asVector3());//creates a loop until the wire chain reaches a final wire destination
            }
            elseif($wire->getId() == Block::END_ROD){
                return $wire;//when the final destination is reached the block for the destination is returned
            }
        }
        return $block;//if there is nothing connected then the block that was passed on as argument 1 is returned back
    }

    public function getConnectedMachines(Block $block, array $return_machines = []){
        $pos = $block->asVector3();
        $machines = [$block->getLevel()->getBlockAt($pos->x, $pos->y - 1, $pos->z),
            $block->getLevel()->getBlockAt($pos->x, $pos->y + 1, $pos->z),
            $block->getLevel()->getBlockAt($pos->x - 1, $pos->y, $pos->z),
            $block->getLevel()->getBlockAt($pos->x + 1, $pos->y, $pos->z),
            $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z - 1),
            $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z + 1)
        ];
        foreach ($machines as $machine){
            if($this->isMachine($machine->getId())){
                array_push($return_machines, $machine);
            }
        }
        if(!empty($return_machines)){
            return $return_machines;
        }
        else {
            return null;
        }
    }

    public function getWireCount(Block $from_wire, Block $block, Vector3 $pos){
        $all_wires = [];
        $wires = [$block->getLevel()->getBlockAt($pos->x, $pos->y - 1, $pos->z),
            $block->getLevel()->getBlockAt($pos->x, $pos->y + 1, $pos->z),
            $block->getLevel()->getBlockAt($pos->x - 1, $pos->y, $pos->z),
            $block->getLevel()->getBlockAt($pos->x + 1, $pos->y, $pos->z),
            $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z - 1),
            $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z + 1)
        ];
        foreach ($wires as $wire){
            if($wire->asVector3() !== $from_wire->asVector3() and $wire->getId() == Block::END_ROD){
                array_push($all_wires, $wire);
            }
        }
        var_dump(count($all_wires));//testing todo remove
        return count($all_wires);
    }

    public function hasConnectedWires(Block $block, Vector3 $pos) : bool{//todo get rid of the vector 3, technically useless
        $wires = [$block->getLevel()->getBlockAt($pos->x, $pos->y - 1, $pos->z),
            $block->getLevel()->getBlockAt($pos->x, $pos->y + 1, $pos->z),
            $block->getLevel()->getBlockAt($pos->x - 1, $pos->y, $pos->z),
            $block->getLevel()->getBlockAt($pos->x + 1, $pos->y, $pos->z),
            $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z - 1),
            $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z + 1)
        ];
        foreach ($wires as $wire){
            if($wire->getId() == Block::END_ROD){
                return true;
            }
        }
        return false;
    }

    public function hasConnectedMachines(Block $block, Vector3 $pos) : bool{
        $machines = [$block->getLevel()->getBlockAt($pos->x, $pos->y - 1, $pos->z),
            $block->getLevel()->getBlockAt($pos->x, $pos->y + 1, $pos->z),
            $block->getLevel()->getBlockAt($pos->x - 1, $pos->y, $pos->z),
            $block->getLevel()->getBlockAt($pos->x + 1, $pos->y, $pos->z),
            $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z - 1),
            $block->getLevel()->getBlockAt($pos->x, $pos->y, $pos->z + 1)
        ];
        foreach ($machines as $machine){
            if($machine->getId() == Block::COMMAND_BLOCK){
                return true;
            }
        }
        return false;
    }

    public function isMachine(BlockIds $block){//int?
        if(in_array($block, self::MACHINES)){
            return true;
        }
        return false;
    }

    public function isWire(BlockIds $block){//int?
        if($block == Block::END_ROD){
            return true;
        }
        return false;
    }

    public function hasMultipleDestinations(Block $block){

    }

    public function getType()
    {
        return "BlockManager";
    }
}