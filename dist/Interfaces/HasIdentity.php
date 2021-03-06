<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface HasIdentity
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface HasIdentity
{
	/**
	 * @return IdentifiesObject
	 */
	public function getIdentifier();
}
