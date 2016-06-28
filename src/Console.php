<?php

namespace NAttreid\Console;

use NAttreid\Console\CommandCollection,
    Nette\Utils\Strings,
    Nette\Reflection\ClassType,
    Nette\Utils\Html;

/**
 * 
 *
 * @author Attreid <attreid@gmail.com>
 */
class Console {

    use \Nette\SmartObject;

    /** @var boolean */
    private $isConsole;

    /** @var CommandCollection[] */
    private $collections = [];

    public function __construct($isConsole) {
        $this->isConsole = $isConsole;
    }

    /**
     * Je apliakce spustena pres prikazovy radek?
     * @return boolean
     */
    public function isConsole() {
        return $this->isConsole;
    }

    /**
     * Prida kolekci prikazu
     * @param CommandCollection $collection
     */
    public function addCommandCollection(CommandCollection $collection) {
        $collection->setConsole($this);
        $class = new ClassType($collection);
        $this->collections[String::lower($class->getShortName())] = $collection;
    }

    /**
     * Spusti prikaz
     * @param string $collection
     * @param string $command
     * @param array $args
     * @throws \Nette\InvalidArgumentException
     */
    public function execute($collection, $command, $args = []) {
        $collection = Strings::lower($collection);

        if (isset($this->collections[$collection])) {
            $class = new ClassType($this->collections[$collection]);
        } else {
            $class = NULL;
        }

        if ($command === 'help' || empty($command)) {
            $this->help($class);
        } elseif ($class !== NULL) {
            if ($class->hasMethod($command)) {
                $method = $class->getMethod($command);
                if ($method->isPublic() && !$method->isAbstract() && !$method->isStatic()) {
                    $this->printTime();
                    $method->invokeArgs($this->collections[$collection], $class->combineArgs($method, $args));
                    $this->printTime('Done', FALSE);
                    return;
                }
            }
        }
        throw new \Nette\InvalidArgumentException;
    }

    /**
     * Vypise cas a text
     * @param string $text
     * @param boolean $fullyQualified
     */
    private function printTime($text = NULL, $fullyQualified = TRUE) {
        $line = '[' . date('d.m.Y H:i:s', time()) . '] '
                . $text . (!empty($text) ? ' ' : '')
                . ($fullyQualified ? $this->getName() . ' => ' : '')
                . $this->getAction();
        $this->printLine($line);
    }

    /**
     * Vypise napovedu
     * @param ClassType $class
     */
    private function help(ClassType $class) {
        if ($class === NULL) {
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
    private function printHelp(ClassType $class) {
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
    private function printConsoleHelp(ClassType $class) {
        $this->printLine($class->getDescription());
        $this->printLine();

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $line = $class->getShortName() . ':' . $method->name;

            foreach ($method->getParameters() as $param) {
                $line .= ' /' . $param->getName();
                if ($param->isDefaultValueAvailable()) {
                    $line .= '=' . $param->getDefaultValue();
                }
            }
            $description = $method->getDescription();
            if (!empty($description)) {
                $line .= "\t\t(" . Strings::replace($description, '/\ +/', ' ') . ')';
            }
            $this->printLine($line);
            $this->printLine();
        }
    }

    /**
     * Vypise napovedu pro danou tridu v HTML
     * @param ClassType $class
     */
    private function printHtmlHelp(ClassType $class) {
        $desc = Html::el('h1');
        $desc->setText($class->getDescription());
        $this->printLine($desc);

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $desc = Html::el('pre');
            $desc->setStyle('margin-bottom: 0px');
            $desc->setText(Strings::replace($method->getDocComment(), '/\ +/', ' '));

            $el = Html::el('a');
            $el->setHtml($method->name);

            $args = '';
            $params = [];
            foreach ($method->getParameters() as $param) {
                if (empty($args)) {
                    $args .= '?';
                } else {
                    $args .= '&';
                }
                $args .= $param->getName() . '=';
                $p = $param->getName();
                if ($param->isDefaultValueAvailable()) {
                    $args .= $param->getDefaultValue();
                    $p .= '=' . $param->getDefaultValue();
                }
                $params[] = $p;
            }

            $link = Strings::lower(str_replace(':', '/', $class->getShortName()) . '/' . $method->name);
            $el->href('/' . $link . $args);

            $line = $desc . $el;
            if (!empty($params)) {
                $line .= ' (' . implode(', ', $params) . ')';
            }
            $this->printLine($line);
        }
    }

    /**
     * Vypise retezec na jeden radek
     * @param string $string
     */
    public function printLine($string = NULL) {
        if ($string !== NULL) {
            echo $string;
        }
        if ($this->isConsole) {
            echo "\n";
        } else {
            echo '<br/>';
        }
    }

}
