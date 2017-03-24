<?php

declare(strict_types=1);

namespace NAttreid\Console\Routing;

use Nette\Application\Helpers;
use Nette\Application\IRouter;
use Nette\Application\Request;
use Nette\Http\IRequest;
use Nette\Http\Url;
use Nette\SmartObject;

/**
 * Router pro CLI
 *
 * @author Attreid <attreid@gmail.com>
 */
class CliRouter implements IRouter
{
	use SmartObject;

	const COMMAND_KEY = 'command';

	public function constructUrl(Request $appRequest, Url $refUrl)
	{
		return null;
	}

	public function match(IRequest $httpRequest)
	{
		if (empty($_SERVER['argv']) || !is_array($_SERVER['argv'])) {
			return null;
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
				$flag = null;
				continue;
			}

			if (isset($flag)) {
				$params[$flag] = true;
				$flag = null;
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

		@list($collection, $command) = Helpers::splitName($params[self::COMMAND_KEY]);
		if (empty($collection)) {
			$collection = $command;
			$command = null;
		}

		unset($params[self::COMMAND_KEY]);
		$params['collection'] = $collection;
		$params['command'] = $command;

		return new Request(
			'Console:Console',
			'CLI',
			$params
		);
	}

}
