<?php

declare(strict_types=1);

namespace MeTooIDK\SuperHarvest;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;

class Main extends PluginBase
{
  public $config;
  public $debug;
  public function onEnable(): void
  {
    @mkdir($this->getDataFolder());
    $this->saveResource("config.yml");
    $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    $this->config->set("hint", "Get Block ID at https://www.digminecraft.com/lists/item_id_list_pe.php");
    if (!$this->config->get("blocks")) {
      $this->getLogger()->info(TextFormat::DARK_GREEN . "Config Files not found. Creating...");
      $this->config->set("blocks", ["Oak Log", "Birch Log", "Spruce Log", "Jungle Log", "Acaia Log", "Mangrove Log", "Cherry Log", "Crimson Stem", "Wraped Stem", "Iron Ore", "Gold Ore", "Diamond Ore", "Redstone Ore", "Lapis Lazuli Ore", "Emerald Ore", "Nether Quartz Ore", "Coal Ore", "Copper Ore", "Amethyst Ore", "Deepslate Iron Ore", "Deepslate Gold Ore", "Deepslate Diamond Ore", "Deepslate Redstone Ore", "Deepslate Lapis Lazuli Ore", "Deepslate Emerald Ore", "Deepslate Copper Ore", "Deepslate Coal Ore", "Deepslate Amethyst Ore", "Deepslate Tuff"]);
    }
    if (!$this->config->get("debug")) {
      $this->config->set("debug", true);
    }
    $this->config->save();
    if ($this->config->get("debug")) {
      $this->debug = true;
      $this->getLogger()->info(TextFormat::BLUE . "DEBUG MODE ENABLED");
      $this->getLogger()->info("Blocks: " . implode(", ", $this->config->get("blocks")));
    }
    $this->getLogger()->info(TextFormat::DARK_GREEN . "I've been enabled!");
    $this->getServer()->getPluginManager()->registerEvents(new SHBlockBreakEvent($this), $this);
  }
  public function onLoad(): void
  {
    $this->getLogger()->info(TextFormat::WHITE . " SuperHarvest Loaded");
  }
}
