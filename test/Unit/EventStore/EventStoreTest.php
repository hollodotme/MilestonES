<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\EventStore;

use hollodotme\MilestonES\EventCollection;
use hollodotme\MilestonES\EventStore;
use hollodotme\MilestonES\EventStoreConfigDelegate;
use hollodotme\MilestonES\EventStream;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Test\Unit\TestAggregateWasDescribed;
use hollodotme\MilestonES\Test\Unit\TestEventObserver;
use hollodotme\MilestonES\Test\Unit\TestEventStoreConfigDelegateWithFailingPersistence;
use hollodotme\MilestonES\Test\Unit\TestEventStoreConfigDelegateWithGlobalObserver;

require_once __DIR__ . '/../_test_classes/TestEventObserver.php';
require_once __DIR__ . '/../_test_classes/TestMemoryPersistenceWithFailOnPersist.php';
require_once __DIR__ . '/../_test_classes/TestEventStoreConfigDelegateWithGlobalObserver.php';
require_once __DIR__ . '/../_test_classes/TestEventStoreConfigDelegateWithFailingPersistence.php';
require_once __DIR__ . '/../_test_classes/TestAggregateWasDescribed.php';
require_once __DIR__ . '/../_test_classes/TestAggregateWasDeleted.php';

class EventStoreTest extends \PHPUnit_Framework_TestCase
{

	public function testCanAttachAndNotifyObserversWhenEventIsCommitted()
	{
		$event_store = new EventStore( new EventStoreConfigDelegate() );
		$observer    = new TestEventObserver();

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new TestAggregateWasDescribed( $identifier );
		$event->setDescription( 'Unit-Test' );

		$collection   = new EventCollection();
		$collection[] = $event;

		$event_store->attachCommittedEventObserver( $observer );
		/** @var EventCollection $collection */
		$event_store->commitEvents( $collection );

		$this->expectOutputString( TestAggregateWasDescribed::class . " with ID Unit-Test-ID was committed.\n" );
	}

	public function testCanAttachAndNotifyGlobalObserversWhenEventIsCommitted()
	{
		$event_store = new EventStore( new TestEventStoreConfigDelegateWithGlobalObserver() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new TestAggregateWasDescribed( $identifier );
		$event->setDescription( 'Unit-Test' );

		$collection   = new EventCollection();
		$collection[] = $event;

		/** @var EventCollection $collection */
		$event_store->commitEvents( $collection );

		$this->expectOutputString( TestAggregateWasDescribed::class . " with ID Unit-Test-ID was committed.\n" );
	}

	public function testCanAttachAndNotifyGlobalAndSpecificObserversWhenEventIsCommitted()
	{
		$event_store = new EventStore( new TestEventStoreConfigDelegateWithGlobalObserver() );
		$observer    = new TestEventObserver();

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new TestAggregateWasDescribed( $identifier );
		$event->setDescription( 'Unit-Test' );

		$collection   = new EventCollection();
		$collection[] = $event;

		$event_store->attachCommittedEventObserver( $observer );
		/** @var EventCollection $collection */
		$event_store->commitEvents( $collection );

		$this->expectOutputString(
			TestAggregateWasDescribed::class . " with ID Unit-Test-ID was committed.\n"
			. TestAggregateWasDescribed::class . " with ID Unit-Test-ID was committed.\n"
		);
	}

	public function testCanDetachObservers()
	{
		$event_store       = new EventStore( new EventStoreConfigDelegate() );
		$observer          = new TestEventObserver();
		$detached_observer = new TestEventObserver();

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new TestAggregateWasDescribed( $identifier );
		$event->setDescription( 'Unit-Test' );

		$event_store->attachCommittedEventObserver( $observer );
		$event_store->attachCommittedEventObserver( $detached_observer );

		$collection   = new EventCollection();
		$collection[] = $event;

		/** @var EventCollection $collection */
		$event_store->commitEvents( $collection );

		$event_store->detachCommittedEventObserver( $detached_observer );

		/** @var EventCollection $collection */
		$event_store->commitEvents( $collection );

		$this->expectOutputString(
			TestAggregateWasDescribed::class . " with ID Unit-Test-ID was committed.\n"
			. TestAggregateWasDescribed::class . " with ID Unit-Test-ID was committed.\n"
			. TestAggregateWasDescribed::class . " with ID Unit-Test-ID was committed.\n"
		);
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventStreamNotFound
	 */
	public function testGetEventStreamForIdFailsWhenEventStreamNotFound()
	{
		$event_store = new EventStore( new EventStoreConfigDelegate() );
		$event_store->getEventStreamForId( new Identifier( 'Unit-Test-ID' ) );
	}

	public function testGetEventStreamForId()
	{
		$event_store = new EventStore( new EventStoreConfigDelegate() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new TestAggregateWasDescribed( $identifier );
		$event->setDescription( 'Unit-Test' );

		$collection   = new EventCollection();
		$collection[] = $event;

		/** @var EventCollection $collection */
		$event_store->commitEvents( $collection );

		$stream              = $event_store->getEventStreamForId( $identifier );
		$reconstituted_event = $stream[0];

		$this->assertInstanceOf( EventStream::class, $stream );
		$this->assertCount( 1, $stream );
		$this->assertEquals( get_class( $event ), get_class( $stream[0] ) );
		$this->assertTrue( $stream[0]->getStreamId()->equals( $identifier ) );
		/** @var TestAggregateWasDescribed $reconstituted_event */
		$this->assertEquals( 'Unit-Test', $reconstituted_event->getDescription() );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\CommittingEventsFailed
	 */
	public function testCommitEventsFailsWhenPersistenceFails()
	{
		$event_store = new EventStore( new TestEventStoreConfigDelegateWithFailingPersistence() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new TestAggregateWasDescribed( $identifier );
		$event->setDescription( 'Unit-Test' );

		$collection   = new EventCollection();
		$collection[] = $event;

		/** @var EventCollection $collection */
		$event_store->commitEvents( $collection );
	}
}
 