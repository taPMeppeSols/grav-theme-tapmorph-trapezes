<?php

namespace Grav\Theme\tapmorph;

/**
 * this class simply provides useful functions
 * @since 2021.01 PM (27.02.2021) standalone
 */
final class Utils extends \Grav\Common\Utils
{
	/**
	 * @property string allowableTags the HTML allowed tags
	 * @see https://www.php.net/manual/en/function.strip-tags.php
	 */
	const allowableTags = '<a><br><span><i>';
	private static $dev = false; //TRUE if development mode


	/** #setter */
	static function init(bool $production)
	{
		$umbrella = 'tapmeppe';
		self::$dev =
			( //TRUE if the plugin is (most likely) running on my computer
				preg_match("/^192\.168\.\\d+\.\\d+$/", gethostbyname($host = gethostname())) && $host == $umbrella &&
				php_uname('s') == "Windows NT" && php_uname('n') == strtoupper($umbrella) && php_uname('m') == "AMD64"
			)
			|| !$production;
	}

	/** #getter */
	static function dev(): bool
	{
		return self::$dev;
	}

	/** */
	static function fn(string $suffix, string $prefix): string
	{
		return $prefix . ucfirst($suffix);
	}

	/**
	 * this function is used to generate the authentication code
	 * @param string $url the current page URL
	 * @return string the authentication code
	 */
	static function authCode(string $url): string
	{
		return preg_replace(
			$regex = "|\\W+|",
			'',
			base64_encode(preg_replace(
				$regex,
				'@',
				base64_encode(preg_replace(
					$regex,
					'#',
					$url
				))
			))
		);
	}

	/**
	 * this function is used to clean the given URL
	 * @version 2020.7 @since PM (07.02.2020) standalone
	 */
	static function clean(string $url): string
	{
		return urldecode(trim($url));
	}

	/**
	 * @version 2020.7 @since PM (07.02.2020) standalone
	 * @source system\defines.php `DS` can also be used
	 */
	static function path(...$path): string
	{
		return implode(DIRECTORY_SEPARATOR, $path);
	}

	/**
	 * this function is used to convert a string to an array
	 * #wrapper
	 */
	static function str2Ar(string $data): array
	{
		return json_decode($data, true) ?: [];
	}

	/**
	 * this function is used to convert an array to a string
	 * #wrapper
	 * #JSON_PRETTY_PRINT
	 */
	static function ar2Str(array $data, int $flags = 0): string
	{
		return json_encode($data, $flags);
	}

	/**
	 * this function is used during the development to debug the code
	 * #die
	 * @param mixed $data
	 */
	static function debug(...$data)
	{
		if (self::$dev) {
			echo '<pre>';
			print_r($data);
			echo '</pre>';
			die('');
		}
	}
}
