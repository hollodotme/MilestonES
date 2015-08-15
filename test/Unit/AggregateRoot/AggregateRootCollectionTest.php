<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\AggregateRoot;

use hollodotme\MilestonES\AggregateRootCollection;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Interfaces\ServesEventStreamData;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestAggregateRoot;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestIdentifier;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestAggregate;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestAggregateDiff;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestAggregateOtherId;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestEvent;

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
	 * @expectedException \hollodotme\MilestonES\Exceptions\AggregateRootIsAlreadyAttached
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

	public function testCanGetChangesOfAllAggregateRootsInCollectionInCorrectOrder()
	{
		$collection = new AggregateRootCollection();

		$aggregate_root_1 = TestAggregateRoot::init( 'Unit-Test-ID-1', 'AR 1: First event' );

		$collection->attach( $aggregate_root_1 );

		$aggregate_root_2 = TestAggregateRoot::init( 'Unit-Test-ID-2', 'AR 2: First event' );

		$aggregate_root_1->test( 'AR 1: Second event' );
		$aggregate_root_2->test( 'AR 2: Second event' );

		$collection->attach( $aggregate_root_2 );

		/** @var ServesEventStreamData[] $changes */
		$changes = $collection->getChanges();

		$first_event_in_changes  = $changes[0]->getPayload();
		$second_event_in_changes = $changes[1]->getPayload();
		$third_event_in_changes  = $changes[2]->getPayload();
		$fourth_event_in_changes = $changes[3]->getPayload();

		/** @var UnitTestEvent $first_event_in_changes */
		$this->assertEquals( 'AR 1: First event', $first_event_in_changes->getDescription() );

		/** @var UnitTestEvent $second_event_in_changes */
		$this->assertEquals( 'AR 2: First event', $second_event_in_changes->getDescription() );

		/** @var UnitTestEvent $third_event_in_changes */
		$this->assertEquals( 'AR 1: Second event', $third_event_in_changes->getDescription() );

		/** @var UnitTestEvent $fourth_event_in_changes */
		$this->assertEquals( 'AR 2: Second event', $fourth_event_in_changes->getDescription() );
	}

	public function testCanClearChangesInAggregateRoots()
	{
		$collection = new AggregateRootCollection();

		$aggregate_root_1 = TestAggregateRoot::init( 'Unit-Test-ID-1', 'AR 1: First event' );

		$collection->attach( $aggregate_root_1 );

		$aggregate_root_2 = TestAggregateRoot::init( 'Unit-Test-ID-2', 'AR 2: First event' );

		$aggregate_root_1->test( 'AR 1: Second event' );
		$aggregate_root_2->test( 'AR 2: Second event' );

		$collection->attach( $aggregate_root_2 );

		$changes = $collection->getChanges();

		$collection->clearCommittedChanges( $changes );

		$this->assertFalse( $aggregate_root_1->hasChanges() );
		$this->assertFalse( $aggregate_root_2->hasChanges() );
	}
}
