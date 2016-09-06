<?php

namespace NAttreid\Console\Control;

use Nette\Security\AuthenticationException;
use Tracy\Debugger,
    NAttreid\Console\Console;

/**
 * Presenter pro konzolove aplikace
 *
 * @author Attreid <attreid@gmail.com>
 */
class ConsolePresenter extends \Nette\Application\UI\Presenter {

    /** @var Console */
    private $console;

    public function __construct(Console $console = NULL) {
        parent::__construct();
        $this->console = $console;
    }

    public function startup() {
        parent::startup();
        if (!$this->console->isConsole() && Debugger::$productionMode) {
            throw new AuthenticationException;
        }
    }

    public function actionDefault($collection, $command, ...$args) {
        try {
            $this->console->execute($collection, $command, $args);
        } catch (\Nette\InvalidArgumentException $ex) {
            if ($this->console->isConsole()) {
                $this->console->printLine("Command '$collection:$command' doesn't exist.");
            } else {
                $this->error();
            }
        }
        $this->terminate();
    }

}
