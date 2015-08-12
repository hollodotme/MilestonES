<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

/**
 * Class Contract
 *
 * @package hollodotme\MilestonES
 */
final class Contract extends ClassId
{
	/**
	 * @param string|object $id
	 */
	public function __construct( $id )
	{
		if ( is_object( $id ) )
		{
			$id = get_class( $id );
		}

		parent::__construct( $id );
	}
}
