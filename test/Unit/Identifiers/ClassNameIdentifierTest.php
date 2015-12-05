<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Identifiers;

use hollodotme\MilestonES\ClassId;

class ClassNameIdentifierTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider classNameCanocicalProvider
	 */
	public function testClassNameIsRepresentedAsCanonicalString( $class_name, $expected_canonical )
	{
		$identifier = new ClassId( $class_name );

		$this->assertSame( $expected_canonical, $identifier->toString() );
		$this->assertSame( $expected_canonical, (string)$identifier );
	}

	public function classNameCanocicalProvider()
	{
		return [
			[ '\\Unit\\Test\\Class', 'Unit.Test.Class' ],
			[ 'Unit\\Test\\Class', 'Unit.Test.Class' ],
			[ '\\Unit_Test\\Class_Name', 'Unit_Test.Class_Name' ],
			[ \stdClass::class, 'stdClass' ],
			[ '___', '___' ],
			[ '\\__2\\___', '__2.___' ],
		];
	}

	/**
	 * @dataProvider invalidClassNameProvider
	 * @expectedException \hollodotme\MilestonES\Exceptions\IdentifierArgumentIsNotAClassName
	 */
	public function testConstructionFailsOnInvalidClassNames( $invalid_class_name )
	{
		new ClassId( $invalid_class_name );
	}

	public function invalidClassNameProvider()
	{
		return [
			[ '3Hundert' ],
			[ 'Unit\\Test-Class' ],
			[ '--' ],
			[ '0' ],
			[ '\\10\\Unit\\Test' ],
			[ 'Unit\\Test\\10' ],
			[ '' ],
			[ '\\\\\\' ],
			[ 'Unit\\\\Test\\Class\\Name' ],
			[ 'Unit\\Test\\Class\\Name\\' ],
		];
	}

	/**
	 * @dataProvider canonicalFqcnProvider
	 */
	public function testClassNameIsReconstitutedFromCanonical( $canonical, $expected_fqcn )
	{
		$identifier = ClassId::fromString( $canonical );

		$this->assertEquals( $expected_fqcn, $identifier->getFullQualifiedClassName() );
	}

	public function canonicalFqcnProvider()
	{
		return [
			[ 'Unit.Test.Class', '\\Unit\\Test\\Class' ],
			[ 'stdClass', '\\stdClass' ],
			[ 'Unit_Test.Class_Name', '\\Unit_Test\\Class_Name' ],
		];
	}

	/**
	 * @dataProvider fqcnBasenameProvider
	 */
	public function testClassBasenameIsExtractedFromFcqn( $fcqn, $expected_basename )
	{
		$identifier = new ClassId( $fcqn );

		$this->assertEquals( $expected_basename, $identifier->getClassBasename() );
	}

	public function fqcnBasenameProvider()
	{
		return [
			[ '\\Unit\\Test\\Class', 'Class' ],
			[ '\\stdClass', 'stdClass' ],
			[ '\\Unit_Test\\Class_Name', 'Class_Name' ],
		];
	}
}
