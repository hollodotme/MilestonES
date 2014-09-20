<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface SerializesData
 * @package hollodotme\MilestonES\Interfaces
 */
interface SerializesData
{
	/**
	 * @param \stdClass $data
	 *
	 * @return string
	 */
	public function serializeData( \stdClass $data );

	/**
	 * @param string $serialized_data
	 *
	 * @return \stdClass
	 */
	public function unserializeData( $serialized_data );
}
