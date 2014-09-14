<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface CommitsChanges
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface CommitsChanges
{
	public function commitChanges();

	/**
	 * @return bool
	 */
	public function hasUncommittedChanges();
}
