<?php

namespace MeTooIDK\SuperHarvest;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\scheduler\Task;
use pocketmine\player\Player;
use pocketmine\scheduler\TaskScheduler;
use MeTooIDK\SuperHarvest\setTimeoutTask;
use pocketmine\network\protocol\mcpe\PlayerSoundPacket;
use pocketmine\utils\Config;

class SHBlockBreakEvent implements Listener
{
    private $toolCantMine;
    private $blocks;
    public function __construct(private Main $plugin)
    {
    }
    
    private function check(Block $block, int $id, $onCheckedSuccess): void
    {
        
        $sides = $block->getAllSides();
        foreach ($sides as $side) {
          
            if ($side->getTypeId() === $id) {
                $onCheckedSuccess($side);
            }
        }
    }
    private function breakOnMatch($block, $wantedBlock, Player $player)
    {

        $this->check($block, $wantedBlock->getTypeId(), function (Block $matchedBlock)  use ($player, $wantedBlock) {
            // 
            $callback = function () use ($matchedBlock, $wantedBlock, $player) {
                
                $pk = new PlaySoundPacket();
                $pk->soundName = "hit.stone"; // string
                $pk->volume = 1; // numeric
                $pk->pitch = 1; // numeric
                $pk->x = $matchedBlock->getPosition()->getX(); // float
                $pk->y = $matchedBlock->getPosition()->getY(); // float
                $pk->z = $matchedBlock->getPosition()->getZ(); // float
                $player->getNetworkSession()->sendDataPacket($pk);
                $player->getWorld()->setBlock($matchedBlock->getPosition(), VanillaBlocks::AIR());
                $player->getWorld()->dropItem($matchedBlock->getPosition(), $matchedBlock->asItem());

                $this->breakOnMatch($matchedBlock, $wantedBlock, $player);
            };



            $this->plugin->getScheduler()->scheduleDelayedTask(new setTimeoutTask($callback), 1);
        });
    }
    public function onBlockBreakEvent(BlockBreakEvent $event): void
    {
        if (!$this->toolCantMine) {
            $this->toolCantMine = $this->plugin->config->get("tool_cant_mine");
         }
         if (!$this->blocks) {
            $this->blocks = $this->plugin->config->get("blocks");
         }

        $block = $event->getBlock();
        $player = $event->getPlayer();
        $id = $block->getTypeId();
        // get item type (Pickaxe,Shovel,Axe,Hoe)
        $itemInHand = $player->getInventory()->getItemInHand();
        $itemType = null;
        //  
        $itemInHandName = $itemInHand->getName();
        if (strpos($itemInHandName, "Pickaxe") !== false) {
            $itemType = "pickaxe";
        } elseif (strpos($itemInHandName, "Shovel") !== false) {
            $itemType = "shovel";
        } elseif (strpos($itemInHandName, "Axe") !== false) {
            $itemType = "axe";
        } elseif (strpos($itemInHandName, "Hoe") !== false) {
            $itemType = "hoe";
        } else {
            $itemType = null;
        }
        if ($itemType == null) return;
     
        //
        $matched = in_array($block->getName(), $this->blocks[$itemType]);
       
        // Check Tool Can Mine
        if (isset($this->toolCantMine[$itemInHandName])) {
            $cantMine = $this->toolCantMine[$itemInHandName];
            if (in_array($block->getName(), $cantMine)) {          
                $matched = false;
            }
        }
        

        if ($matched) {
            if ($this->plugin->debug) {
            }
            $this->breakOnMatch($block, $block, $player);
        }
    }
}

// Check Block CLass
