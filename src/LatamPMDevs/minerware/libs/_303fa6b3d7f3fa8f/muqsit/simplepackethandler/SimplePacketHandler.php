<?php

declare(strict_types=1);

namespace LatamPMDevs\minerware\libs\_303fa6b3d7f3fa8f\muqsit\simplepackethandler;

use InvalidArgumentException;
use LatamPMDevs\minerware\libs\_303fa6b3d7f3fa8f\muqsit\simplepackethandler\interceptor\IPacketInterceptor;
use LatamPMDevs\minerware\libs\_303fa6b3d7f3fa8f\muqsit\simplepackethandler\interceptor\PacketInterceptor;
use LatamPMDevs\minerware\libs\_303fa6b3d7f3fa8f\muqsit\simplepackethandler\monitor\IPacketMonitor;
use LatamPMDevs\minerware\libs\_303fa6b3d7f3fa8f\muqsit\simplepackethandler\monitor\PacketMonitor;
use pocketmine\event\EventPriority;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\plugin\Plugin;

final class SimplePacketHandler{

	public static function createInterceptor(Plugin $registerer, int $priority = EventPriority::NORMAL, bool $handle_cancelled = false) : IPacketInterceptor{
		if($priority === EventPriority::MONITOR){
			throw new InvalidArgumentException("Cannot intercept packets at MONITOR priority");
		}
		return new PacketInterceptor($registerer, PacketPool::getInstance(), $priority, $handle_cancelled);
	}

	public static function createMonitor(Plugin $registerer, bool $handle_cancelled = false) : IPacketMonitor{
		return new PacketMonitor($registerer, PacketPool::getInstance(), $handle_cancelled);
	}
}