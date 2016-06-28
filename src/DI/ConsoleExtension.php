<?php

namespace NAttreid\Console\DI;

/**
 * Rozsireni konzole
 * 
 * @author Attreid <attreid@gmail.com>
 */
class ConsoleExtension extends \Nette\DI\CompilerExtension {

    private $defaults = [
        'consoleMode' => '%consoleMode%',
        'prefix' => 'cli',
        'commands' => []
    ];

    public function loadConfiguration() {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults, $this->getConfig());

        $config['consoleMode'] = \Nette\DI\Helpers::expand($config['consoleMode'], $this->getContainerBuilder()->parameters);

        $console = $builder->addDefinition($this->prefix('console'))
                ->setClass('NAttreid\Console\Console');

        $collections = $config['commands'];
        array_unshift($collections, 'NAttreid\Console\Collections\App');

        foreach ($collections as $collection) {
            $commandCollection = $builder->addDefinition($this->prefix('commands' . $this->getShortName($collection)))
                    ->setClass($this->getClass($collection), $collection instanceof Statement ? $collection->arguments : [])
                    ->addSetup('setConsole', [$console]);
            $console->addSetup('addCommandCollection', [$commandCollection]);
        }

        $builder->addDefinition($this->prefix('router'))
                ->setClass('NAttreid\Console\Routing\Router')
                ->setArguments([$config['consoleMode'], $config['prefix']]);
    }

    public function beforeCompile() {
        $builder = $this->getContainerBuilder();
        $router = $builder->getByType('NAttreid\Routers\RouterFactory');
        try {
            $builder->getDefinition($router)
                    ->addSetup('addRouter', ['@' . $this->prefix('router'), 0]);
        } catch (\Nette\DI\MissingServiceException $ex) {
            throw new \Nette\DI\MissingServiceException("Missing extension 'nattreid/routing'");
        }

        $builder->getDefinition('application.presenterFactory')
                ->addSetup('setMapping', [
                    ['Console' => 'NAttreid\Cosole\Control\*Presenter']
        ]);
    }

    /**
     * @param mixed $class
     * @return string
     */
    private function getClass($class) {
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
    private function getShortName($class) {
        $classType = new \Nette\Reflection\ClassType($this->getClass($class));
        return $classType->getShortName();
    }

}
