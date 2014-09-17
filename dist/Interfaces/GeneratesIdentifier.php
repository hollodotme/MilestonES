<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface GeneratesIdentifier
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface GeneratesIdentifier
{
	/**
	 * @return Identifies
	 */
	public static function generate();
}
