<?php

declare(strict_types=1);

namespace NAttreid\Console\Routing;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;
use Tracy\Debugger;

/**
 * Deploy router
 *
 * @author Attreid <attreid@gmail.com>
 */
class Router extends \NAttreid\Routing\Router
{

	/** @var bool */
	private $isConsole;

	public function __construct(bool $isConsole, string $prefix)
	{
		parent::__construct($prefix);
		$this->isConsole = $isConsole;
	}

	public function createRoutes(): void
	{
		$router = $this->getRouter();

		if ($this->isConsole) {
			$router[] = new CliRouter();
		}

		// pousteni klienta na locale
		if (!Debugger::$productionMode) {
			$router[] = $cliRouter = new RouteList();
			$cliRouter[] = new Route($this->url . '[/<collection>][/<command>]', 'Console:Console:default');
		}
	}

}
