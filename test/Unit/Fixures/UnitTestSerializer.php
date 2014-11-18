<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Test\Unit;

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
	 * @param string $serialized_data
	 *
	 * @return mixed
	 */
	public function unserializeData( $serialized_data )
	{
		return $serialized_data;
	}
}