<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\GeneratesIdentifier;
use Rhumsaa\Uuid\Uuid;

/**
 * Class UUIdentifer
 *
 * @package hollodotme\MilestonES
 */
class UUIdentifer extends Identifier implements GeneratesIdentifier
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
