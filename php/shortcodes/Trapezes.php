<?php

namespace Grav\Plugin\Shortcodes;

use Grav\Theme\tapmorph\{Trapezes, Utils};
use Thunder\Shortcode\Events;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Thunder\Shortcode\EventHandler\FilterRawEventHandler;

// @php-ignore
final class ShortcodeTrapezes extends Shortcode
{
	private Trapezes $trapezes;

	/**
	 * 
	 */
	function __construct(string $key, array $design, string $error)
	{
		// @php-ignore
		parent::__construct();
		$this->trapezes = new Trapezes($key, $design, $error, $this->twig);
	}

	/**
	 * @source system\src\Grav\Common\Twig\Twig.php
	 */
	function init()
	{
		$handlers = $this->shortcode->getHandlers(); //twig is pre processed
		// $handlers = $this->shortcode->getRawHandlers(); //twig is NOT pre processed
		// $this->twig->processString($sc->getContent()),
		$containers = [];

		$handlers->add( // anchors
			$containers[] = Trapezes::fn('anchors'),
			function (ShortcodeInterface $sc) use (&$containers): string {
				$this->check($sc, $containers);

				$id = $this->shortcode->getId($sc);
				$anchors = [];
				foreach ($this->shortcode->getStates($id) as $key => $state) $anchors[] = [
					'id' => "$id-$key",
					'label' => $state->getParameter('label', $state->getContent() ?: $this->flag()),
				] + $state->getParameters(); //class, href, target

				return $this->trapezes->anchors($anchors, $sc->getParameters());
			}
		);
		$handlers->add(
			Trapezes::fn('anchor'), //anchor: add items to anchors state using parent id
			function (ShortcodeInterface $sc) use (&$containers): string {
				$parent = $this->check($sc, $containers); //the anchor is not limited to the 'anchors' shortcode
				$this->shortcode->setStates($this->shortcode->getId($parent), $sc);
				return '';
			}
		);

		$handlers->add( // block
			$containers[] = Trapezes::fn('block'),
			fn (ShortcodeInterface $sc): string => $this->trapezes->block(
				$sc->getContent() ?: $this->flag(),
				$sc->getParameters()
			)
		);

		$handlers->add( // paragraph
			$containers[] = Trapezes::fn('paragraph'),
			fn (ShortcodeInterface $sc): string => $this->trapezes->paragraph(
				$sc->getParameter('title', $this->flag('TITLE')),
				$sc->getContent() ?: $this->flag(),
				$sc->getParameters()
			)
		);

		// carousel
		// @see https://github.com/getgrav/grav-plugin-shortcode-ui/blob/develop/classes/shortcodes/AccordionsShortcode.php
		// @see https://github.com/getgrav/grav-plugin-shortcode-ui/blob/develop/templates/partials/ui-accordion.html.twig
		$handlers->add(
			$carousel = Trapezes::fn('carousel'),
			function (ShortcodeInterface $sc): string {
				$id = $this->shortcode->getId($sc);
				$items = [];
				foreach ($this->shortcode->getStates($id) as $key => $state) $items[] = [
					'id' => "$id-$key", //dummy key:value
					$state->getParameter('title', $this->flag('TITLE')),
					'content' => $state->getContent() ?: $this->flag(),
				] + $state->getParameters();

				return $this->trapezes->carousel($items, $sc->getParameters());
			}
		);
		$handlers->add(
			$containers[] = Trapezes::fn('item', $carousel), //carousel item: add items to carousel state using parent id
			function (ShortcodeInterface $sc) use ($carousel): string {
				$parent = $this->check($sc, [$carousel]); // process only if the parent shortcode is `Trapezes::fn('carousel')`
				$this->shortcode->setStates($this->shortcode->getId($parent), $sc);
				return '';
			}
		);

		// error
		$handlers->add(
			Trapezes::fn('error'),
			fn (ShortcodeInterface $sc): string => $this->trapezes->error()
		);

		// this block is used to prevent child shortcode from being processed
		// @see https://github.com/getgrav/grav-plugin-shortcode-core#raw
		// @see https://github.com/getgrav/grav-plugin-shortcode-core/blob/develop/classes/shortcodes/RawShortcode.php
		// $this->shortcode->getEvents()->addListener(
		// 	Events::FILTER_SHORTCODES,
		// 	new FilterRawEventHandler($containers)
		// );
	}


	/**
	 * this function is used to prevent the use of the child shortcodes outside a container shortcodes
	 * @param ShortcodeInterface $sc
	 * @param array $containers
	 */
	private function check(ShortcodeInterface $sc, array $containers)
	{
		// @php-ignore
		if (!($parent = $sc->getParent()) || !in_array($parent->getName(), $containers))
			throw new \Exception(
				"The shortcode '" . $sc->getName() . "' can NOT be invoked outside a container shortcode (" .
					implode(', ', $containers)
					. ")",
				1
			);
		// ...
		return $parent;
	}

	/**
	 * 
	 */
	private function flag(string $text = 'CONTENT'): string
	{
		return "!?$text?!";
	}
}
