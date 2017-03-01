<?php

declare(strict_types = 1);

namespace NAttreid\Console;

/**
 * Kolekce prikazu pro konzoli
 *
 * @author Attreid <attreid@gmail.com>
 */
abstract class CommandCollection
{

	/** @var Console */
	private $console;

	public function setConsole(Console $console)
	{
		$this->console = $console;
	}

	/**
	 * Vypise retezec na jeden radek
	 * @param string $string
	 */
	protected function printLine(string $string)
	{
		$this->console->printLine($string);
	}

}
