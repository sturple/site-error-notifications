<?php

namespace Fgms\SiteErrorNotifications;

/**
 * Invokes die whenever any of its members are called.
 */
class DieErrorHandler implements ErrorHandlerInterface
{
	public function error($errno, $errstr, $errfile, $errline, array $errcontext)
	{
		die();
	}

	public function uncaught($ex)
	{
		die();
	}
}
