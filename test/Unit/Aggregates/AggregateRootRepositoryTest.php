<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Aggregates;

require_once __DIR__ . '/../_test_classes/TestEventObserver.php';
require_once __DIR__ . '/../_test_classes/TestAggregateRoot.php';
require_once __DIR__ . '/../_test_classes/TestAggregateWasDescribed.php';
require_once __DIR__ . '/../_test_classes/TestAggregateRootRepository.php';
require_once __DIR__ . '/../_test_classes/TestAggregateRootRepositoryWithTestEventObserver.php';
require_once __DIR__ . '/../_test_classes/TestAggregateRootRepositoryWithInvalidAggregateRootName.php';

use hollodotme\MilestonES\AggregateRootCollection;
use hollodotme\MilestonES\EventCollection;
use hollodotme\MilestonES\Events\AggregateRootWasAllocated;
use hollodotme\MilestonES\EventStore;
use hollodotme\MilestonES\EventStoreConfigDelegate;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Test\Unit\TestAggregateRoot;
use hollodotme\MilestonES\Test\Unit\TestAggregateRootRepository;
use hollodotme\MilestonES\Test\Unit\TestAggregateRootRepositoryWithInvalidAggregateRootName;
use hollodotme\MilestonES\Test\Unit\TestAggregateRootRepositoryWithTestEventObserver;
use hollodotme\MilestonES\Test\Unit\TestAggregateWasDescribed;

class AggregateRootRepositoryTest extends \PHPUnit_Framework_TestCase
{

	/** @var EventStore */
	private $event_store;

	private $collection;

	public function setUp()
	{
		$this->event_store = new EventStore( new EventStoreConfigDelegate() );
		$this->collection  = new AggregateRootCollection();
	}

	public function testCanTrackAnAggregateRoot()
	{
		$repository     = new TestAggregateRootRepository( $this->event_store, $this->collection );
		$aggregate_root = TestAggregateRoot::allocateWithId( new Identifier( 'Unit-Test-ID' ) );

		$repository->track( $aggregate_root );

		$this->assertTrue( $repository->isTracked( $aggregate_root ) );
	}

	public function testCanGetATrackedAggregateRootById()
	{
		$repository     = new TestAggregateRootRepository( $this->event_store, $this->collection );
		$aggregate_root = TestAggregateRoot::allocateWithId( new Identifier( 'Unit-Test-ID' ) );

		$repository->track( $aggregate_root );

		$tracked = $repository->getWithId( new Identifier( 'Unit-Test-ID' ) );

		$this->assertSame( $aggregate_root, $tracked );
		$this->assertTrue( $repository->isTracked( $tracked ) );
	}

	public function testCanGetAggregateRootReconstitutedFromEventStream()
	{
		$identifier = new Identifier( 'Unit-Test-ID' );
		$this->simulateEventStreamWithID( $identifier );

		$repository = new TestAggregateRootRepository( $this->event_store, $this->collection );

		/** @var TestAggregateRoot $reconstituted */
		$reconstituted = $repository->getWithId( new Identifier( 'Unit-Test-ID' ) );

		$this->assertInstanceOf( TestAggregateRoot::class, $reconstituted );
		$this->assertTrue( $repository->isTracked( $reconstituted ) );
		$this->assertTrue( $reconstituted->getIdentifier()->equals( $identifier ) );
		$this->assertEquals( 'Unit-Test', $reconstituted->getDescription() );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\ClassIsNotAnAggregateRoot
	 */
	public function testCanGetAggregateRootFromEventStreamFailsWhenAggregateRootClassIsInvalid()
	{
		$identifier = new Identifier( 'Unit-Test-ID' );
		$this->simulateEventStreamWithID( $identifier );

		$repository = new TestAggregateRootRepositoryWithInvalidAggregateRootName(
			$this->event_store,
			$this->collection
		);

		$repository->getWithId( new Identifier( 'Unit-Test-ID' ) );
	}

	private function simulateEventStreamWithID( Identifies $id )
	{
		$alloc_event    = new AggregateRootWasAllocated( $id );
		$describe_event = new TestAggregateWasDescribed( $id );
		$describe_event->setDescription( 'Unit-Test' );

		$collection   = new EventCollection();
		$collection[] = $alloc_event;
		$collection[] = $describe_event;

		$this->event_store->commitEvents( $collection );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventStreamNotFound
	 */
	public function testGetAggregateRootFromEventStreamFailsWhenNothingTrackedAndEventStreamNotFound()
	{
		$repository = new TestAggregateRootRepository( $this->event_store, $this->collection );

		$repository->getWithId( new Identifier( 'Unit-Test-ID' ) );
	}

	public function testCanRegisterEventObservers()
	{
		new TestAggregateRootRepositoryWithTestEventObserver( $this->event_store, $this->collection );

		$this->simulateEventStreamWithID( new Identifier( 'Unit-Test-ID' ) );

		$this->expectOutputString(
			"hollodotme\\MilestonES\\Events\\AggregateRootWasAllocated with ID Unit-Test-ID was committed.\n"
			. "hollodotme\\MilestonES\\Test\\Unit\\TestAggregateWasDescribed with ID Unit-Test-ID was committed.\n"
		);
	}
}
