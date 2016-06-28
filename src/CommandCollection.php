<?php

namespace NAttreid\Console;

use NAttreid\Console\Console;

/**
 * Kolekce prikazu pro konzoli
 *
 * @author Attreid <attreid@gmail.com>
 */
abstract class CommandCollection {

    use \Nette\SmartObject;

    /** @var Console */
    private $console;

    public function setConsole(Console $console) {
        $this->console = $console;
    }

    /**
     * Vypise retezec na jeden radek
     */
    protected function printLine($string) {
        $this->console->printLine($string);
    }

}
