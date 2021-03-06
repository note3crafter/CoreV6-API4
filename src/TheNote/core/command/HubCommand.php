<?php
//   ╔═════╗╔═╗ ╔═╗╔═════╗╔═╗    ╔═╗╔═════╗╔═════╗╔═════╗
//   ╚═╗ ╔═╝║ ║ ║ ║║ ╔═══╝║ ╚═╗  ║ ║║ ╔═╗ ║╚═╗ ╔═╝║ ╔═══╝
//     ║ ║  ║ ╚═╝ ║║ ╚══╗ ║   ╚══╣ ║║ ║ ║ ║  ║ ║  ║ ╚══╗
//     ║ ║  ║ ╔═╗ ║║ ╔══╝ ║ ╠══╗   ║║ ║ ║ ║  ║ ║  ║ ╔══╝
//     ║ ║  ║ ║ ║ ║║ ╚═══╗║ ║  ╚═╗ ║║ ╚═╝ ║  ║ ║  ║ ╚═══╗
//     ╚═╝  ╚═╝ ╚═╝╚═════╝╚═╝    ╚═╝╚═════╝  ╚═╝  ╚═════╝
//   Copyright by TheNote! Not for Resale! Not for others
//

namespace TheNote\core\command;

use pocketmine\data\java\GameModeIdMap;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\sound\EndermanTeleportSound;
use TheNote\core\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;

class HubCommand extends Command
{
	private $plugin;

	public function __construct(Main $plugin)
	{
		$this->plugin = $plugin;
		$config = new Config($this->plugin->getDataFolder() . Main::$setup . "settings" . ".json", Config::JSON);
		parent::__construct("hub", $config->get("prefix") . "Teleportiere dich zum Spawn", "/hub", ["spawn", "lobby"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool
	{
		$configs = new Config($this->plugin->getDataFolder() . Main::$setup . "settings" . ".json", Config::JSON);
		$config = new Config($this->plugin->getDataFolder() . Main::$setup . "Config" . ".yml", Config::YAML);
		if (!$sender instanceof Player) {
			$sender->sendMessage($configs->get("error") . "§cDiesen Command kannst du nur Ingame benutzen");
			return false;
		}
		if (!$this->testPermission($sender)) {
			$sender->sendMessage($configs->get("error") . "Du hast keine Berechtigung um diesen Command auszuführen!");
			return false;
		}
		$this->plugin->getServer()->getWorldManager()->loadWorld($config->get("Defaultworld"));
		$sender->teleport(Server::getInstance()->getWorldManager()->getWorldByName($config->get("Defaultworld"))->getSafeSpawn());
		$sender->setGamemode(GameMode::fromString($config->get("Gamemode")));
		if ($config->get("Food") == true) {
			$sender->getHungerManager()->setFood(20);
		}
		if ($config->get("Heal") == true) {
			$sender->setHealth(20);
		}
		if ($config->get("Teleportsound") == true) {
			$sender->getWorld()->addSound($sender->getPosition(), new EndermanTeleportSound());
		}
		$sender->sendMessage($configs->get("prefix") . "§6Du wurdest zum §eSpawn §6teleportiert");
		return true;
	}
}
