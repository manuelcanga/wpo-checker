<?php

namespace Trasweb\Plugins\WpoChecker\Collections;

use Countable;
use Iterator;
use Trasweb\Plugins\WpoChecker\Entities\Site;

/**
 * Class Sites.
 *
 * @package Collections
 */
final class Sites implements Iterator, Countable {
	/**
	 * @var array
	 */
	private $wpo_sites;
	/**
	 * @var int
	 */
	private $current_site;

	/**
	 * Sites constructor.
	 *
	 * @param array $sites
	 */
	final public function __construct( array $sites ) {
		$this->rewind();
		$this->wpo_sites = array_values( $sites );
	}

	/**
	 * Rewind the Iterator to the first element.
	 *
	 * @link  https://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	final public function rewind() {
		$this->current_site = 0;
	}

	/**
	 * Return the current element.
	 *
	 * @link  https://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	final public function current() {
		return new Site( $this->key(), $this->get_current_site() );
	}

	/**
	 * Return the key of the current element.
	 *
	 * @link  https://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	final public function key() {
		return $this->get_current_site()['id'];
	}

	/**
	 * Retrieve current site in loop.
	 *
	 * @return array
	 */
	final private function get_current_site(): array {
		return $this->wpo_sites[ $this->current_site ] ?? [];
	}

	/**
	 * Move forward to next element.
	 *
	 * @link  https://php.net/manual/en/iterator.next.php
	 * @return void
	 * @since 5.0.0
	 */
	final public function next(): void {
		$this->current_site ++;
	}

	/**
	 * Checks if current position is valid.
	 *
	 * @link  https://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	final public function valid(): bool {
		return ! empty( $this->get_current_site() );
	}

	/**
	 * Retrieve count of sites.
	 *
	 * @return int
	 */
	public function count(): int {
		return count( $this->wpo_sites );
	}
}
