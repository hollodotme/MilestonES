<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface KeepsARole
 * @package hollodotme\MilestonES\Interfaces
 */
interface KeepsARole
{
	/**
	 * @param ActsAsRole $role
	 */
	public function setRole( ActsAsRole $role );

	/**
	 * @return ActsAsRole
	 */
	public function getRole();

	/**
	 * @return bool
	 */
	public function hasRole();

	public function removeRole();

	/**
	 * @param Identifies $role_id
	 *
	 * @return bool
	 */
	public function hasRoleWithId( Identifies $role_id );
}