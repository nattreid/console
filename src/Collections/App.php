<?php

declare(strict_types = 1);

namespace NAttreid\Console\Collections;

use NAttreid\AppManager\AppManager;
use NAttreid\Console\CommandCollection;

/**
 * Sprava aplikace
 *
 * @author Attreid <attreid@gmail.com>
 */
class App extends CommandCollection
{

	/** @var AppManager */
	private $app;

	public function __construct(AppManager $app)
	{
		$this->app = $app;
	}

	/**
	 * Smazani session
	 * @param string $expiration (2 minutes, 4 days, atd) null smaze pouze expirovanou session
	 */
	public function clearSession(string $expiration = null)
	{
		$this->app->clearSession($expiration);
	}

	/**
	 * Smazani cache
	 */
	public function clearCache()
	{
		$this->app->clearCache();
	}

	/**
	 * Smazani cache modelu
	 */
	public function invalidateCache()
	{
		$this->app->invalidateCache();
	}

	/**
	 * Smazani logu
	 */
	public function clearLog()
	{
		$this->app->clearLog();
	}

	/**
	 * Smazani temp
	 */
	public function clearTemp()
	{
		$this->app->clearTemp();
	}

	/**
	 * Git pull
	 */
	public function gitPull()
	{
		$this->app->gitPull(true);
	}

	/**
	 * Aktualizace composeru
	 */
	public function composerUpdate()
	{
		$this->app->composerUpdate(true);
	}

	/**
	 * Zapnuti udrzby
	 */
	public function maintenance()
	{
		$this->printLine('Maintenance On');
		$this->printLine("To turn off maintenance run application with parameter maintenanceOff in browser or 'php index.php maintenanceOff' in console");
		$this->app->maintenance();
	}

}
