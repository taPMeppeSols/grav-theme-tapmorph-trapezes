<?php

namespace Grav\Theme\tapmorph;

/**
 * this class is used manage the cookies settings
 * @final
 * @since 2021.01 PM (09.03.2021) standalone
 */
final class Cookies
{
	const 
		key = 'cookies',
		nodes = ['statistics', 'marketing', 'others'];
	private $key, $config, $settings = [];


	/**
	 * @param string $theme
	 * @param \Grav\Common\Config\Config $config
	 */
	function __construct(string $theme, \Grav\Common\Config\Config $config)
	{
		$this->key = "$theme-" . base64_encode('grav-' . self::key);
		$this->config = $config;
	}

	/**
	 * this function is used to set the cookies settings
	 */
	function set()
	{
		if (isset($_POST[self::key])) foreach ($_POST[self::key] as $key => $_) {
			$this->settings[$key] = md5(trim($this->config->get('theme.' . self::key . "_snippets_$key", ''))); //store the current snippets
		}
		setcookie(
			$this->key,
			Utils::dev() ? Utils::ar2Str($this->settings) : base64_encode(Utils::ar2Str($this->settings)),
			time() + 86400 * 30, //+30 days
			'/'
		);
	}

	/**
	 * this function is used to get the current cookies settings
	 * @return array s.e.
	 */
	function get(): array
	{
		if ($this->settings) return $this->settings;
		//DO NOT SWITCH
		elseif (isset($_COOKIE[$this->key])) {
			$cookies = Utils::dev() ?
				Utils::str2Ar($_COOKIE[$this->key]) :
				Utils::str2Ar(base64_decode($_COOKIE[$this->key]));
			foreach (['essential', ...self::nodes] as $node) {
				if ($snippets = trim($this->config->get('theme.' . self::key . "_snippets_$node", ''))) {
					if (!(isset($cookies[$node]) && $cookies[$node] == md5($snippets))) return []; //TRUE if new node or changes have been detected 
				}
			}
			//...
			return $cookies;
		}
		//...
		return [];
	}
}
