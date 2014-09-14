<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Identifiers;

use hollodotme\MilestonES\CanonicalIdentifier;

class AggregateTypeIdentifierTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @covers       hollodotme\MilestonES\AggregateTypeIdentifier::__construct
	 * @covers       hollodotme\MilestonES\AggregateTypeIdentifier::__toString
	 * @covers       hollodotme\MilestonES\AggregateTypeIdentifier::toString
	 * @dataProvider classNameCanonicalProvider
	 */
	public function testCanCreateCanonicalFromClassName( $class_name, $expected_canonical )
	{
		$id = new CanonicalIdentifier( $class_name );

		$this->assertEquals( $expected_canonical, $id->toString() );
		$this->assertEquals( $expected_canonical, (string)$id );
	}

	public function classNameCanonicalProvider()
	{
		return array(
			array( '\\Unit\\Test\\Class\\Name', 'Unit.Test.Class.Name' ),
			array( 'Unit\\Test\\Class\\Name\\', 'Unit.Test.Class.Name' ),
		);
	}

	/**
	 * @covers       hollodotme\MilestonES\AggregateTypeIdentifier::__toString
	 * @covers       hollodotme\MilestonES\AggregateTypeIdentifier::toString
	 * @covers       hollodotme\MilestonES\AggregateTypeIdentifier::fromString
	 * @covers       hollodotme\MilestonES\Identifier::fromString
	 * @dataProvider canonicalProvider
	 */
	public function testCanCreateFromCanonical( $canonical, $expected_canonical )
	{
		$id = CanonicalIdentifier::fromString( $canonical );

		$this->assertEquals( $expected_canonical, $id->toString() );
		$this->assertEquals( $expected_canonical, (string)$id );
	}

	public function canonicalProvider()
	{
		return array(
			array( 'Unit.Test.Class.Name', 'Unit.Test.Class.Name' ),
			array( '.Unit.Test.Class.Name.', 'Unit.Test.Class.Name' ),
		);
	}
}
