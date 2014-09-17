<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Serializers;

use hollodotme\MilestonES\Interfaces\SerializesData;

/**
 * Class PhpSerializer
 * @package hollodotme\MilestonES\Serializers
 */
class PhpSerializer implements SerializesData
{
	/**
	 * @param mixed $data
	 *
	 * @return string
	 */
	public function serializeData( $data )
	{
		return serialize( $data );
	}

	/**
	 * @param string $serialized_data
	 *
	 * @return mixed
	 */
	public function unserializeData( $serialized_data )
	{
		return unserialize( $serialized_data );
	}
}