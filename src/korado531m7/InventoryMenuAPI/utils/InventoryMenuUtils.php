<?php
namespace korado531m7\InventoryMenuAPI\utils;

use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;

class InventoryMenuUtils{
    public static function sendTagData(Player $player, CompoundTag $tag, Vector3 $pos){
        $writer = new NetworkLittleEndianNBTStream();
        $pk = new BlockActorDataPacket;
        $pk->x = $pos->x;
        $pk->y = $pos->y;
        $pk->z = $pos->z;
        $pk->namedtag = $writer->write($tag);
        $player->dataPacket($pk);
    }
    
    public static function sendPairData(Player $player, Vector3 $pos, Block $block){
        self::sendFakeBlock($player, $pos->add(1), $block);
        $tag = new CompoundTag();
        $tag->setInt('pairx', $pos->x);
        $tag->setInt('pairz', $pos->z);
        self::sendTagData($player, $tag, $pos->add(1));
    }
    
    public static function sendFakeBlock(Player $player, Vector3 $pos, Block $block){
        $pk = new UpdateBlockPacket();
        $pk->x = (int) $pos->x;
        $pk->y = (int) $pos->y;
        $pk->z = (int) $pos->z;
        $pk->flags = UpdateBlockPacket::FLAG_ALL;
        $pk->blockRuntimeId = $block->getRuntimeId();
        $player->dataPacket($pk);
    }
    
    public static function removeBlock(Player $player, Vector3 $pos, bool $double = false){
        self::sendFakeBlock($player, $pos, Block::get(Block::AIR));
        if($double)
            self::sendFakeBlock($player, $pos->add(1), Block::get(Block::AIR));
    }
}