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

    const COMMAND_KEY = 'command';

    public function constructUrl(Request $appRequest, Url $refUrl) {
        return NULL;
    }

    public function match(IRequest $httpRequest) {
        if (empty($_SERVER['argv']) || !is_array($_SERVER['argv'])) {
            return NULL;
        }

        $params = ['action' => 'default'];
        $names = [self::COMMAND_KEY];
        $args = $_SERVER['argv'];
        array_shift($args);

        foreach ($args as $arg) {
            $opt = preg_replace('#/|-+#A', '', $arg);
            if ($opt === $arg) {
                if (isset($flag) || $flag = array_shift($names)) {
                    $params[$flag] = $arg;
                } else {
                    $params[] = $arg;
                }
                $flag = NULL;
                continue;
            }

            if (isset($flag)) {
                $params[$flag] = TRUE;
                $flag = NULL;
            }

            if ($opt !== '') {
                $pair = explode('=', $opt, 2);
                if (isset($pair[1])) {
                    $params[$pair[0]] = $pair[1];
                } else {
                    $flag = $pair[0];
                }
            }
        }

        @list($collection, $command) = \Nette\Application\Helpers::splitName($params[self::COMMAND_KEY]);
        if (empty($collection)) {
            $collection = $command;
            $command = NULL;
        }
        
        unset($params[self::COMMAND_KEY]);
        $params['collection'] = $collection;
        $params['command'] = $command;

        return new Request(
                'Console:Console', //
                'CLI', //
                $params //
        );
    }

}
