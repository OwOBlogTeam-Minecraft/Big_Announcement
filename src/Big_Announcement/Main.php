<?php

/*                             Copyright (c) 2017-2018 TeaTech All right Reserved.
 *
 *      ████████████  ██████████           ██         ████████  ██           ██████████    ██          ██
 *           ██       ██                 ██  ██       ██        ██          ██        ██   ████        ██
 *           ██       ██                ██    ██      ██        ██          ██        ██   ██  ██      ██
 *           ██       ██████████       ██      ██     ██        ██          ██        ██   ██    ██    ██
 *           ██       ██              ████████████    ██        ██          ██        ██   ██      ██  ██
 *           ██       ██             ██          ██   ██        ██          ██        ██   ██        ████
 *           ██       ██████████    ██            ██  ████████  ██████████   ██████████    ██          ██
**/

namespace Big_Announcement;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\Player;

use pocketmine\scheduler\CallbackTask;


class Main extends PluginBase implements Listener
{
	private static $instance = null;
	
	public function onLoad()
	{
		$this->pluginname = $this->getDescription()->getName();
		@define("B","§f[§b".$this->pluginname."§f]");
		self::$instance = $this;
	}
	
	public function onEnable()
	{
		$this->getServer()->getLogger()->info(B."§e加载完成!");
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		
		$this->CreateConfig();
	}
	
	public function onDisable()
	{
		$this->getServer()->getLogger()->info(B."§c取消加载.");
	}
	
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args)
	{
		if($cmd->getName() === "b")
		{
			if(\pocketmine\network\protocol\Info::CURRENT_PROTOCOL == 105)
			{
				$sender->sendMessage(B."§c仅限服务端协议版本为§e105§c使用!");
				$sender->sendMessage(B."§c你服务器的协议版本为: §6".\pocketmine\network\protocol\Info::CURRENT_PROTOCOL);
				return true;
			}
			
			if(isset($args[0]) && isset($args[1]) &&  isset($args[2]))
			{
				$sender->sendMessage(B."用法: §d/§6b §f<§e大标题§f> §f<§e中标题§f> §f<§e小标题§f>");
				return true;
			}
			
			$title = $args[0];
			$info = $args[1];
			$under = $args[2];
			
			foreach($this->getServer()->getOnlinePlayers() as $p)
			{
				$p->sendTitle($title, $info, 1, 1, 10);
				$p->sendActionBar($under, 1, 1);
			}
			
			$sender->sendMessage(B."§a发送成功!");
			$sender->sendMessage(B."§a你刚刚发送的信息依次为: ".$title." , ".$info." , ".$under);
			return true;
		}
	}
	
	
	
	public function onPlayerJoin(\pocketmine\event\player\PlayerJoinEvent $e)
	{
		$p = $e->getPlayer();
		$n = $p->getName();
		
		sleep(1);
		if(\pocketmine\network\protocol\Info::CURRENT_PROTOCOL == 105)
		{
			$title = $this->config->get("BigTitle");
			$info = $this->config->get("CentreTitle");
			$under = $this->config->get("SmallTitle");
			
			$p->sendTitle($title, $info, 1, 1, 10);
			$p->sendActionBar($under, 1, 1);
			unset($title,$info,$under);
		}
	}
	
	
	
	
	
	
	
	
	
	
	public function Announcement()
	{
		$BA = $this->automsg->getAll();
		$msg = str_replace("{Prefix}", $BA["msg"]["Prefix"], $BA["msg"]["messages"]);
		$msg = str_replace("{Time}", "§f[§e".date("H")."§f:§e".date("i")."§f:§e".date("s")."§f]", $msg);
		foreach($this->getServer()->getOnlinePlayers() as $p)
		{
			$p->sendTitle("", $this->sendAnnouncementMessage(str_replace("{PlayerName}", $p->getName(), $msg)), 1, 1, 10);
		}
		unset($BA);
	}
	
	
	
	public function CreateConfig()
	{
		if(!is_dir($this->getDataFolder())){@mkdir($this->getDataFolder(),0777,true);}
		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML, array
		(
			"BigTitle"=>"欢迎加入服务器",
			"CentreTitle"=>"可爱的玩家~",
			"SmallTitle"=>"作者Teaclon",
			"Permission"=>true
		));
		
		$this->automsg = new Config($this->getDataFolder()."automsg.yml", Config::YAML, array
		(
			"msg"=>array
			(
				"Time"=>1,
				"Prefix"=>"[小奇葩]",
				"messages"=>array
				(
					"{Time}{Prefix}自定义弹出1",
					"{Time}{Prefix}自定义弹出2",
					"{Time}{Prefix}自定义弹出3",
					"{Time}{Prefix}你可以按照这个格式无限的添加弹出的话",
					"{Time}{Prefix}不建议时间设定的很频繁",
				),
			),
		));
		
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask([$this,"Announcement"]),$this->automsg->getNested("msg.Time") * 20 * 60);
	}
	
	
	
	
	
	
    public function sendAnnouncementMessage(array $messages, $amount = 1)
	{
    	return $messages[array_rand($messages, $amount)];
    }
}








?>