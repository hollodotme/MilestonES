<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\EventStore;

use hollodotme\MilestonES\DomainEventEnvelope;
use hollodotme\MilestonES\DomainEventEnvelopeCollection;
use hollodotme\MilestonES\EventStore;
use hollodotme\MilestonES\EventStoreConfigDelegate;
use hollodotme\MilestonES\EventStream;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Test\Unit\TestEventObserver;
use hollodotme\MilestonES\Test\Unit\TestEventStoreConfigDelegateWithEmptyPersistence;
use hollodotme\MilestonES\Test\Unit\TestEventStoreConfigDelegateWithFailingPersistence;
use hollodotme\MilestonES\Test\Unit\TestEventStoreConfigDelegateWithGlobalObserver;
use hollodotme\MilestonES\Test\Unit\TestEventStoreConfigDelegateWithInvalidEnvelopeCollection;
use hollodotme\MilestonES\Test\Unit\TestEventStoreConfigDelegateWithNonCountableIteratorPersistence;
use hollodotme\MilestonES\Test\Unit\TestEventStoreConfigDelegateWithObjectStoragePersistence;
use hollodotme\MilestonES\Test\Unit\UnitTestEvent;

require_once __DIR__ . '/../Fixures/TestEventObserver.php';
require_once __DIR__ . '/../Fixures/TestGlobalEventObserver.php';
require_once __DIR__ . '/../Fixures/TestMemoryPersistenceWithFailOnPersist.php';
require_once __DIR__ . '/../Fixures/TestMemoryeEmptyPersistence.php';
require_once __DIR__ . '/../Fixures/TestMemoryPersistanceReturningStringStream.php';
require_once __DIR__ . '/../Fixures/TestMemoryPersistenceReturningNotCountableIterator.php';
require_once __DIR__ . '/../Fixures/TestMemoryPersistenceWithObjectStorage.php';
require_once __DIR__ . '/../Fixures/TestEventStoreConfigDelegateWithGlobalObserver.php';
require_once __DIR__ . '/../Fixures/TestEventStoreConfigDelegateWithFailingPersistence.php';
require_once __DIR__ . '/../Fixures/TestEventStoreConfigDelegateWithEmptyPersistence.php';
require_once __DIR__ . '/../Fixures/TestEventStoreConfigDelegateWithInvalidEnvelopeCollection.php';
require_once __DIR__ . '/../Fixures/TestEventStoreConfigDelegateWithNonCountableIteratorPersistence.php';
require_once __DIR__ . '/../Fixures/TestEventStoreConfigDelegateWithObjectStoragePersistence.php';
require_once __DIR__ . '/../Fixures/UnitTestEvent.php';

class EventStoreTest extends \PHPUnit_Framework_TestCase
{

	public function testCanAttachAndNotifyObserversWhenEventIsCommitted()
	{
		$event_store = new EventStore( new EventStoreConfigDelegate() );
		$observer    = new TestEventObserver();

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new DomainEventEnvelopeCollection();
		$collection[] = new DomainEventEnvelope( $event, [ ] );

		$event_store->attachCommittedEventObserver( $observer );
		/** @var DomainEventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$this->expectOutputString( UnitTestEvent::class . " with ID Unit-Test-ID was committed.\n" );
	}

	public function testCanAttachAndNotifyGlobalObserversWhenEventIsCommitted()
	{
		$event_store = new EventStore( new TestEventStoreConfigDelegateWithGlobalObserver() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new DomainEventEnvelopeCollection();
		$collection[] = new DomainEventEnvelope( $event, [ ] );

		/** @var DomainEventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$this->expectOutputString( UnitTestEvent::class . " with ID Unit-Test-ID was globally observed.\n" );
	}

	public function testCanAttachAndNotifyGlobalAndSpecificObserversWhenEventIsCommitted()
	{
		$event_store = new EventStore( new TestEventStoreConfigDelegateWithGlobalObserver() );
		$observer    = new TestEventObserver();

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new DomainEventEnvelopeCollection();
		$collection[] = new DomainEventEnvelope( $event, [ ] );

		$event_store->attachCommittedEventObserver( $observer );
		/** @var DomainEventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$this->expectOutputString(
			UnitTestEvent::class . " with ID Unit-Test-ID was committed.\n"
			. UnitTestEvent::class . " with ID Unit-Test-ID was globally observed.\n"
		);
	}

	public function testCanDetachObservers()
	{
		$event_store       = new EventStore( new EventStoreConfigDelegate() );
		$observer          = new TestEventObserver();
		$detached_observer = new TestEventObserver();

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$event_store->attachCommittedEventObserver( $observer );
		$event_store->attachCommittedEventObserver( $detached_observer );

		$collection   = new DomainEventEnvelopeCollection();
		$collection[] = new DomainEventEnvelope( $event, [ ] );

		/** @var DomainEventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$event_store->detachCommittedEventObserver( $detached_observer );

		/** @var DomainEventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$this->expectOutputString(
			UnitTestEvent::class . " with ID Unit-Test-ID was committed.\n"
			. UnitTestEvent::class . " with ID Unit-Test-ID was committed.\n"
			. UnitTestEvent::class . " with ID Unit-Test-ID was committed.\n"
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

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventStreamNotFound
	 */
	public function testGetEventStreamForIdFailsWhenEventStreamIsEmpty()
	{
		$event_store = new EventStore( new TestEventStoreConfigDelegateWithEmptyPersistence() );
		$event_store->getEventStreamForId( new Identifier( 'Unit-Test-ID' ) );
	}

	public function testGetEventStreamForId()
	{
		$event_store = new EventStore( new EventStoreConfigDelegate() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new DomainEventEnvelopeCollection();
		$collection[] = new DomainEventEnvelope( $event, [ ] );

		/** @var DomainEventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$stream = $event_store->getEventStreamForId( $identifier );
		/** @var DomainEventEnvelope $fetched_event_envelope */
		$fetched_event_envelope = $stream[0];
		/** @var UnitTestEvent $fetched_event */
		$fetched_event = $fetched_event_envelope->getPayload();

		$this->assertInstanceOf( EventStream::class, $stream );
		$this->assertCount( 1, $stream );
		$this->assertInstanceOf( DomainEventEnvelope::class, $fetched_event_envelope );
		$this->assertEquals( get_class( $event ), get_class( $fetched_event ) );
		$this->assertTrue( $fetched_event_envelope->getStreamId()->equals( $identifier ) );
		$this->assertTrue( $fetched_event->getStreamId()->equals( $identifier ) );
		$this->assertEquals( 'Unit-Test', $fetched_event->getDescription() );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\CommittingEventsFailed
	 */
	public function testCommitEventsFailsWhenPersistenceFails()
	{
		$event_store = new EventStore( new TestEventStoreConfigDelegateWithFailingPersistence() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new DomainEventEnvelopeCollection();
		$collection[] = new DomainEventEnvelope( $event, [ ] );

		/** @var DomainEventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\InvalidEventEnvelopesCollection
	 */
	public function testGetEventStreamForIdFailsWhenEventEnvelopesCollectionIsNotIteratable()
	{
		$event_store = new EventStore( new TestEventStoreConfigDelegateWithInvalidEnvelopeCollection() );
		$event_store->getEventStreamForId( new Identifier( 'Unit-Test-ID' ) );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\InvalidEventEnvelopesCollection
	 */
	public function testGetEventStreamForIdFailsWhenEventEnvelopesCollectionIsNotCountable()
	{
		$event_store = new EventStore( new TestEventStoreConfigDelegateWithNonCountableIteratorPersistence() );
		$event_store->getEventStreamForId( new Identifier( 'Unit-Test-ID' ) );
	}

	public function testGetEventStreamForIdWithCountableIterator()
	{
		$event_store = new EventStore( new TestEventStoreConfigDelegateWithObjectStoragePersistence() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new DomainEventEnvelopeCollection();
		$collection[] = new DomainEventEnvelope( $event, [ ] );

		/** @var DomainEventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$stream = $event_store->getEventStreamForId( $identifier );

		/** @var DomainEventEnvelope $fetched_event_envelope */
		$fetched_event_envelope = $stream[0];
		/** @var UnitTestEvent $fetched_event */
		$fetched_event = $fetched_event_envelope->getPayload();

		$this->assertInstanceOf( EventStream::class, $stream );
		$this->assertCount( 1, $stream );
		$this->assertInstanceOf( DomainEventEnvelope::class, $fetched_event_envelope );
		$this->assertEquals( get_class( $event ), get_class( $fetched_event ) );
		$this->assertTrue( $fetched_event_envelope->getStreamId()->equals( $identifier ) );
		$this->assertTrue( $fetched_event->getStreamId()->equals( $identifier ) );
		$this->assertEquals( 'Unit-Test', $fetched_event->getDescription() );
	}
}
