<?php

namespace Grav\Theme\tapmorph;

use Grav\Common\Twig\Twig;

/**
 * this class is used generate the .twig function related to the creation of the trapezes snippets
 * @final
 * @since 2021.01 PM (27.02.2021) standalone
 */
final class Trapezes
{
	const
		key = 'trapeze',
		key2 = self::key . 's',
		col = 'col-md-',
		add = self::key . '-additional-';
	private const
		container = 'container ',
		illus = 'illustration';
	private static $image = [];
	private
		$left = true, $columns = [6, 6], $text = '', $position = '', $radius = '',
		$counter = 0;
	private string $row;
	private Twig $twig;
	private string $error;


	/**
	 * this function is used to set the internal image configuration
	 * @param string[] ...$image the image configuration
	 */
	static function image(...$image)
	{
		self::$image = $image;
	}

	/**
	 * @wrapper
	 */
	static function fn(string $suffix, string $prefix = self::key): string
	{
		return Utils::fn($suffix, $prefix);
	}

	/**
	 * @constructor
	 * @param string $slug the project slug
	 * @param array $design the design values
	 * @param string $error the error notice
	 * @param Twig $twig the twig object
	 */
	function __construct(string $slug, array $design, string $error, Twig $twig)
	{
		$key = self::key;
		$this->row = "row $slug-row $key-row";
		if (isset($design['status']) && $design['status']) {
			if (isset($design['orientation'])) $this->left = $design['orientation'] == 1;
			if (isset($design[$slug = 'columns'])) $this->columns = explode(
				',',
				preg_replace("/(\d+)/", "$1 $key-$slug-$1", $design[$slug])
			);
			if (isset($design['text'])) $this->text = "$key-text-" . $design['text'] . " ";
			if (isset($design['image_position'])) $this->position = " $key-position-" . $design['image_position'];
			if (isset($design['image_radius'])) $this->radius = " $key-radius-" . $design['image_radius'];
		}
		$this->error = $error;
		$this->twig = $twig;
	}

	/**
	 * this function is used to generate the anchors block snippet
	 * @since 1.0.2 PM (05.03.2022) standalone
	 * @param array $anchors the HTML:a attributes
	 * 											 format: {
	 * 												 class: optional:__EMPTY_STRING__; the HTML:class attribute
	 * 												 id: optional:__EMPTY_STRING__; the HTML:id attribute
	 * 												 href: optional:__EMPTY_STRING__; the HTML:href attribute
	 * 												 target: optional:__INTELLIGENT_GUESS__; the HTML:target attribute
	 * 												 label: s.e.
	 * 											 }
	 * @param array $config {[]} the configuration object
	 * 											format: {
	 * 												class: optional:__EMPTY_STRING__; the HTML:class attribute
	 * 												id: optional:__EMPTY_STRING__; the HTML:id attribute
	 * 											}
	 * @return string the anchors snippet
	 */
	function anchors(array $anchors, array $config = []): string
	{
		// $mark = __FUNCTION__;
		list($class, $id) = $this->parseIdentifiers($config['class'] ?? '', $config['id'] ?? '');
		foreach ($anchors as &$anchor) {
			$anchor['class'] = isset($anchor['class']) ? 'button-' . preg_replace("/\W+/", "-", trim($anchor['class'])) : '';
			$anchor['href'] = trim($anchor['href'] ?? '');
			if (!isset($anchor['target'])) $anchor['target'] = (strpos($anchor['href'], 'https://') === 0 ? '_blank' : '_self');
		}

		return $this->processTemplate(__FUNCTION__, compact('class', 'id', 'anchors'));
	}

	/**
	 * this function is used to generate the trapeze block snippet
	 * @since 2021.01 PM (28.11.2020) standalone
	 * @param string $block the HTML snippet representing the block
	 * @param array $config {[]} the configuration object
	 * 											format: {
	 * 												class: optional:__EMPTY_STRING__; the HTML:class attribute
	 * 												id: optional:__EMPTY_STRING__; the HTML:id attribute
	 * 												image: optional:__EMPTY_STRING__; the filename of the image to place in the background
	 * 												fixed: optional:TRUE; FALSE if the background image should NOT be fixed
	 * 												compact: optional:FALSE; set TRUE to have a compact text
	 * 											}
	 * @return string the trapeze snippet
	 */
	function block(string $content, array $config = []): string
	{
		$mark = __FUNCTION__;
		$key = self::key;
		$row = self::container . $this->row;
		$column = "col $key-center $key-$mark";
		if (isset($config['compact']) && $config['compact']) $column .= " $key-compact";
		list($class, $id) = $this->parseIdentifiers($config['class'] ?? '', $config['id'] ?? '');
		//DO NOT MERGE
		$class = "$key $key-" . ($this->left ? 'left' : 'right') . " $key-$mark $class";
		$this->left = !$this->left; //switch for the next round
		# html_entity_decode
		if (isset($config['image']) && ($image = $config['image'])) {
			$class .= " $key-" . self::illus . (isset($config['fixed']) && !$config['fixed'] ? '' : " $key-fixed");
			$image = $this->parseImage($image);

			return $this->processTemplate(
				"$mark.image",
				compact('id', 'class', 'row', 'column', 'content', 'image')
			);
		} else return $this->processTemplate(
			"$mark.text",
			compact('id', 'class', 'row', 'column', 'content')
		);
	}

	/**
	 * this function is used to generate the trapeze snippet
	 * @since 2021.01 PM (28.11.2020) standalone
	 * @param string $title s.e.
	 * @param string $content s.e.
	 * @param array $config {[]} the configuration object
	 * 											format: {
	 * 												class: optional:__EMPTY_STRING__; the HTML:class attribute
	 * 												id: optional:__EMPTY_STRING__; the HTML:id attribute
	 * 												image: optional:__EMPTY_STRING__; the filename of the image
	 * 												background: optional:FALSE; set to TRUE if the image should be in the background
	 * 												fixed: optional:TRUE; set to FALSE if the background image should NOT be fixed
	 * 												compact: optional:FALSE; set TRUE to have a compact text
	 * 											}
	 * @return string the trapeze snippet
	 */
	function paragraph(string $title, string $content, array $config = []): string
	{
		$mark = __FUNCTION__;
		$key = self::key;
		$row = self::container . $this->row;
		list($class, $id) = $this->parseIdentifiers($config['class'] ?? '', $config['id'] ?? "${key}_$title");
		//DO NOT MERGE
		$class = "$key $key-" . ($this->left ? 'left' : 'right') . " $key-$mark $class";
		//DO NOT SWITCH
		$this->left = !$this->left; //switch for the next round
		list($title, $content) = $this->parseTexts($title, $content);

		if (isset($config['image']) && ($image = $config['image'])) {
			$class .= " $key-minimum";
			$image = $this->parseImage($image);
			$config2 = compact('id', 'row', 'image', 'title', 'content');

			if (isset($config['background']) && $config['background']) {
				return $this->processTemplate(
					"$mark.image.background",
					$config2 + [
						'class' => "$class $key-" . self::illus . (isset($config['fixed']) && !$config['fixed'] ? '' : " $key-fixed"),
						'column_text' => "col $key-center " . (isset($config['compact']) && $config['compact'] ? "$key-compact" : ''),
					]
				);
			} else {
				$col = self::col;
				$img = " $key-image" . $this->position . $this->radius;
				return $this->processTemplate(
					"$mark.image.basic",
					$config2 + [
						'class' => $class,
						'column_image' => !$this->left ? [ //"!$this->left" is on purpose, the switch has already taken place
							$col . $this->columns[1] . $img,
						] : [
							$col . $this->columns[1] . "$img $key-mobile",
							$col . $this->columns[1] . "$img $key-desktop",
						],
						'column_text' => $col . $this->columns[0] . " $key-text",
					]
				);
			}
		} else return $this->processTemplate(
			"$mark.text",
			compact('id', 'class', 'row', 'title', 'content') + [
				'column_text' => "col $key-center " . (isset($config['compact']) && $config['compact'] ? "$key-compact" : ""),
			]
		);
	}

	/**
	 * this function is used to generate the trapeze snippet containing a slideshow
	 * @since 2021.01 PM (27.11.2020) standalone
	 * @see https://getbootstrap.com/docs/4.5/components/carousel/#with-indicators
	 * @see https://getbootstrap.com/docs/4.5/components/carousel/#individual-carousel-item-interval
	 * @see https://stackoverflow.com/a/55400179/2652918
	 * @param array $data the list of data used to build the carousel
	 * 										format: [
	 * 											{
	 * 												image: the image filename,
	 * 												title: the title,
	 * 												content: the content,
	 * 											},
	 * 											...
	 * 										]
	 * @param array $config {[]} the configuration object
	 * 											format: {
	 * 												class: optional:__EMPTY_STRING__; the HTML:class attribute
	 * 												id: optional:__EMPTY_STRING__; the HTML:id attribute
	 * 												image: optional:__EMPTY_STRING__; the filename of the image to place in the background
	 * 												fixed: optional:TRUE; set to FALSE if the background image should NOT be fixed
	 * 												interval: optional:FALSE; 
	 * 																	the amount in milliseconds of time to delay between automatically switch to the next item
	 * 																	omit the key or set to 0 or set to FALSE to prevent the automatic switch
	 * 												fade: optional:FALSE; set to TRUE to switch by fading instead of sliding
	 * 												indicators: optional:TRUE; the navigation at the bottom; set FALSE to omit them
	 * 												controls: optional:TRUE; the navigation left & right; set FALSE to omit them
	 * 												compact: optional:FALSE; set TRUE to have a compact text
	 * 											}
	 * @return string the trapeze snippet
	 */
	function carousel(array $data, array $config = []): string
	{
		$mark = __FUNCTION__;
		$key = self::key;
		list($class, $id) = $this->parseIdentifiers($config['class'] ?? '', $config['id'] ?? "${key}_${mark}_" . ($this->counter++));
		$img = " $key-image" . $this->position . $this->radius;
		if ($this->left) {
			$direction = 'left';
			$image = [
				self::col . $this->columns[1] . $img,
			];
		} else {
			$direction = 'right';
			$image = [
				self::col . $this->columns[1] . "$img $key-mobile",
				self::col . $this->columns[1] . "$img $key-desktop",
			];
		}
		// DO NOT SWITCH
		$class = "$key $key-$direction $key-$mark $class"; //extend the container class;
		$indicators = [];
		$items = [];
		$classes = [
			'class_block' => "col $key-center $key-block", //less performant but easier to debug
			'class_text' => self::col . $this->columns[0] . " $key-text",
			'class_image' => $image,
		];
		$active = 'active';
		$current = 'true';
		foreach ($data as $i => $item) {
			list($title, $content) = $this->parseTexts($item['title'] ?? '', $item['content'] ?? '');
			$indicators[] = compact('i', 'active', 'current');
			$items[] = compact('title', 'content', 'active') + $classes + [
				'interval' => isset($item['interval']) ? 'data-bs-interval="' . $item['interval'] . '"' : '',
				'class_center' => "col $key-center" . (isset($item['compact']) && $item['compact'] ? " $key-compact" : ''),
				'block' => $item['block'] ?? false,
				'image' => isset($item['image']) && $item['image'] ? $this->parseImage($item['image']) : '',
			];
			// DO NOT SWITCH
			$active = '';
			$current = 'false';
		}
		//...
		if (isset($config['image']) && ($image = $config['image'])) {
			$class .= " $key-" . self::illus . (!isset($config['fixed']) || $config['fixed'] ? " $key-fixed" : '');
			$image = $this->parseImage($image);
		} else $image = '';
		//DO NOT SWITCH
		$this->left = !$this->left; //switch for the next round
		return $this->processTemplate( //the carousel is too complexe to easily split & maintain more than one template
			$mark,
			compact('key', 'id', 'class', 'image', 'mark', 'items') + [
				'row' => $this->row,
				'fade' => isset($config['fade']) && $config['fade'] === true ? "$mark-fade" : '',
				// 'interval' => $config['interval'] ?? 'true',
				'interval' => isset($config['interval']) ? 'data-bs-interval="'. $config['interval'] .'"' : '',
				'touch' => isset($config['touch']) ? 'data-bs-touch="'. $config['touch'] .'"' : '',
				'indicators' => !isset($config['indicators']) || $config['indicators'] ? $indicators : [],
				'controls' => $config['controls'] ?? true, //TRUE if unset or not false
				// 'class_block' => "col $key-center $key-block"
			]
		);
	}

	/**
	 * this function is used to generate the trapeze error snippet
	 * @since 2021.01 PM (26.12.2020)
	 * @return string the trapeze snippet
	 */
	function error(): string
	{
		$mark = __FUNCTION__;
		$key = self::key;
		list($class, $id) = $this->parseIdentifiers('', '');
		$class = "$key $key-" . ($this->left ? 'left' : 'right') . " $key-block $key-$mark " . $this->text . " $class";
		$this->left = !$this->left; //switch for the next round

		if ($this->error) {
			return $this->processTemplate(
				"$mark.custom",
				compact('class', 'id') + [
					'class_row' => self::container . $this->row,
					'class_col' => "col $key-center $key-compact $key-block",
					'content' => strip_tags($this->error, Utils::allowableTags),
				]
			);
		} else {
			$icons = [
				'🤔', '🤨', '😑', '🙄', '😣', '🤐', '😫', '😒', '😕', '🙃', '😖', '😟', '😂', '😅',
				'😆', '🙁', '😤', '😩', '😬', '🤪', '😵', '😠', '🤐', '😷', '🤒', '🤕', '🤣', '😥',
				'🛑', '⛔', '🚫'
			];
			$config = compact('class', 'id') + [
				'class_icon' => "$mark-icon",
				'icon' => $icons[rand(0, count($icons) - 1)],
			];

			return $this->processTemplate("$mark.default", $config); //this won't work here because the error is invoked at the system level
		}
	}


	/**
	 * this function is used parse the identifiers
	 * @since 2021.01 PM (28.11.2020) standalone
	 * @param array|string $class
	 * @param string $id
	 * @return array
	 */
	private function parseIdentifiers(/*array|string*/$class, string $id): array
	{
		$regex = "/\W+/";
		if (is_string($class)) $class = self::add . preg_replace($regex, '-', $class);
		else $class = implode(
			' ',
			array_map(
				function ($class) use ($regex) {
					return $class ? self::add . preg_replace($regex, '-', $class) : '';
				},
				$class
			)
		);

		return [$this->text . $class, preg_replace($regex, '_', trim($id))];
	}

	/**
	 * this function is used parse the image
	 * @since 2021.01 PM (28.11.2020) standalone
	 * @see https://learn.getgrav.org/17/themes/theme-vars#path
	 * @see https://learn.getgrav.org/17/themes/theme-vars#permalink
	 * @see https://learn.getgrav.org/17/themes/theme-vars#base-url-simple-variable
	 * @see https://learn.getgrav.org/17/themes/theme-vars#rooturl-include_host-false
	 * @source system\src\Grav\Common\Twig\Twig.php #base_url_simple
	 * @param string $image the image filename
	 * @return string representing the image URL or the placeholder URL if the initial couldn't be found
	 */
	private function parseImage(string $image): string
	{
		if (is_file(self::$image[0] . "/" . ($image = trim($image)))) return self::$image[1] . "/$image";
		else return self::$image[2];
	}

	/**
	 * this function is used to clean & parse the trapeze descriptive data
	 * @version 2021.01 @since PM (27.11.2020) standalone
	 * @see https://www.php.net/manual/en/function.strip-tags.php
	 * @param string $title s.e.
	 * @param string $content s.e.
	 * @return array the parsed data; format list(title, content, anchors)
	 */
	private function parseTexts(string $title, string $content): array
	{
		return [
			strip_tags(trim($title), Utils::allowableTags),
			trim($content),
			// preg_replace('@(^<pre>\s*<code>|</code>\s*</pre>$)@', '', trim($content))
		];
	}

	/**
	 * 
	 */
	private function processTemplate(string $name, array $config): string
	{
		return $this->twig->processTemplate('partials/' . self::key2 . "/$name.html.twig", $config);
		// return new \Twig_Markup(
		// 	$this->twig->processTemplate('partials/' . self::key2 . "/$name.html.twig", $config),
		// 	'utf-8'
		// );
	}
}
