<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

require_once __DIR__ . '/_test_classes/Enum.php';
require_once __DIR__ . '/_test_classes/TestEnum.php';

class EnumTest extends \PHPUnit_Framework_TestCase
{
	public function testDefaultValueIsSetOnEmptyConstrution()
	{
		$enum = new TestEnum();

		$this->assertEquals( '', $enum );
		$this->assertTrue( $enum->equals( null ) );
		$this->assertTrue( $enum->equals( TestEnum::NULL_TEST ) );
		$this->assertEquals( null, $enum->getDefaultValue() );
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testConstructionFailsOnInvalidValue()
	{
		new TestEnum( 'An invalid value' );
	}

	public function testCanExposeAllPossibleValues()
	{
		$enum = new TestEnum();

		$expected_values = [
			TestEnum::STRING_TEST,
			TestEnum::INT_TEST,
			TestEnum::FLOAT_TEST,
			TestEnum::FALSE_TEST,
			TestEnum::TRUE_TEST,
			TestEnum::NULL_TEST,
		];

		$this->assertEquals( $expected_values, $enum->getValues() );
	}

	/**
	 * @dataProvider enumValueProvider
	 */
	public function testExposesValueGivenOnConstruction( $init_value, $expected_value )
	{
		$enum = new TestEnum( $init_value );

		$this->assertTrue( $enum->equals( $expected_value ) );
	}

	public function enumValueProvider()
	{
		return [
			[null, TestEnum::NULL_TEST],
			[TestEnum::STRING_TEST, TestEnum::STRING_TEST],
			['Unit.Test', TestEnum::STRING_TEST],
			[TestEnum::FLOAT_TEST, TestEnum::FLOAT_TEST],
			[123.45, TestEnum::FLOAT_TEST],
			[TestEnum::INT_TEST, TestEnum::INT_TEST],
			[12345, TestEnum::INT_TEST],
			[false, TestEnum::FALSE_TEST],
			[true, TestEnum::TRUE_TEST],
		];
	}

	/**
	 * @dataProvider typeSensitiveProvider
	 */
	public function testEqualsIsTypeSesnsitive( $init_value, $other_value )
	{
		$enum = new TestEnum( $init_value );

		$this->assertFalse( $enum->equals( $other_value ) );
	}

	public function typeSensitiveProvider()
	{
		return [
			[123.45, '123.45'],
			[12345, '12345'],
			[false, 0],
			[false, ''],
			[true, 1],
			[null, false],
			[null, 0],
			[null, ''],
		];
	}
}
 