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
	public function serializeData( $data )
	{
		return serialize( $data );
	}

	/**
	 * @param string $serializedData
	 *
	 * @return mixed
	 */
	public function unserializeData( $serializedData )
	{
		return unserialize( $serializedData );
	}
}
