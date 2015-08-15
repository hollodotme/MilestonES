<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\Interfaces\SerializesData;

/**
 * Class UnitTestSerializer
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class UnitTestSerializer implements SerializesData
{
	/**
	 * @param mixed $data
	 *
	 * @return string
	 */
	public function serializeData( $data )
	{
		return $data;
	}

	/**
	 * @param string $serializedData
	 *
	 * @return mixed
	 */
	public function unserializeData( $serializedData )
	{
		return $serializedData;
	}
}