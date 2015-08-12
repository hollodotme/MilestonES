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
	 * @return IdentifiesObject
	 */
	public static function generate();
}
