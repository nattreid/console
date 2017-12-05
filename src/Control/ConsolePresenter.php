<?php

declare(strict_types=1);

namespace NAttreid\Console\Control;

use NAttreid\Console\Console;
use NAttreid\Console\InvalidArgumentException;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;
use Tracy\Debugger;

/**
 * Presenter pro konzolove aplikace
 *
 * @author Attreid <attreid@gmail.com>
 */
class ConsolePresenter extends Presenter
{

	/** @var Console */
	private $console;

	public function __construct(Console $console = null)
	{
		parent::__construct();
		$this->console = $console;
	}

	/**
	 * @throws AuthenticationException
	 */
	public function startup(): void
	{
		parent::startup();
		if (!$this->console->isConsole() && Debugger::$productionMode) {
			throw new AuthenticationException;
		}
	}

	/**
	 * @param string|null $collection
	 * @param string|null $command
	 * @throws AbortException
	 * @throws BadRequestException
	 */
	public function actionDefault(string $collection = null, string $command = null): void
	{
		try {
			$this->console->execute($collection, $command, $this->getParameters());
		} catch (InvalidArgumentException $ex) {
			if ($this->console->isConsole()) {
				$this->console->printLine("Command '$collection:$command' doesn't exist.");
			} else {
				$this->error();
			}
		}
		$this->terminate();
	}

}
