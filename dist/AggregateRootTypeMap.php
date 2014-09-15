<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\MappedTypeDoesNotExist;
use hollodotme\MilestonES\Exceptions\MappingValueDoesNotExist;
use hollodotme\MilestonES\Exceptions\TypeMapContainsMultipleMappedType;
use hollodotme\MilestonES\Exceptions\TypeMapContainsNonStrings;
use hollodotme\MilestonES\Interfaces\MapsTypes;

/**
 * Class AggregateRootTypeMap
 *
 * @package hollodotme\MilestonES
 */
final class AggregateRootTypeMap implements MapsTypes
{

	/** @var array */
	private $map;

	/**
	 * @param array $map
	 */
	public function __construct( array $map )
	{
		$this->map = $map;

		$this->guardUniquenessAndStrings();
	}

	/**
	 * @param string $mapping_value
	 *
	 * @throws MappingValueDoesNotExist
	 * @return string
	 */
	public function getMappedType( $mapping_value )
	{
		if ( $this->mappingValueExists( $mapping_value ) )
		{
			return $this->getMappedTypeForMappingValue( $mapping_value );
		}
		else
		{
			throw new MappingValueDoesNotExist( $mapping_value );
		}
	}

	/**
	 * @param string $mapped_type
	 *
	 * @throws MappedTypeDoesNotExist
	 * @return string
	 */
	public function getMappingValue( $mapped_type )
	{
		if ( $this->mappedTypeExists( $mapped_type ) )
		{
			return $this->getMappingValueForMappedType( $mapped_type );
		}
		else
		{
			throw new MappedTypeDoesNotExist( $mapped_type );
		}
	}

	/**
	 * @param string $mapping_value
	 *
	 * @return bool
	 */
	private function mappingValueExists( $mapping_value )
	{
		return array_key_exists( $mapping_value, $this->map );
	}

	/**
	 * @param string $mapping_value
	 *
	 * @return string
	 */
	private function getMappedTypeForMappingValue( $mapping_value )
	{
		return $this->map[ $mapping_value ];
	}

	/**
	 * @param string $mapped_type
	 *
	 * @return bool
	 */
	private function mappedTypeExists( $mapped_type )
	{
		return in_array( $mapped_type, $this->map );
	}

	/**
	 * @param string $mapped_type
	 *
	 * @return string
	 */
	private function getMappingValueForMappedType( $mapped_type )
	{
		return array_flip( $this->map )[ $mapped_type ];
	}

	/**
	 * @throws TypeMapContainsMultipleMappedType
	 * @throws TypeMapContainsNonStrings
	 */
	private function guardUniquenessAndStrings()
	{
		$this->guardUniqueness();
		$this->guardStrings();
	}

	/**
	 * @throws TypeMapContainsMultipleMappedType
	 */
	private function guardUniqueness()
	{
		$unique_keys   = array_unique( array_keys( $this->map ) );
		$unique_values = array_unique( array_values( $this->map ) );

		if ( count( $unique_keys ) != count( $unique_values ) )
		{
			throw new TypeMapContainsMultipleMappedType();
		}
	}

	/**
	 * @throws TypeMapContainsNonStrings
	 */
	private function guardStrings()
	{
		foreach ( $this->map as $mapping_value => $mapped_type )
		{
			$this->guardString( $mapping_value );
			$this->guardString( $mapped_type );
		}
	}

	/**
	 * @param mixed $value
	 *
	 * @throws TypeMapContainsNonStrings
	 */
	private function guardString( $value )
	{
		if ( !is_string( $value ) )
		{
			throw new TypeMapContainsNonStrings();
		}
	}
}
