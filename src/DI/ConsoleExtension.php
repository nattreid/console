<?php

namespace NAttreid\Console\DI;

use NAttreid\Console\Collections\App;
use NAttreid\Console\Routing\Router;
use NAttreid\Routing\RouterFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Helpers;
use Nette\DI\MissingServiceException;
use Nette\DI\Statement;
use Nette\Reflection\ClassType;

/**
 * Rozsireni konzole
 *
 * @author Attreid <attreid@gmail.com>
 */
class ConsoleExtension extends CompilerExtension
{

	private $defaults = [
		'consoleMode' => '%consoleMode%',
		'prefix' => 'cli',
		'commands' => []
	];

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		$config['consoleMode'] = Helpers::expand($config['consoleMode'], $builder->parameters);

		$console = $builder->addDefinition($this->prefix('console'))
			->setClass('NAttreid\Console\Console')
			->setArguments([$config['consoleMode'], $config['prefix']]);

		$collections = $config['commands'];
		array_unshift($collections, App::class);

		foreach ($collections as $collection) {
			$commandCollection = $builder->addDefinition($this->prefix('collection' . $this->getShortName($collection)))
				->setClass($this->getClass($collection), $collection instanceof Statement ? $collection->arguments : []);
			$console->addSetup('addCommandCollection', [$commandCollection]);
		}

		$builder->addDefinition($this->prefix('router'))
			->setClass(Router::class)
			->setArguments([$config['consoleMode'], $config['prefix']]);
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$router = $builder->getByType(RouterFactory::class);
		try {
			$builder->getDefinition($router)
				->addSetup('addRouter', ['@' . $this->prefix('router'), RouterFactory::PRIORITY_HIGH]);
		} catch (MissingServiceException $ex) {
			throw new MissingServiceException("Missing extension 'nattreid/routing'");
		}

		$builder->getDefinition('application.presenterFactory')
			->addSetup('setMapping', [
				['Console' => 'NAttreid\Console\Control\*Presenter']
			]);
	}

	/**
	 * @param mixed $class
	 * @return string
	 */
	private function getClass($class)
	{
		if ($class instanceof Statement) {
			return $class->getEntity();
		} elseif (is_object($class)) {
			return get_class($class);
		} else {
			return $class;
		}
	}

	/**
	 * @param mixed $class
	 * @return string
	 */
	private function getShortName($class)
	{
		$classType = new ClassType($this->getClass($class));
		return $classType->getShortName();
	}

}
