<?php

namespace Grav\Theme\tapmorph;

/**
 * this class is the default way to manage the subscribers
 * @final 
 * @since PM (11.03.2022) standalone
 */
final class SubscribersCustom extends Subscribers
{
	/**
	 * @override
	 */
	protected function register_(string &$response, string &$error, string $email, string $code)
	{
		throw new \Exception("Error Processing Request", 1);
	}

	/**
	 * @override
	 * #redirect()
	 */
	protected function optIn_(string &$error, string $alias)
	{
		throw new \Exception("Error Processing Request", 1);
	}

	/**
	 * @override
	 * #redirect()
	 */
	protected function delete_(string $email, string $flag)
	{
		throw new \Exception("Error Processing Request", 1);
	}

	/**
	 * @override
	 */
	protected function deleteBulk_(string &$response, array $emails, callable $text)
	{
		throw new \Exception("Error Processing Request", 1);
	}

	/**
	 * @override
	 */
	protected function addresses_(string $format): array
	{
		throw new \Exception("Error Processing Request", 1);
	}
}
