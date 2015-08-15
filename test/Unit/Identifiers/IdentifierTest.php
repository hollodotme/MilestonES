<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Identifiers;

use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestIdentifier;

class IdentifierTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider idStringProvider
	 */
	public function testRepresentsAString( $id )
	{
		$identifier = new Identifier( $id );

		$this->assertSame( $id, $identifier->toString() );
		$this->assertSame( $id, (string)$identifier );
	}

	public function idStringProvider()
	{
		return [
			[ '12345' ],
			[ 'null' ],
			[ '0' ],
			[ 'test_id' ],
			[ 'abcdefg-847584-ufhusdgf-83645' ],
		];
	}

	/**
	 * @dataProvider idNumberProvider
	 */
	public function testNumbersWillBeConvertedToStrings( $number, $expected_string )
	{
		$identifier = new Identifier( $number );

		$this->assertSame( $expected_string, $identifier->toString() );
		$this->assertSame( $expected_string, (string)$identifier );
	}

	public function idNumberProvider()
	{
		return [
			[ 12345, '12345' ],
			[ 0, '0' ],
			[ 12.56, '12.56' ],
			[ -12, '-12' ],
		];
	}

	/**
	 * @dataProvider noScalarsProvider
	 * @expectedException \hollodotme\MilestonES\Exceptions\IdentifierArgumentIsNotScalar
	 */
	public function testNonScalarsAsIdWillThrowAnException( $expression )
	{
		new Identifier( $expression );
	}

	public function noScalarsProvider()
	{
		return [
			[ false ],
			[ null ],
			[ true ],
			[ new \stdClass() ],
			[ [ ] ],
		];
	}

	/**
	 * @dataProvider idStringProvider
	 */
	public function testCanBeConstructedFromString( $id_string )
	{
		$identifier = Identifier::fromString( $id_string );

		$this->assertInstanceOf( Identifier::class, $identifier );
		$this->assertSame( $id_string, $identifier->toString() );
		$this->assertSame( $id_string, (string)$identifier );
	}

	/**
	 * @dataProvider idNumberProvider
	 */
	public function testCanBeContructedFromNumbers( $number, $expected_string )
	{
		$identifier = Identifier::fromString( $number );

		$this->assertInstanceOf( Identifier::class, $identifier );
		$this->assertSame( $expected_string, $identifier->toString() );
		$this->assertSame( $expected_string, (string)$identifier );
	}

	/**
	 * @dataProvider noScalarsProvider
	 * @expectedException \hollodotme\MilestonES\Exceptions\IdentifierArgumentIsNotScalar
	 */
	public function testCannotBeContructedFromExpressions( $expression )
	{
		Identifier::fromString( $expression );
	}

	/**
	 * @dataProvider idEqualsIdentifierProvider
	 */
	public function testEquals( $id, Identifier $other )
	{
		$identifier = new Identifier( $id );

		$this->assertTrue( $identifier->equals( $other ) );
		$this->assertTrue( $other->equals( $identifier ) );
	}

	public function idEqualsIdentifierProvider()
	{
		return [
			[ '12345', new Identifier( '12345' ) ],
			[ '12345', new Identifier( 12345 ) ],
			[ 12345, new Identifier( '12345' ) ],
			[ 12345, new Identifier( 12345 ) ],
			[ 'NULL', new Identifier( 'NULL' ) ],
			[ 'FALSE', new Identifier( 'FALSE' ) ],
			[ 'TRUE', new Identifier( 'TRUE' ) ],
			[ '0', new Identifier( '0' ) ],
			[ '0', new Identifier( 0 ) ],
			[ 0, new Identifier( '0' ) ],
			[ 0, new Identifier( 0 ) ],
			[ 'test_id', new Identifier( 'test_id' ) ],
			[ 'abcdefg-847584-ufhusdgf-83645', new Identifier( 'abcdefg-847584-ufhusdgf-83645' ) ],
			[ 12.56, new Identifier( '12.56' ) ],
			[ 12.56, new Identifier( 12.56 ) ],
			[ .8, new Identifier( 0.8 ) ],
			[ '12.56', new Identifier( 12.56 ) ],
			[ '12.56', new Identifier( '12.56' ) ],
			[ -12, new Identifier( '-12' ) ],
			[ -12, new Identifier( -12 ) ],
			[ '-12', new Identifier( -12 ) ],
			[ '-12', new Identifier( '-12' ) ],
		];
	}

	public function testEqualsFailsOnDifferentClasses()
	{
		$identifier = new Identifier( 'Unit.Test.ID' );
		$other      = new TestIdentifier( 'Unit.Test.ID' );

		// Note: class name is explicitly checked, so even inherited classes will fail
		$this->assertInstanceOf( Identifier::class, $other );

		$this->assertFalse( $identifier->equals( $other ) );
		$this->assertFalse( $other->equals( $identifier ) );
	}

	/**
	 * @dataProvider idStringEqualsFailesProvider
	 */
	public function testEqualsFailsOnDifferentIdString( $id_string, $other_id_string )
	{
		$identifier = new Identifier( $id_string );
		$other      = new Identifier( $other_id_string );

		$this->assertFalse( $identifier->equals( $other ) );
		$this->assertFalse( $other->equals( $identifier ) );
	}

	public function idStringEqualsFailesProvider()
	{
		return [
			[ 'Unit-Test-ID', 'unit-test-id' ],
			[ 'Unit-Test-ID', 'test-id' ],
			[ 'Unit-Test-ID', '' ],
			[ .8, 0.9 ],
		];
	}

	public function testRepresentableAsJson()
	{
		$id = new Identifier( 'Unit-Test-ID' );

		$json = json_encode( [ 'id' => $id ] );

		$this->assertJsonStringEqualsJsonString( '{"id": "Unit-Test-ID"}', $json );
	}
}
