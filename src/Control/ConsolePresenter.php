<?php

namespace NAttreid\Console\Control;

use Tracy\Debugger,
    NAttreid\Console\Console;

/**
 * Presenter pro konzolove aplikace
 * 
 * @author Attreid <attreid@gmail.com>
 */
class ConsolePresenter extends \Nette\Application\UI\Presenter {

    /** @var Console @inject */
    public $console;

    public function startup() {
        parent::startup();
        if (!$this->console->isConsole() && Debugger::$productionMode) {
            throw new \Nette\Security\AuthenticationException;
        }
    }

    public function actionDefault($collection, $command, ...$args) {
        try {
            $this->console->execute($collection, $command, $args);
            $this->terminate();
        } catch (\Nette\InvalidArgumentException $ex) {
            if ($this->console->isConsole()) {
                $this->console->printLine("Command '$collection:$command' not exists.");
            } else {
                $this->error();
            }
        }
    }

}
