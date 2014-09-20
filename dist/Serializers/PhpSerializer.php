<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Serializers;

use hollodotme\MilestonES\Interfaces\SerializesData;

/**
 * Class PhpSerializer
 *
 * @package hollodotme\MilestonES\Serializers
 */
class PhpSerializer implements SerializesData
{
	/**
	 * @param \stdClass $data
	 *
	 * @return string
	 */
	public function serializeData( \stdClass $data )
	{
		return serialize( $data );
	}

	/**
	 * @param string $serialized_data
	 *
	 * @return \stdClass
	 */
	public function unserializeData( $serialized_data )
	{
		return unserialize( $serialized_data );
	}
}
