<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface IdentityMap
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface IdentityMap
{
	/**
	 * @param IsIdentified $identified_object
	 */
	public function attach( IsIdentified $identified_object );

	/**
	 * @param Identifies $id
	 *
	 * @return IsIdentified
	 */
	public function find( Identifies $id );

	/**
	 * @param Identifies $id
	 *
	 * @return bool
	 */
	public function isAttached( Identifies $id );
}
