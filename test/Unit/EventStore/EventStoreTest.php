<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\EventStore;

use hollodotme\MilestonES\EventEnvelope;
use hollodotme\MilestonES\EventEnvelopeCollection;
use hollodotme\MilestonES\EventStore;
use hollodotme\MilestonES\EventStoreConfig;
use hollodotme\MilestonES\EventStream;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventListener;
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventStoreConfigWithEmptyPersistence;
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventStoreConfigWithFailingPersistence;
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventStoreConfigWithGlobalEventListener;
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventStoreConfigWithInvalidEnvelopeCollection;
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventStoreConfigWithNonCountableIteratorPersistence;
use hollodotme\MilestonES\Test\Unit\Fixures\TestEventStoreConfigWithObjectStoragePersistence;
use hollodotme\MilestonES\Test\Unit\Fixures\UnitTestEvent;

class EventStoreTest extends \PHPUnit_Framework_TestCase
{

	public function testCanAttachAndNotifyObserversWhenEventIsCommitted()
	{
		$event_store = new EventStore( new EventStoreConfig() );
		$observer    = new TestEventListener();

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( $event, [ ] );

		$event_store->attachEventListener( $observer );
		/** @var EventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$this->expectOutputString( UnitTestEvent::class . " with ID Unit-Test-ID was committed.\n" );
	}

	public function testCanAttachAndNotifyGlobalObserversWhenEventIsCommitted()
	{
		$event_store = new EventStore( new TestEventStoreConfigWithGlobalEventListener() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( $event, [ ] );

		/** @var EventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$this->expectOutputString( UnitTestEvent::class . " with ID Unit-Test-ID was globally observed.\n" );
	}

	public function testCanAttachAndNotifyGlobalAndSpecificObserversWhenEventIsCommitted()
	{
		$event_store = new EventStore( new TestEventStoreConfigWithGlobalEventListener() );
		$observer    = new TestEventListener();

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( $event, [ ] );

		$event_store->attachEventListener( $observer );
		/** @var EventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$this->expectOutputString(
			UnitTestEvent::class . " with ID Unit-Test-ID was committed.\n"
			. UnitTestEvent::class . " with ID Unit-Test-ID was globally observed.\n"
		);
	}

	public function testCanDetachObservers()
	{
		$event_store       = new EventStore( new EventStoreConfig() );
		$observer          = new TestEventListener();
		$detached_observer = new TestEventListener();

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$event_store->attachEventListener( $observer );
		$event_store->attachEventListener( $detached_observer );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( $event, [ ] );

		/** @var EventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$event_store->detachEventListener( $detached_observer );

		/** @var EventEnvelopeCollection $collection */
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
		$event_store = new EventStore( new EventStoreConfig() );
		$event_store->getEventStreamForId( new Identifier( 'Unit-Test-ID' ) );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventStreamNotFound
	 */
	public function testGetEventStreamForIdFailsWhenEventStreamIsEmpty()
	{
		$event_store = new EventStore( new TestEventStoreConfigWithEmptyPersistence() );
		$event_store->getEventStreamForId( new Identifier( 'Unit-Test-ID' ) );
	}

	public function testGetEventStreamForId()
	{
		$event_store = new EventStore( new EventStoreConfig() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( $event, [ ] );

		/** @var EventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$stream = $event_store->getEventStreamForId( $identifier );
		/** @var EventEnvelope $fetched_event_envelope */
		$fetched_event_envelope = $stream[0];
		/** @var UnitTestEvent $fetched_event */
		$fetched_event = $fetched_event_envelope->getPayload();

		$this->assertInstanceOf( EventStream::class, $stream );
		$this->assertCount( 1, $stream );
		$this->assertInstanceOf( EventEnvelope::class, $fetched_event_envelope );
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
		$event_store = new EventStore( new TestEventStoreConfigWithFailingPersistence() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( $event, [ ] );

		/** @var EventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\InvalidEventEnvelopeCollection
	 */
	public function testGetEventStreamForIdFailsWhenEventEnvelopesCollectionIsNotIteratable()
	{
		$event_store = new EventStore( new TestEventStoreConfigWithInvalidEnvelopeCollection() );
		$event_store->getEventStreamForId( new Identifier( 'Unit-Test-ID' ) );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\InvalidEventEnvelopeCollection
	 */
	public function testGetEventStreamForIdFailsWhenEventEnvelopesCollectionIsNotCountable()
	{
		$event_store = new EventStore( new TestEventStoreConfigWithNonCountableIteratorPersistence() );
		$event_store->getEventStreamForId( new Identifier( 'Unit-Test-ID' ) );
	}

	public function testGetEventStreamForIdWithCountableIterator()
	{
		$event_store = new EventStore( new TestEventStoreConfigWithObjectStoragePersistence() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( $event, [ ] );

		/** @var EventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$stream = $event_store->getEventStreamForId( $identifier );

		/** @var EventEnvelope $fetched_event_envelope */
		$fetched_event_envelope = $stream[0];
		/** @var UnitTestEvent $fetched_event */
		$fetched_event = $fetched_event_envelope->getPayload();

		$this->assertInstanceOf( EventStream::class, $stream );
		$this->assertCount( 1, $stream );
		$this->assertInstanceOf( EventEnvelope::class, $fetched_event_envelope );
		$this->assertEquals( get_class( $event ), get_class( $fetched_event ) );
		$this->assertTrue( $fetched_event_envelope->getStreamId()->equals( $identifier ) );
		$this->assertTrue( $fetched_event->getStreamId()->equals( $identifier ) );
		$this->assertEquals( 'Unit-Test', $fetched_event->getDescription() );
	}
}
