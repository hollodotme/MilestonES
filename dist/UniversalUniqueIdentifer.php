<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\GeneratesIdentifier;
use Rhumsaa\Uuid\Uuid;

/**
 * Class UniversalUniqueIdentifer
 *
 * @package hollodotme\MilestonES
 */
abstract class UniversalUniqueIdentifer extends Identifier implements GeneratesIdentifier
{
	/**
	 * @return static
	 */
	public static function generate()
	{
		$uuid = Uuid::uuid4()->toString();

		return new static( $uuid );
	}
}
