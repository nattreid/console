<?php

declare(strict_types=1);

namespace NAttreid\Console\Collections;

use Exception;
use NAttreid\AppManager\AppManager;
use NAttreid\Console\CommandCollection;
use NAttreid\Utils\TempFile;

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
	public function clearSession(string $expiration = null): void
	{
		$this->app->clearSession($expiration);
	}

	/**
	 * Smazani cache
	 */
	public function clearCache(): void
	{
		$this->app->clearCache();
	}

	/**
	 * Smazani cache modelu
	 */
	public function invalidateCache(): void
	{
		$this->app->invalidateCache();
	}

	/**
	 * Smazani logu
	 */
	public function clearLog(): void
	{
		$this->app->clearLog();
	}

	/**
	 * Smazani temp
	 */
	public function clearTemp(): void
	{
		$this->app->clearTemp();
	}

	/**
	 * Zaloha databaze
	 * @param string $path
	 * @throws Exception
	 */
	public function backupDatabase(string $path): void
	{
		$file = new TempFile(basename($path));
		$this->app->backupDatabase($file);
		$file->move(dirname($path));
	}

	/**
	 * Zaloha
	 * @param string $path
	 * @throws Exception
	 */
	public function backup(string $path): void
	{
		$file = new TempFile(basename($path));
		$this->app->backup($file);
		$file->move(dirname($path));
	}

	/**
	 * Git pull
	 */
	public function gitPull(): void
	{
		$this->app->gitPull(true);
	}

	/**
	 * Aktualizace composeru
	 */
	public function composerUpdate(): void
	{
		$this->app->composerUpdate(true);
	}

	/**
	 * Zapnuti udrzby
	 */
	public function maintenance(): void
	{
		$this->printLine('Maintenance On');
		$this->printLine("To turn off maintenance run application with parameter maintenanceOff in browser or 'php index.php maintenanceOff' in console");
		$this->app->maintenance();
	}

}
