<?php

namespace apartkktrain\login;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerChatEvent;

class Main extends PluginBase implements Listener
{

  private $config;
  private $config2;
  private $config3;

    public function onEnable()
    {

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->config = new Config($this->getDataFolder() . "name.yml", Config::YAML);
        $this->config2 = new Config($this->getDataFolder() . "ip.yml", Config::YAML);
        $this->config3 = new Config($this->getDataFolder() . "uuid.yml", Config::YAML);

    }


    public function onJoin(PlayerJoinEvent $event)
    {
    	$player = $event->getPlayer();
    	$name = $event->getPlayer()->getName();

    	$ip = $player->getAddress();
    	$uuid = $player->getUniqueId();


    	if (!$this->config->exists($name)) {
    		$player->setImmobile();
    		$player->sendMessage("§a[Login] Welcome to CubicPE. Register your account with \n/register [password] and login with \n /login [password]");
    	}
    	if ($this->config->exists($name)) 
    	{
    		$myip = $this->config2->get($name);
    		$myuuid = $this->config3->get($name);

    		if ($myip === $ip&&$myuuid === $uuid) 
    		{
                
            $player->sendMessage("§a[Login] Login was successful. Welcome back!");
                
    		}else{
    			$player->sendMessage("§4[Login] Wrong Password,Try Again!");
      		    $player->setImmobile();  			
    		}
    	
    	}

    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool
    {

        if(!$sender instanceof Player)
        {
          $sender->sendMessage("§cThis comment must be entered ingame!");
          return true;
        }

        $name = $sender->getName();
        $ip = $sender->getAddress();
    	$uuid = $sender->getUniqueId();
        switch($label){

          case 'register':
         if (!$this->config->exists($name)) 
         {
         	if (!isset($args[0])) 
         	{
         		$sender->sendMessage("To Register, type /register [password]");
         	}else{
            $password = ($args[0]);
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $this->config->set($name,$hash);
            $this->config2->set($name,$ip);
            $this->config3->set($name,$uuid);
            $sender->setImmobile(false);
            $sender->sendMessage("§a[Login] Thank You for Registering!");
            $this->config->save();
            $this->config2->save();
            $this->config3->save();
            return true;
            }
          }
          case "login":
          if ($this->config->exists($name)) 
          {
          	if (!isset($args[0])) 
          	{
          		$sender->sendMessage("§4To Login, type /login [password] ");
          	}
            $hash = $this->config->get($name);
            if (password_verify($args[0], $hash)) 
            {
            	$sender->sendMessage("§a[Login] Login Was Successful!");
            	$this->config2->remove($name);
            	$this->config3->remove($name);
            	$this->config2->set($name,$ip);
            	$this->config3->set($name,$uuid);
                $this->config2->save();
                $this->config3->save();
      		    $sender->setImmobile(false);  

            }else
            {
            	$sender->sendMessage("§4Password is incorrect!");
            }
          
         }
          break;        
        }
        return true;
    }

    public function onDisable()
    {
      $this->config->save();
      $this->config2->save();
      $this->config3->save();

    }

}
