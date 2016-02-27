<?php

namespace Managon;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\math\Vector3;

class Mobtel extends PluginBase implements Listener{
  private $mobnum = 0;
  function onEnable(){
    $this->getLogger()->info("§2Mobtel　Loaded!!  §bBy Managon");
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
	if(!file_exists($this->getDataFolder())){
          @mkdir($this->getDataFolder(),0774,true);
       }
	      $this->c = new Config($this->getDataFolder(). "Mob.yml", Config::YAML, array());
	}
  function onCommand(CommandSender $sender, Command $command, $label, array $args){
    switch (strtolower($command->getName())){
		      case "tel":
			  if(!isset($args[0])){
			  $sender->sendMessage("§2Usage: /tel [worldname]");
	        }else{
			  $w_name = $args[0];
			    if(Server::getInstance()->loadLevel($w_name) !==False){
				   $this->c->set($this->mobnum,[$w_name, $sender->getName(), "off"]);
				   $this->c->set($sender->getName(),$this->mobnum);
				   $this->c->save();
				   $sender->sendMessage("§bTap mob and finish!!");
				   return $this->mobnum++;
			    }
				else{
				   $sender->sendMessage("§aI didn't find the world...");
				   return;
			  }
			}
	}
  }
  
  function onMobTouch(EntityDamageEvent $e){
    if($e instanceof EntityDamageByEntityEvent){
	   if(!$e->getEntity() instanceof Player or !$e->getEntity() instanceof Projectile){
	      $p = $e->getDamager();
	      $name = $p->getName();
	      if($this->c->exists($name) and $this->c->get($this->c->get($name))[2] == "off"){
				$world = $this->c->get($this->c->get($name))[0];
				$mob = $e->getEntity();
				$x = ceil($mob->getX());
				$y = ceil($mob->getY());
				$z = ceil($mob->getZ());
				$mix = $x + $y + $z;
				$this->c->remove($this->c->get($name));
				$this->c->save();
				$this->c->set($this->c->get($name),[$world, $name, "on", $x, $y, $z]);
				$this->c->set($mix, $world);
				$this->c->save(); 
				$p->sendMessage("§dDone!!");
				$e->setCancelled();
	   }else{
	                  $mob = $e->getEntity();
				      $x = ceil($mob->getX());
				      $y = ceil($mob->getY());
				      $z = ceil($mob->getZ());
				      $mix = $x + $y + $z;
					  if($this->c->exists($mix)){
					     $p->sendMessage("§bTeleporting...");
						 Server::getInstance()->loadLevel($this->c->get($mix));
					     $p->teleport(Server::getInstance()->getLevelByName($this->c->get($mix))->getSpawnLocation());
						 $e->setCancelled();
				   }
	   }
	}
  }
}
}
