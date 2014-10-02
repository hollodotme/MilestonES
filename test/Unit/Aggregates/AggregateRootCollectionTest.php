<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Aggregates;

require_once __DIR__ . '/../_test_classes/TestAggregateRoot.php';
require_once __DIR__ . '/../_test_classes/TestIdentifier.php';

use hollodotme\MilestonES\AggregateRootCollection;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Test\Unit\TestAggregateRoot;
use hollodotme\MilestonES\Test\Unit\TestIdentifier;

class AggregateRootCollectionTest extends \PHPUnit_Framework_TestCase
{
	public function testCanAttachAggregateRoot()
	{
		$collection     = new AggregateRootCollection();
		$identifier     = new Identifier( 'Unit-Test-ID' );
		$aggregate_root = TestAggregateRoot::allocateWithId( $identifier );

		$collection->attach( $aggregate_root );

		$this->assertCount( 1, $collection );
		$this->assertTrue( $collection->isAttached( $aggregate_root ) );
		$this->assertTrue( $collection->idExists( $identifier ) );
	}

	public function testAttachingSameAggregateRootMoreThanOnceHasNoEffect()
	{
		$collection     = new AggregateRootCollection();
		$identifier     = new Identifier( 'Unit-Test-ID' );
		$aggregate_root = TestAggregateRoot::allocateWithId( $identifier );

		$collection->attach( $aggregate_root );
		$collection->attach( $aggregate_root );

		$this->assertCount( 1, $collection );
		$this->assertTrue( $collection->isAttached( $aggregate_root ) );
		$this->assertTrue( $collection->idExists( $identifier ) );
	}

	public function testTwoAggregateRootsWithDifferentIdentifierClassesButSameIdCanBeAttached()
	{
		$collection          = new AggregateRootCollection();
		$identifier          = new Identifier( 'Unit-Test-ID' );
		$test_identifier     = new TestIdentifier( 'Unit-Test-ID' );
		$aggregate_root      = TestAggregateRoot::allocateWithId( $identifier );
		$test_aggregate_root = TestAggregateRoot::allocateWithId( $test_identifier );

		$collection->attach( $aggregate_root );
		$collection->attach( $test_aggregate_root );

		$this->assertCount( 2, $collection );
		$this->assertTrue( $collection->isAttached( $aggregate_root ) );
		$this->assertTrue( $collection->isAttached( $test_aggregate_root ) );
		$this->assertTrue( $collection->idExists( $identifier ) );
		$this->assertTrue( $collection->idExists( $test_identifier ) );
	}

	public function testTwoAggregateRootsWithDifferentIdsCanBeAttached()
	{
		$collection          = new AggregateRootCollection();
		$identifier          = new Identifier( 'Unit-Test-ID-1' );
		$test_identifier     = new Identifier( 'Unit-Test-ID-X' );
		$aggregate_root      = TestAggregateRoot::allocateWithId( $identifier );
		$test_aggregate_root = TestAggregateRoot::allocateWithId( $test_identifier );

		$collection->attach( $aggregate_root );
		$collection->attach( $test_aggregate_root );

		$this->assertCount( 2, $collection );
		$this->assertTrue( $collection->isAttached( $aggregate_root ) );
		$this->assertTrue( $collection->isAttached( $test_aggregate_root ) );
		$this->assertTrue( $collection->idExists( $identifier ) );
		$this->assertTrue( $collection->idExists( $test_identifier ) );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\AggregateRootWithEqualIdIsAlreadyAttached
	 */
	public function testAttachingTwoAggregateRootsWithSameIdFails()
	{
		$collection           = new AggregateRootCollection();
		$identifier           = new Identifier( 'Unit-Test-ID' );
		$aggregate_root       = TestAggregateRoot::allocateWithId( $identifier );
		$other_aggregate_root = TestAggregateRoot::allocateWithId( $identifier );

		$collection->attach( $aggregate_root );
		$collection->attach( $other_aggregate_root );
	}

	public function testAggregateRootCanBeFoundByIdWhenAttached()
	{
		$collection     = new AggregateRootCollection();
		$identifier     = new Identifier( 'Unit-Test-ID' );
		$aggregate_root = TestAggregateRoot::allocateWithId( $identifier );

		$collection->attach( $aggregate_root );

		$found = $collection->find( new Identifier( 'Unit-Test-ID' ) );

		$this->assertSame( $aggregate_root, $found );
	}

	public function testAggregateRootsWithDifferentIdentifierClassesButSameIdCanBeFoundWhenAttached()
	{
		$collection          = new AggregateRootCollection();
		$identifier          = new Identifier( 'Unit-Test-ID' );
		$test_identifier     = new TestIdentifier( 'Unit-Test-ID' );
		$aggregate_root      = TestAggregateRoot::allocateWithId( $identifier );
		$test_aggregate_root = TestAggregateRoot::allocateWithId( $test_identifier );

		$collection->attach( $aggregate_root );
		$collection->attach( $test_aggregate_root );

		$found      = $collection->find( new Identifier( 'Unit-Test-ID' ) );
		$test_found = $collection->find( new TestIdentifier( 'Unit-Test-ID' ) );

		$this->assertSame( $aggregate_root, $found );
		$this->assertSame( $test_aggregate_root, $test_found );
		$this->assertNotSame( $found, $test_found );
		$this->assertNotSame( $aggregate_root, $test_aggregate_root );
		$this->assertFalse( $found->getIdentifier()->equals( $test_found->getIdentifier() ) );
	}

	public function testCanLoopOverCollectionMoreThanOnce()
	{
		$collection     = new AggregateRootCollection();
		$identifier     = new Identifier( 'Unit-Test-ID' );
		$aggregate_root = TestAggregateRoot::allocateWithId( $identifier );

		$collection->attach( $aggregate_root );

		echo "Loop 1:";
		foreach ( $collection as $key => $aggregate )
		{
			echo "\nKey: {$key}, ID: {$aggregate->getIdentifier()}";
		}

		echo "\nLoop 2:";
		foreach ( $collection as $key => $aggregate )
		{
			echo "\nKey: {$key}, ID: {$aggregate->getIdentifier()}";
		}

		$this->expectOutputString( "Loop 1:\nKey: 0, ID: Unit-Test-ID\nLoop 2:\nKey: 0, ID: Unit-Test-ID" );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\AggregateRootNotFound
	 */
	public function testFindingNotAttachedAggregateRootByIdFails()
	{
		$collection = new AggregateRootCollection();

		$collection->find( new Identifier( 'Unit-Test-ID' ) );
	}
}
 