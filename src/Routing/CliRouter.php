<?php

namespace NAttreid\Console\Routing;

use Nette\Application\Request,
    \Nette\Http\Url,
    \Nette\Http\IRequest,
    Nette\Application\IRouter;

/**
 * Router pro CLI
 *
 * @author Attreid <attreid@gmail.com>
 */
class CliRouter implements IRouter {

    use \Nette\SmartObject;

    /** @var string */
    private $default;

    public function __construct($default) {
        $this->default = $default;
    }

    public function constructUrl(Request $appRequest, Url $refUrl) {
        return NULL;
    }

    public function match(IRequest $httpRequest) {
        if (empty($_SERVER['argv']) || !is_array($_SERVER['argv'])) {
            return NULL;
        }
        $args = $_SERVER['argv'];
    }

}
