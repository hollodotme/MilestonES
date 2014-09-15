<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface MapsIdentities
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface MapsIdentities
{
	/**
	 * @param HasIdentity $identified_object
	 */
	public function attach( HasIdentity $identified_object );

	/**
	 * @param Identifies $id
	 *
	 * @return HasIdentity
	 */
	public function find( Identifies $id );

	/**
	 * @param Identifies $id
	 *
	 * @return bool
	 */
	public function isAttached( Identifies $id );
}
