<?php

namespace NAttreid\Console;

use Nette\Reflection\ClassType;
use Nette\Reflection\Method;
use Nette\Utils\Html;
use Nette\Utils\Strings;

/**
 *
 *
 * @author Attreid <attreid@gmail.com>
 */
class Console
{

	use \Nette\SmartObject;

	/** @var boolean */
	private $isConsole;

	/** @var string */
	private $prefix;

	/** @var CommandCollection[] */
	private $collections = [];

	public function __construct($isConsole, $prefix)
	{
		$this->isConsole = $isConsole;
		$this->prefix = $prefix;
	}

	/**
	 * Je apliakce spustena pres prikazovy radek?
	 * @return boolean
	 */
	public function isConsole()
	{
		return $this->isConsole;
	}

	/**
	 * Prida kolekci prikazu
	 * @param CommandCollection $collection
	 */
	public function addCommandCollection(CommandCollection $collection)
	{
		$collection->setConsole($this);
		$class = new ClassType($collection);
		$this->collections[Strings::lower($class->getShortName())] = $collection;
	}

	/**
	 * Spusti prikaz
	 * @param string $collection
	 * @param string $command
	 * @param array $args
	 * @throws \Nette\InvalidArgumentException
	 */
	public function execute($collection, $command, $args = [])
	{
		$collection = Strings::lower($collection);

		if (isset($this->collections[$collection])) {
			$class = new ClassType($this->collections[$collection]);
		} else {
			$class = null;
		}

		if ($command === 'help' || (empty($command) && empty($collection)) || (($collection === 'help' || $class !== null) && empty($command))) {
			$this->help($class);
			return;
		} elseif ($class !== null) {
			if ($class->hasMethod($command)) {
				$method = $class->getMethod($command);
				if ($method->isPublic() && !$method->isAbstract() && !$method->isStatic()) {
					$this->printTime($class->getShortName() . ' => ' . $method->name);
					$method->invokeArgs($this->collections[$collection], $args);
					$this->printTime($method->name . ' done');
					return;
				}
			}
		}
		throw new \Nette\InvalidArgumentException;
	}

	/**
	 * Vypise cas a text
	 * @param string $text
	 */
	private function printTime($text)
	{
		$line = '[' . date('d.m.Y H:i:s', time()) . '] ' . $text;
		$this->printLine($line);
	}

	/**
	 * Vypise napovedu
	 * @param ClassType $class
	 */
	private function help(ClassType $class = null)
	{
		if ($class === null) {
			foreach ($this->collections as $collection) {
				$this->printHelp(new ClassType($collection));
			}
		} else {
			$this->printHelp($class);
		}
	}

	/**
	 * Vypise napovedu pro danou tridu
	 * @param ClassType $class
	 */
	private function printHelp(ClassType $class)
	{
		if ($this->isConsole) {
			$this->printConsoleHelp($class);
		} else {
			$this->printHtmlHelp($class);
		}
	}

	/**
	 * Vypise napovedu pro danou tridu v konzoli
	 * @param ClassType $class
	 */
	private function printConsoleHelp(ClassType $class)
	{
		$this->printLine($class->getDescription());
		$this->printLine();

		foreach ($this->getMethod($class) as $method) {
			$line = $class->getShortName() . ':' . $method->name;

			foreach ($method->getParameters() as $param) {
				$line .= ' /' . $param->getName();
				if ($param->isDefaultValueAvailable()) {
					$line .= '=' . $this->getValue($param->getDefaultValue());
				}
			}
			$description = $method->getDescription();
			if (!empty($description)) {
				$line .= "\t\t(" . Strings::replace($description, '/\ +/', ' ') . ')';
			}
			$this->printLine($line);
		}
		$this->printLine();
	}

	/**
	 * Vypise napovedu pro danou tridu v HTML
	 * @param ClassType $class
	 */
	private function printHtmlHelp(ClassType $class)
	{
		$desc = Html::el('h1');
		$desc->setText($class->getDescription());
		$this->printLine($desc);

		foreach ($this->getMethod($class) as $method) {
			$desc = Html::el('pre');
			$desc->setStyle('margin-bottom: 0px');
			$desc->setText(Strings::replace($method->getDocComment(), '/(\ |\t)+/', ' '));

			$el = Html::el('a');
			$el->setHtml($method->name);

			$args = '';
			$params = [];
			foreach ($method->getParameters() as $param) {
				$p = $param->getName();
				if ($param->isDefaultValueAvailable()) {
					$value = $param->getDefaultValue();
					$p .= '=' . $this->getValue($value);
				} else {
					$args .= (empty($args) ? '?' : '&') . $param->getName() . '=';
				}
				$params[] = $p;
			}

			$link = Strings::lower(str_replace(':', '/', $this->prefix . '/' . $class->getShortName()) . '/' . $method->name);
			$el->href('/' . $link . $args);

			$line = $desc . $el;
			if (!empty($params)) {
				$line .= ' (' . implode(', ', $params) . ')';
			}
			$this->printLine($line);
		}
	}

	/**
	 * @param $value
	 * @return string
	 */
	private function getValue($value)
	{
		switch ($value) {
			default:
				return $value;
			case null:
				return 'null';
			case true:
				return '1';
			case false:
				return '0';
		}
	}

	/**
	 * @param ClassType $class
	 * @return Method[]
	 */
	private function getMethod(ClassType $class)
	{
		$methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC & ~\ReflectionMethod::IS_PROTECTED);
		$result = [];
		foreach ($methods as $method) {
			if (Strings::startsWith($method->name, '__') ||
				$method->name === 'setConsole'
			) {
				continue;
			}
			$result[] = $method;
		}
		return $result;
	}

	/**
	 * Vypise retezec na jeden radek
	 * @param string $string
	 */
	public function printLine($string = null)
	{
		if ($string !== null) {
			echo $string;
		}
		if ($this->isConsole) {
			echo "\n";
		} else {
			echo '<br/>';
		}
	}

}
