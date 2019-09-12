<?php

namespace DenielWorld\TechMinePM\managers;

use DenielWorld\TechMinePM\Main;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\level\Level;
use pocketmine\level\particle\RedstoneParticle;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use CoalGenerator;

class BlockManager{

    private $type;

    private $plugin;

    private const MACHINES = [BlockIds::COMMAND_BLOCK/*Coal Generator*/];

    public function __construct(Main $plugin, string $type = "BlockManager")
    {
        $this->type = "BlockManager";
        $this->plugin = $plugin;
    }

    public function init(){
        BlockFactory::registerBlock(new CoalGenerator($this->plugin), true);
    }

    //todo make this return an array of blocks if a junction is met
    public function getConnectedWires(Block $block, Vector3 $pos) : Block{//returns where the connected wires lead lel
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

    public function powerWire(Block $block) : void{//releases a redstone particle showing the activeness of the wire
        //todo figure out how long lifetime stuff is
        $particle = new RedstoneParticle($block->asVector3(), 10);
        $block->getLevel()->addParticle($particle);
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
            return null;//maybe have this as an empty array for consistency?
        }
    }

    //He would be alive if not for bulka \/
    /*public function getWireCount(Block $from_wire, Block $block, Vector3 $pos){
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
        return count($all_wires);
    }*/

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

    public function hasJunction(Block $block){
        /*todo when a junction is connected, that info should be saved in config with the data of the connected block, to check if there is any junction provided. This is only an option*/
    }

    //Similar to the normal setBlockAt method, but featuring an automatic machine registration into the config
    //in other words to set a block somewhere to a functional machine, you are required to use this function
    public function setBlockAt(int $x, int $y, int $z, string $level, Block $block, int $meta = 0){
        $machine_data = new Config($this->plugin->getDataFolder() . "machine_data.yml", Config::YAML);
        if($this->plugin->getServer()->getLevelByName($level) instanceof Level){
            if($block->getId() == Block::COMMAND_BLOCK) {
                $this->plugin->getServer()->getLevelByName($level)->setBlockIdAt($x, $y, $z, $block->getId());
                $this->plugin->getServer()->getLevelByName($level)->setBlockDataAt($x, $y, $z, $meta);
                $nested_value = "machines." . $x . ":" . $y . ":" . $z . ":" . $level;
                $machine_data->setNested($nested_value, "Coal Generator");
                $machine_data->setNested($nested_value . ".stored-energy", 0);
                $machine_data->setNested($nested_value . ".stored-coal", 0);
                $machine_data->setNested($nested_value . ".transfer-speed", 10);//probs wont be used for a good while but lets just pick up a spot for it for now
                $machine_data->save();
            }
        }//todo remove the nested config at location if exists at the placing position
    }

    public function getType()
    {
        return "BlockManager";
    }
}