<?php

/**
 * @see https://learn.getgrav.org/17/forms/forms/reference-form-actions
 * - $this->grav['language']->translate('PLUGIN_FORM.ERROR_VALIDATING_CAPTCHA');
 * @see https://learn.getgrav.org/17/themes/theme-vars
 * @see https://learn.getgrav.org/17/themes/twig-filters-functions
 * @see https://learn.getgrav.org/17/cookbook/twig-recipes#custom-twig-filter-function
 * @see https://learn.getgrav.org/17/forms/forms
 * @see https://learn.getgrav.org/17/api
 * @see https://learn.getgrav.org/17/plugins/event-hooks
 * 
 * @example development
 * - http://tapmeppe/solutions/tapmorph/grav_development/admin
 * - http://tapmeppe/solutions/tapmorph/grav_development/en
 * - http://tapmorph.grav.dev/en
 * - http://tapmorph.grav.dev/admin
 * - http://tapmorph.grav.tests/en
 * - http://tapmorph.grav.tests/admin
 */

namespace Grav\Theme;

// use Composer\Autoload\ClassLoader;
use Grav\Common\{Theme, Grav};
use Grav\Common\Language\LanguageCodes;
use RocketTheme\Toolbox\Event\Event;

/**
 * @since 2021.01 PM (28.02.2021)
 * replacement `RocketTheme\Toolbox\Event\Event` by `Symfony\Contracts\EventDispatcher\Event`
 * - although being now @deprecated, replacing `RocketTheme\Toolbox\Event\Event` leads to a DataType exception
 */

use Grav\Theme\tapmorph\{Utils, Cookies, Subscribers, SubscribersDefault, SubscribersCustom, Trapezes};


class TapmorphTrapezes extends Theme
{
	const sep = "|#|", lang = 'en'; //default language
	private static
		$thm, $theme, $dir, $slug, $auth,
		$recipients = [], $routes = [], $labels = [];
	private $lang = self::lang;
	private Cookies $cookies;
	private Subscribers $subscribers;
	private Trapezes $trapezes;


	##### construct - start #####
	/**
	 * `$this->config` is set in system/src/Grav/Common/Plugin.php
	 */
	##### construct - end #####


	##### ADMIN functions - start #####

	/**
	 * this function is used to get the list of all users (email addresses, language) available in the system
	 * @since PM (20.12.2019) standalone
	 * @since PM (25.01.2020) - the dependency from the plugin 'admin-addon-user-subscribers' has become optional
	 * 												- additionally an internal simple buffer is now used to speed up multiple access to the list
	 * @source tapmorph-maintenance
	 * @source /user/plugins/admin-addon-user-subscribers/src/Users/Manager.php :: userNames
	 * @source /user/plugins/admin-addon-user-subscribers/src/Users/Manager.php :: users
	 */
	static function recipients(): array
	{
		if (!self::$recipients) {
			self::$recipients = []; //simple security measure

			if (class_exists("\\AdminAddonUserManager\\Users\\Manager")) {
				$users = \AdminAddonUserManager\Users\Manager::$instance->users();
				foreach ($users as $user) if ($user['email']) self::$recipients[$user['email'] . Subscribers::sep . $user['language'] . Subscribers::sep . $user['title']] = $user['email'] . ' - ' . $user['title']; //alternative: $user['fullname']
			} else {
				self::autoload();
				$grav = Grav::instance();
				$files = ($dir = $grav['locator']->findResource('account://')) ?
					array_diff(scandir($dir), ['.', '..']) :
					[];
				foreach ($files as $file) if (Utils::endsWith($file, YAML_EXT)) {
					/** @source system\defines.php */
					$user = $grav['accounts']->load(trim(pathinfo($file, PATHINFO_FILENAME)));
					//$users[$user->username] = $user;
					if ($user['email']) self::$recipients[$user['email'] . Subscribers::sep . $user['language'] . Subscribers::sep . $user['title']] = $user['email'] . ' - ' . $user['title']; //alternative: $user['fullname']
				}
			}

			ksort(self::$recipients);
		}

		return self::$recipients;
	}

	/**
	 * this function is used to get the list of all current pages in `blueprints.yaml`
	 * @since PM (18.12.2019) standalone
	 * @since PM (25.01.2020) - the dependency from the plugin 'admin' has been removed
	 * 												- additionally an internal simple buffer is now used to speed up multiple access to the list
	 * @since PM (08.03.2022) the code has been adapted to the new version of grav
	 * @source tapmorph-maintenance
	 * @source https://github.com/getgrav/grav-plugin-sitemap/blob/develop/sitemap.php :: onPagesInitialized
	 * @return array
	 */
	static function pages(): array
	{
		if (!self::$routes) {
			$grav = Grav::instance();

			/**
			 * @source user\plugins\admin\classes\plugin\Admin.php :: routes
			 * @source user\plugins\admin\classes\plugin\Admin.php :: enablePages
			 */
			$pages = $grav['pages'];
			$pages->enablePages();
			/** @since @version Grav 1.7 */
			$routes = array_unique($pages->routes());
			ksort($routes);

			/**
			 * @source /user/plugins/admin/classes/admin.php :: siteLanguages
			 */
			$config = $grav['config'];
			$labelise = fn (array $languages): array => array_map(
				fn (string $language): string => LanguageCodes::getNativeName($language),
				$languages
			);
			$languages = $labelise((array) $config->get('system.languages.supported', [])); //the supported languages
			// $hideHomeRoute = !$config->get('system.home.hide_in_urls', false); //the opposite is on purpose

			self::$routes = []; //the enriched list of the pages
			foreach ($routes as /*$route => */ $path) {
				$page = $pages->get($path); //the current page
				if ($page->visible()) {
					$langs = $labelise(array_keys($page->translatedLanguages())); //the languages in which the current page is available

					//$this->hide_home_route
					//$this->home_route
					//self::$routes[$page->home() && $hideHomeRoute ? $route[0] : $route] = $page->title() ." (". implode(", ", $langs ?: $languages) .")";
					//self::$routes[$page->home() && $hideHomeRoute ? $route[0] : $route] = $page->menu() ." (". implode(", ", $langs ?: $languages) .")";

					//@see system/src/Grav/Common/Page/Page.php :: url
					self::$routes[$page->url(false, false, false, true)] = $page->title() . " (" . implode(", ", $langs ?: $languages) . ")";
				}
			}
			//array_keys(self::$routes);
		}

		return self::$routes;
	}

	##### ADMIN functions - end #####


	##### EVENT functions - start #####

	/**
	 * @override
	 * @source tapmorph-maintenance
	 */
	static function getSubscribedEvents()
	{
		//the theme name & the directory in which the system specific data will by stored
		self::autoload();
		$grav = Grav::instance();
		$locator = $grav['locator'];
		//self::$theme = basename(__DIR__); //the theme name
		self::$theme = basename(self::$thm = $locator->findResource('theme://')); //the theme -path & -name
		Utils::init($grav['config']->get('themes.' . self::$theme . '.production'));

		if (!(self::$dir = $locator->findResource('user-data://' . self::$theme))) { //custom path finding way
			mkdir( //invoked if the directory doesn't exit yet
				self::$dir = Utils::path(USER_DIR . 'data', self::$theme),
				/** @source system\defines.php */
				0755,
				true
			);
			/*
			self::$dir = defined('USER_DIR') ?
				Utils::path(USER_DIR .'data', self::$theme) : /** @source system\defines.php * /
				Utils::path(dirname(__DIR__, 2), 'data', self::$theme), 
			*/
		}

		self::$slug = explode('-', self::$theme)[0];
		self::$auth = self::$slug . '_auth_code';

		/** @see https://learn.getgrav.org/17/plugins/event-hooks#event-order */
		return [
			// this is not working for me
			// 'onPluginsInitialized' => [
			// 	['autoload', 100001],
			// 	['init', 100000]
			// ],
			'onFormProcessed' => ['onFormProcessed', 0],
			'onRequestHandlerInit' => ['onRequestHandlerInit', 0],
			'onShortcodeHandlers' => ['onShortcodeHandlers', 0],
			'onTwigInitialized' => ['onTwigInitialized', 0],
			'onTwigSiteVariables' => ['onTwigSiteVariables', 0],
		];
	}

	/**
	 */
	static function autoload()
	{
		require_once __DIR__ . '/vendor/autoload.php';
	}

	/**
	 * this function is used to process the grav native form submissions
	 * @see https://learn.getgrav.org/17/forms/forms/reference-form-actions#custom-actions
	 * @see https://learn.getgrav.org/17/forms/forms/reference-form-actions#an-example-of-custom-form-handling
	 * @source user\plugins\form\form.php::onFormProcessed
	 * @source user\plugins\form\classes\Form.php
	 */
	function onFormProcessed(Event $event)
	{
		/* this block is quite interesting
				$uri = $this->grav['uri'];
				$action = $form->value('action');
				$hostname = $uri->host();
				$ip = Uri::ip();
			*/

		/**
		 * @var string $action
		 * @var Grav\Plugin\Form\Form $form
		 * 			> values > items > data
		 */
		$action = $event['action'];
		$form = $event['form'];
		/**
		 * - $form->value()->toArray() ={} $form->data()->toArray()
		 * - $form->getData()->toArray() ??
		 * - $form->getName() ={} $form->name()
		 * - $form->getFields() ={} $form->fields()
		 */
		//$params = $event['params']; //bool
		$locator = $this->grav['locator'];

		switch ($action) {
			case 'ajax':
				if (Utils::dev()) file_put_contents(
					__DIR__ . '/helpers/form.data.json',
					Utils::ar2Str([$form->getName(), $form->value()->toArray()], JSON_PRETTY_PRINT)
				);
			case 'presave':
				if ($name = $form->getName()) { //since plugin:form:v3.0 the form name is mandatory
					//DO NOT MERGE
					if (!$locator->findResource("user-data://$name")) mkdir( //invoked if the directory doesn't exit yet
						Utils::path(USER_DIR . 'data', $name),
						/** @source system\defines.php */
						0755,
						true
					);
				}
		}
		//return;
	}

	/** @source tapmorph-maintenance */
	function onRequestHandlerInit(Event $event)
	{
		/**
		 * @var string $url I decide to use a custom to be in control of the result
		 * 									@see https://learn.getgrav.org/17/api#class-gravcommonuri URI alternative used by GRAV
		 * 									@example $uri = $this->grav['uri'];
		 */
		$this->cookies = new Cookies(self::$theme, $this->config);
		try {
			require_once self::$dir . '/SubscribersCustom.php'; //throws \Error if file couldn't be loaded
			$subscribers = SubscribersCustom::class;
			if (!(class_exists($subscribers) && is_subclass_of($subscribers, Subscribers::class))) throw new \Exception("Incorect format");
		} catch (\Throwable $e) {
			$subscribers = SubscribersDefault::class;
		}
		$this->subscribers = new $subscribers(
			$this->lang = $this->grav['language']->getActive() ?: $this->config->get('site.default_lang', self::lang),
			self::lang,
			self::$auth,
			$this->config->get('site.title', self::$theme),
			self::$dir,
			self::$thm,
			$url =
				(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ? "https://" : "http://") .
				$_SERVER["SERVER_NAME"] .
				explode('?', $_SERVER["REQUEST_URI"])[0],
			$this->config->get('theme.registration_recipients', []),
			$this->grav['accounts']
		);

		/**
		 * PM (18.12.2019) registration block
		 * - the sign up process is done according to the European GDPR directive
		 */
		if (isset($_REQUEST[self::$auth])) {
			error_reporting(E_ERROR | E_WARNING | E_PARSE); //security measure to prevent warnings to distort the JSON responses

			$code = Utils::authCode($url);
			if (isset($_POST[self::$auth]) && $_POST[self::$auth] == $code) {
				if (isset($_POST[Cookies::key]) && $_POST[Cookies::key]) $this->cookies->set();
				elseif (isset($_POST[Subscribers::email]) && $_POST[Subscribers::email]) $this->subscribers->register(
					$this->config->get('theme.registration_status', false),
					$code
				);
				elseif (isset($_POST[Subscribers::emails]) && $_POST[Subscribers::emails]) $this->subscribers->deleteBulk(
					fn (string $key): string => $this->text($key)
				);
				//NO REDIRECT
			} elseif (isset($_GET[self::$auth])) {
				$auth = Utils::clean($_GET[self::$auth]);
				if ($auth == $code) $this->subscribers->optIn($this->config->get('theme.registration_status', false)); //opt-in step from the confirmation email
				elseif ($this->subscribers->isEmail($auth) && isset($_GET['delete']) && $_GET['delete']) $this->subscribers->delete($auth); //delete option
			}

			$event->stopPropagation(); //this line will actually never be reached; it was set just to remember its existence for future projects
		} else return $event;
	}

	/**
	 * @source user\plugins\shortcode-core\classes\plugin\ShortcodeManager.php :: registerAllShortcodes & registerShortcode
	 */
	function onShortcodeHandlers(Event $event)
	{
		if (class_exists("Grav\\Plugin\\Shortcodes\\Shortcode")) { //TRUE if the 'shortcode-core' plugin has been installed
			require_once __DIR__ . '/php/shortcodes/Trapezes.php';

			(new \Grav\Plugin\Shortcodes\ShortcodeTrapezes(
				self::$slug,
				$this->theme2design(),
				// DO NOT SWITCH
				self::$labels['error']
			))->init();

			// $this->grav['shortcode']->registerAllShortcodes(__DIR__ . '/php/shortcodes');
			// $this->grav['shortcode']->registerShortcode('Trapezes', __DIR__ . '/php/shortcodes');
		}
	}

	/**
	 * this function is used to handle the 'onTwigInitialized' event
	 * @see https://learn.getgrav.org/17/cookbook/twig-recipes#custom-twig-filter-function
	 * @source system\src\Grav\Common\Twig\Twig.php
	 * @param Event $event the object representing the event
	 */
	function onTwigInitialized(Event $event)
	{
		//Utils::debug($this->grav['user']);
		$twig = $this->grav['twig']->twig();
		$this->trapezes = new Trapezes(
			self::$slug,
			$this->theme2design(),
			// DO NOT SWITCH
			self::$labels['error'],
			$this->grav['twig']
		);
		$options = ['is_safe' => ['html']];
		foreach (['text', 'label', 'labelC', 'resource', Subscribers::key, 'errorNote'] as $fn) {
			$twig->addFunction(new \Twig_SimpleFunction(Utils::fn($fn, self::$slug), [$this, $fn]), $options);
			$twig->addFilter(new \Twig_SimpleFilter(Utils::fn($fn, self::$slug), [$this, $fn]), $options);
		}

		$twig->addFunction(new \Twig_SimpleFunction(Trapezes::fn($fn = 'block'), [$this->trapezes, $fn]), $options);
	}

	/**
	 * this function is used to add additional values to the TWIG engine
	 * @source tapmorph-maintenance
	 * @version 2020.8 @since PM (08.02.2020)
	 * @since PM (24.12.2020) the template check has been added
	 * @since PM (25.12.2020) @deprecated the template check
	 * @see https://learn.getgrav.org/17/plugins/event-hooks#ontwigsitevariables
	 * @see https://learn.getgrav.org/17/api#class-gravcommonpagepage
	 * @see https://www.geeksforgeeks.org/php-startswith-and-endswith-functions/
	 * @see https://learn.getgrav.org/17/themes/theme-vars#path
	 * @see https://learn.getgrav.org/17/themes/theme-vars#permalink
	 * @see https://learn.getgrav.org/17/themes/theme-vars#base-url-simple-variable
	 * @see https://learn.getgrav.org/17/themes/theme-vars#rooturl-include_host-false
	 * @param Event $event the object representing the event
	 */
	function onTwigSiteVariables(Event $event)
	{
		Trapezes::image( //set the image configuration; it can't be done any sooner
			($page = $this->grav['page'])->path(),
			$page->permalink(),
			$this->grav['base_url'] . '/' . $this->grav['locator']->findResource('theme://images/placeholder.png', false),
			//$this->grav['uri']->rootUrl() .'/'. $this->grav['locator']->findResource('theme://images/placeholder.png', false),
		);

		$values = [
			'slug' => self::$slug,
			'auth' => self::$auth,
			'dev' => Utils::dev(),
			'name' => Subscribers::name,
			'email' => Subscribers::email,
			'emails' => Subscribers::emails,
			'languages' => \Grav\Plugin\Admin\Admin::siteLanguages(), //the supported languages
			//...
			Cookies::key . '_key' => Cookies::key,
			Cookies::key . '_nodes' => Cookies::nodes,
			Cookies::key . '_settings' => $this->cookies->get(),
			Trapezes::key . '_key' => Trapezes::key,
			Trapezes::key . '_col' => Trapezes::col,
			Trapezes::key . '_add' => Trapezes::add,
		];
		$twig = $this->grav['twig'];
		foreach ($values as $key => $value) $twig->twig_vars[self::$slug . "_$key"] = $value;
	}

	##### EVENT functions - end #####


	##### EXTENDING functions - start #####

	/**
	 * this function is used to get the translated text based on the key
	 * @param string $key
	 * @return string the translated text
	 */
	function text(string $key): string
	{
		return $this->grav['language']->translate(strtoupper('THEME_' . self::$slug . '_' . Trapezes::key2 . ".$key"));
	}

	/**
	 * this function is used to provide the adequate translated label based on the given key
	 * it is loosely based on the function `{{ '_NAMESPACE_._KEY_'|t }}` in twig
	 * @param string $key s.e.
	 * @return string the adequate translated label
	 */
	function label(string $key): string
	{
		$key0 = strtolower($key);
		return
			isset(self::$labels[$key0]) && ($label = self::$labels[$key0]) ?
			strip_tags($label, Utils::allowableTags) :
			$this->text("LABELS_TEXTS_$key");
	}

	/**
	 * this function is used to provide the adequate translated label based on the given **cookies** suffix
	 * @param string $key s.e.
	 * @return string the adequate translated label
	 */
	function labelC(string $key): string
	{
		return $this->label(Cookies::key . "_$key");
	}

	/**
	 * this function is used to provide the url to a given resource
	 * I wrote this function because in twigs at times '.url' works, at times '.path' works
	 * 
	 * @example ``twig`` {% set url = __RESOURCE__.url ?: base_url_simple ~'/'~ __RESOURCE__.path %}
	 * @see https://learn.getgrav.org/17/themes/theme-vars#base-url-simple-variable
	 * 
	 * @since PM (08.03.2021)
	 * @param object|array $resource s.e.
	 * @return string ideally the URL to the path
	 */
	function resource(/*object|array*/$resource): string
	{
		return is_object($resource) ? $resource->url() : $this->grav['base_url'] . '/' . $resource['path'];
	}

	/**
	 * this shortcode is used to list the current subscribers in a trapeze
	 * @param string $format {'Y-m-d H:i:s'} 
	 * 											 the format of the opt-in date
	 * 											 @see https://www.php.net/manual/en/function.date for more info
	 * @return string @see ./core/Trapezes::block
	 */
	function subscribers(string $format = 'Y-m-d H:i:s'): string
	{
		return $this->trapezes->block(
			$this->subscribers->addresses(
				fn (string $key): string => $this->text($key),
				$format,
				fn (string $name, array $config) => $this->grav['twig']->processTemplate(
					'partials/' . Trapezes::key2 . '/' . Subscribers::key . ".$name.html.twig",
					$config
				)
			),
			['class' => Subscribers::key]
		);
	}

	function errorNote(): string
	{
		return $this->trapezes->error();
	}

	##### EXTENDING functions - end #####


	private function theme2design(): array
	{
		#$theme = ($event && isset($event['config']) ? $event['config'] : $this->grav['config'])->get('theme');
		$theme = $this->grav['config']->get('theme');
		if (isset($theme['labels_texts'])) {
			foreach ($theme['labels_texts'] as $labels) if ($labels['language'] == $this->lang) {
				self::$labels = $labels;
				break;
			}
		}
		// ...
		if (!isset(self::$labels['error'])) self::$labels['error'] = '';
		return $theme['design'] ?? [];
	}
}
