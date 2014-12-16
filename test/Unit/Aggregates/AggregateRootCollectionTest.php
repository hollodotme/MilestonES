<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Aggregates;

require_once __DIR__ . '/../Fixures/UnitTestAggregate.php';
require_once __DIR__ . '/../Fixures/UnitTestAggregateDiff.php';
require_once __DIR__ . '/../Fixures/UnitTestAggregateOtherId.php';
require_once __DIR__ . '/../Fixures/TestIdentifier.php';

use hollodotme\MilestonES\AggregateRootCollection;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Test\Unit\TestIdentifier;
use hollodotme\MilestonES\Test\Unit\UnitTestAggregate;
use hollodotme\MilestonES\Test\Unit\UnitTestAggregateDiff;
use hollodotme\MilestonES\Test\Unit\UnitTestAggregateOtherId;

class AggregateRootCollectionTest extends \PHPUnit_Framework_TestCase
{
	public function testCanAttachAggregateRoot()
	{
		$collection     = new AggregateRootCollection();
		$identifier     = new Identifier( 'Unit-Test-ID' );
		$aggregate_root = UnitTestAggregate::schedule( 'Unit-Test' );

		$collection->attach( $aggregate_root );

		$this->assertCount( 1, $collection );
		$this->assertTrue( $collection->isAttached( $aggregate_root ) );
		$this->assertTrue( $collection->idExists( $identifier ) );
	}

	public function testAttachingSameAggregateRootMoreThanOnceHasNoEffect()
	{
		$collection     = new AggregateRootCollection();
		$identifier     = new Identifier( 'Unit-Test-ID' );
		$aggregate_root = UnitTestAggregate::schedule( 'Unit-Test' );

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
		$aggregate_root      = UnitTestAggregate::schedule( 'Unit-Test' );
		$aggregate_root_diff = UnitTestAggregateDiff::schedule( 'Unit-Test' );

		$collection->attach( $aggregate_root );
		$collection->attach( $aggregate_root_diff );

		$this->assertCount( 2, $collection );
		$this->assertTrue( $collection->isAttached( $aggregate_root ) );
		$this->assertTrue( $collection->isAttached( $aggregate_root_diff ) );
		$this->assertTrue( $collection->idExists( $identifier ) );
		$this->assertTrue( $collection->idExists( $test_identifier ) );
	}

	public function testTwoAggregateRootsWithDifferentIdsCanBeAttached()
	{
		$collection           = new AggregateRootCollection();
		$identifier           = new Identifier( 'Unit-Test-ID' );
		$other_identifier     = new Identifier( 'Unit-Test-ID-X' );
		$aggregate_root       = UnitTestAggregate::schedule( 'Unit-Test' );
		$other_aggregate_root = UnitTestAggregateOtherId::schedule( 'Unit-Test' );

		$collection->attach( $aggregate_root );
		$collection->attach( $other_aggregate_root );

		$this->assertCount( 2, $collection );
		$this->assertTrue( $collection->isAttached( $aggregate_root ) );
		$this->assertTrue( $collection->isAttached( $other_aggregate_root ) );
		$this->assertTrue( $collection->idExists( $identifier ) );
		$this->assertTrue( $collection->idExists( $other_identifier ) );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\AggregateRootWithEqualIdIsAlreadyAttached
	 */
	public function testAttachingTwoAggregateRootsWithSameIdFails()
	{
		$collection           = new AggregateRootCollection();
		$aggregate_root       = UnitTestAggregate::schedule( 'Unit-Test' );
		$other_aggregate_root = UnitTestAggregate::schedule( 'Unit-Test' );

		$collection->attach( $aggregate_root );
		$collection->attach( $other_aggregate_root );
	}

	public function testAggregateRootCanBeFoundByIdWhenAttached()
	{
		$collection     = new AggregateRootCollection();
		$identifier     = new Identifier( 'Unit-Test-ID' );
		$aggregate_root = UnitTestAggregate::schedule( 'Unit-Test' );

		$collection->attach( $aggregate_root );

		$found = $collection->find( $identifier );

		$this->assertSame( $aggregate_root, $found );
	}

	public function testAggregateRootsWithDifferentIdentifierClassesButSameIdCanBeFoundWhenAttached()
	{
		$collection          = new AggregateRootCollection();
		$identifier          = new Identifier( 'Unit-Test-ID' );
		$test_identifier     = new TestIdentifier( 'Unit-Test-ID' );
		$aggregate_root      = UnitTestAggregate::schedule( 'Unit-Test' );
		$test_aggregate_root = UnitTestAggregateDiff::schedule( 'Unit-Test' );

		$collection->attach( $aggregate_root );
		$collection->attach( $test_aggregate_root );

		$found      = $collection->find( $identifier );
		$test_found = $collection->find( $test_identifier );

		$this->assertSame( $aggregate_root, $found );
		$this->assertSame( $test_aggregate_root, $test_found );
		$this->assertNotSame( $found, $test_found );
		$this->assertNotSame( $aggregate_root, $test_aggregate_root );
		$this->assertFalse( $found->getIdentifier()->equals( $test_found->getIdentifier() ) );
	}

	public function testCanLoopOverCollectionMoreThanOnce()
	{
		$collection     = new AggregateRootCollection();
		$aggregate_root = UnitTestAggregate::schedule( 'Unit-Test' );

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
