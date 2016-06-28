<?php

namespace NAttreid\Console\Routing;

use Nette\Application\Routers\Route,
    Nette\Application\Routers\RouteList,
    Tracy\Debugger;

/**
 * Deploy router
 *
 * @author Attreid <attreid@gmail.com>
 */
class Router extends \NAttreid\Routing\Router {

    /** @var boolean */
    private $isConsole;

    public function __construct($isConsole, $prefix) {
        parent::__construct($prefix);
        $this->isConsole = $isConsole;
    }

    public function createRoutes() {
        $router = $this->getRouter();

        if ($this->isConsole) {
            $router[] = new CliRouter();
        }

        // pousteni klienta na locale
        if (!Debugger::$productionMode) {
            $router[] = $cliRouter = new RouteList();
            $cliRouter[] = new Route($this->getUrl() . '[/<collection>][/<command>]', 'Console:Console:default');
        }
    }

}
