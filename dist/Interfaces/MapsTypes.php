<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface MapsTypes
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface MapsTypes
{
	/**
	 * @param string $mapping_value
	 *
	 * @return string
	 */
	public function getMappedType( $mapping_value );

	/**
	 * @param string $mapped_type
	 *
	 * @return string
	 */
	public function getMappingValue( $mapped_type );
}
