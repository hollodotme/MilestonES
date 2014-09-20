<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Serializers;

use hollodotme\MilestonES\Interfaces\SerializesData;

/**
 * Class JsonSerializer
 *
 * @package hollodotme\MilestonES\Serializers
 */
class JsonSerializer implements SerializesData
{
	/**
	 * @param \stdClass $data
	 *
	 * @return string
	 */
	public function serializeData( \stdClass $data )
	{
		return json_encode( $data );
	}

	/**
	 * @param string $serialized_data
	 *
	 * @return \stdClass
	 */
	public function unserializeData( $serialized_data )
	{
		return json_decode( $serialized_data );
	}
}
