<?php
namespace korado531m7\InventoryMenuAPI\inventory; 

use korado531m7\InventoryMenuAPI\task\Task;
use korado531m7\InventoryMenuAPI\utils\TradingRecipe;

use RuntimeException;
use pocketmine\Player;
use pocketmine\block\BlockIds;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\item\Item;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;

class VillagerInventory extends MenuInventory{
    private $recipe = [];
    private $id;
    
    public function __construct(){
        parent::__construct();
        $this->setReadonly(false);
        $this->setName('Villager Inventory');
    }
    
    public function addRecipe(TradingRecipe $recipe){
        $this->recipe[] = $recipe;
    }
    
    public function getRecipes() : array{
        return $this->recipe;
    }
    
    public function setTask(Task $task){
        throw new RuntimeException('Cannot set task to villager inventory');
    }
    
    public function getDefaultSize() : int{
        return 2;
    }
    
    public function getNetworkType() : int{
        return self::TRADING;
    }
    
    public function getBlockId() : int{
        return BlockIds::AIR;
    }
    
    public function setId(int $id){
        $this->id = $id;
    }
    
    public function getId() : int{
        return $this->id;
    }
    
    public function doClose(Player $player) : void{
        parent::doClose($player);
        $pk = new RemoveActorPacket();
        $pk->entityUniqueId = $this->getId();
        $player->dataPacket($pk);
    }
    
    private function sendVillager(Player $player){
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type = EntityIds::VILLAGER;
        $pk->position = $this->getPosition();
        $pk->motion = null;
        $pk->metadata = [
            Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, 1 << Entity::DATA_FLAG_IMMOBILE],
            Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 0]
        ];
        $player->dataPacket($pk);
    }
    
    private function getTradeRecipeTags() : array{
        $recipes = [];
        foreach($this->getRecipes() as $tradingRecipe){
            $tag = new CompoundTag();
            $tag->setTag($tradingRecipe->getIngredient()->nbtSerialize(-1, 'buyA'));
            $ing2 = $tradingRecipe->getIngredient2();
            if($ing2 instanceof Item){
                $tag->setTag($ing2->nbtSerialize(-1, 'buyB'));
            }
            $tag->setTag($tradingRecipe->getResult()->nbtSerialize(-1, 'sell'));
            $tag->setInt('maxUses', 32767);
            $tag->setInt('uses', 0);
            $tag->setByte('rewardExp', 0);
            $recipes[] = $tag;
        }
        return $recipes;
    }
    
    public function sendTradingData(Player $player){
        $this->sendVillager($player);
        $tag = new CompoundTag();
        $tag->setTag(new ListTag('Recipes', $this->getTradeRecipeTags()));
        $nbt = new NetworkLittleEndianNBTStream();
        $pk = new UpdateTradePacket;
        $pk->windowId = 2;
        $pk->tradeTier = 0;
        $pk->isV2Trading = false;
        $pk->isWilling = false;
        $pk->traderEid = $this->getId();
        $pk->playerEid = $player->getId();
        $pk->displayName = $this->getName();
        $pk->offers = $nbt->write($tag);
        $player->dataPacket($pk);
    }
}