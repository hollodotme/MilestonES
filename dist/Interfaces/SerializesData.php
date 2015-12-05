<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface SerializesData
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface SerializesData
{
	/**
	 * @param mixed $data
	 *
	 * @return string
	 */
	public function serializeData( $data );

	/**
	 * @param string $serializedData
	 *
	 * @return mixed
	 */
	public function unserializeData( $serializedData );
}
