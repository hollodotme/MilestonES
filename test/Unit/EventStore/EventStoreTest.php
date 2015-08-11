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
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventObserver;
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventStoreConfigDelegateWithEmptyPersistence;
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventStoreConfigDelegateWithFailingPersistence;
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventStoreConfigDelegateWithGlobalObserver;
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventStoreConfigDelegateWithInvalidEnvelopeCollection;
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventStoreConfigDelegateWithNonCountableIteratorPersistence;
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventStoreConfigDelegateWithObjectStoragePersistence;
use hollodotme\MilestonES\Test\Unit\Fixures\UnitTestEvent;

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
