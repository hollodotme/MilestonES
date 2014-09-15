<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\AggregateRoot;

use hollodotme\MilestonES\AggregateRootTypeMap;

class AggregateRootTypeMapTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::__construct
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::guardUniquenessAndStrings
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::guardUniqueness
	 * @expectedException \hollodotme\MilestonES\Exceptions\TypeMapContainsMultipleMappedType
	 */
	public function testThrowsExceptionWhenTypeIsMappedMultiple()
	{
		$map = array(
			'Unit\\Test\\Mapping' => 'Unit.Test',
			'Unit\\Test\\Value'   => 'Unit.Test',
		);

		new AggregateRootTypeMap( $map );
	}

	/**
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::__construct
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::guardUniquenessAndStrings
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::guardStrings
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::guardString
	 * @expectedException \hollodotme\MilestonES\Exceptions\TypeMapContainsNonStrings
	 */
	public function testThrowsExceptionWhenMapContainsNonStringTypes()
	{
		$map = array(
			'Unit\\Test\\Mapping' => array( 'Unit.Test' ),
		);

		new AggregateRootTypeMap( $map );
	}

	/**
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::__construct
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::guardUniquenessAndStrings
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::guardStrings
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::guardString
	 * @expectedException \hollodotme\MilestonES\Exceptions\TypeMapContainsNonStrings
	 */
	public function testThrowsExceptionWhenMapContainsNonStringKeys()
	{
		$map = array( 1 => 'Unit.Test' );

		new AggregateRootTypeMap( $map );
	}

	/**
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::getMappingValue
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::mappingValueExists
	 * @expectedException \hollodotme\MilestonES\Exceptions\MappedTypeDoesNotExist
	 */
	public function testThrowsExceptionWhenMappedTypeDoesNotExist()
	{
		$type_map = new AggregateRootTypeMap( array() );

		$type_map->getMappingValue( 'Unit.Test' );
	}

	/**
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::getMappedType
	 * @covers \hollodotme\MilestonES\AggregateRootTypeMap::mappedTypeExists
	 * @expectedException \hollodotme\MilestonES\Exceptions\MappingValueDoesNotExist
	 */
	public function testThrowsExceptionWhenMappingValueDoesNotExist()
	{
		$type_map = new AggregateRootTypeMap( array() );

		$type_map->getMappedType( 'Unit\\Test' );
	}
}
