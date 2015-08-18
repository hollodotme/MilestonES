<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\ApplicationStateStore;

use hollodotme\MilestonES\ApplicationStateStore;
use hollodotme\MilestonES\ApplicationStateStoreConfig;
use hollodotme\MilestonES\EventEnvelope;
use hollodotme\MilestonES\EventEnvelopeCollection;
use hollodotme\MilestonES\EventStream;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestApplicationStateStoreConfigWithEmptyPersistence;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestApplicationStateStoreConfigWithFailingPersistence;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestApplicationStateStoreConfigWithGlobalEventListener;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestApplicationStateStoreConfigWithInvalidEnvelopeCollection;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestApplicationStateStoreConfigWithNonCountableIteratorPersistence;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestApplicationStateStoreConfigWithObjectStoragePersistence;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestEventListener;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestEvent;

class ApplicationStateStoreTest extends \PHPUnit_Framework_TestCase
{

	public function testCanAttachAndNotifyObserversWhenEventIsCommitted()
	{
		$event_store = new ApplicationStateStore( new ApplicationStateStoreConfig() );
		$observer    = new TestEventListener();

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( 0, $event, [ ] );

		$event_store->attachEventListener( $observer );
		/** @var EventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$this->expectOutputString( UnitTestEvent::class . " with ID Unit-Test-ID was committed.\n" );
	}

	public function testCanAttachAndNotifyGlobalObserversWhenEventIsCommitted()
	{
		$event_store = new ApplicationStateStore( new TestApplicationStateStoreConfigWithGlobalEventListener() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( 0, $event, [ ] );

		/** @var EventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$this->expectOutputString( UnitTestEvent::class . " with ID Unit-Test-ID was globally observed.\n" );
	}

	public function testCanAttachAndNotifyGlobalAndSpecificObserversWhenEventIsCommitted()
	{
		$event_store = new ApplicationStateStore( new TestApplicationStateStoreConfigWithGlobalEventListener() );
		$observer    = new TestEventListener();

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( 0, $event, [ ] );

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
		$event_store  = new ApplicationStateStore( new ApplicationStateStoreConfig() );
		$observer          = new TestEventListener();
		$detached_observer = new TestEventListener();

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$event_store->attachEventListener( $observer );
		$event_store->attachEventListener( $detached_observer );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( 0, $event, [ ] );

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
		$event_store = new ApplicationStateStore( new ApplicationStateStoreConfig() );
		$event_store->getEventStreamForId( new Identifier( 'Unit-Test-ID' ) );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventStreamNotFound
	 */
	public function testGetEventStreamForIdFailsWhenEventStreamIsEmpty()
	{
		$event_store = new ApplicationStateStore( new TestApplicationStateStoreConfigWithEmptyPersistence() );
		$event_store->getEventStreamForId( new Identifier( 'Unit-Test-ID' ) );
	}

	public function testGetEventStreamForId()
	{
		$event_store = new ApplicationStateStore( new ApplicationStateStoreConfig() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( 0, $event, [ ] );

		/** @var EventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$stream = $event_store->getEventStreamForId( $identifier );
		/** @var EventEnvelope $fetchedEventEnvelope */
		$fetchedEventEnvelope = $stream[0];
		/** @var UnitTestEvent $fetchedEvent */
		$fetchedEvent = $fetchedEventEnvelope->getPayload();

		$this->assertInstanceOf( EventStream::class, $stream );
		$this->assertCount( 1, $stream );
		$this->assertInstanceOf( EventEnvelope::class, $fetchedEventEnvelope );
		$this->assertEquals( get_class( $event ), get_class( $fetchedEvent ) );
		$this->assertTrue( $fetchedEventEnvelope->getStreamId()->equals( $identifier ) );
		$this->assertTrue( $fetchedEvent->getStreamId()->equals( $identifier ) );
		$this->assertEquals( 'Unit-Test', $fetchedEvent->getDescription() );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\CommittingEventsFailed
	 */
	public function testCommitEventsFailsWhenPersistenceFails()
	{
		$event_store = new ApplicationStateStore( new TestApplicationStateStoreConfigWithFailingPersistence() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( 0, $event, [ ] );

		/** @var EventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\InvalidCommitEnvelopeCollection
	 */
	public function testGetEventStreamForIdFailsWhenEventEnvelopesCollectionIsNotIteratable()
	{
		$event_store = new ApplicationStateStore( new TestApplicationStateStoreConfigWithInvalidEnvelopeCollection() );
		$event_store->getEventStreamForId( new Identifier( 'Unit-Test-ID' ) );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\InvalidCommitEnvelopeCollection
	 */
	public function testGetEventStreamForIdFailsWhenEventEnvelopesCollectionIsNotCountable()
	{
		$event_store =
			new ApplicationStateStore( new TestApplicationStateStoreConfigWithNonCountableIteratorPersistence() );
		$event_store->getEventStreamForId( new Identifier( 'Unit-Test-ID' ) );
	}

	public function testGetEventStreamForIdWithCountableIterator()
	{
		$event_store = new ApplicationStateStore( new TestApplicationStateStoreConfigWithObjectStoragePersistence() );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new UnitTestEvent( $identifier, 'Unit-Test' );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( 0, $event, [ ] );

		/** @var EventEnvelopeCollection $collection */
		$event_store->commitEvents( $collection );

		$stream = $event_store->getEventStreamForId( $identifier );

		/** @var EventEnvelope $fetchedEventEnvelope */
		$fetchedEventEnvelope = $stream[0];
		/** @var UnitTestEvent $fetchedEvent */
		$fetchedEvent = $fetchedEventEnvelope->getPayload();

		$this->assertInstanceOf( EventStream::class, $stream );
		$this->assertCount( 1, $stream );
		$this->assertInstanceOf( EventEnvelope::class, $fetchedEventEnvelope );
		$this->assertEquals( get_class( $event ), get_class( $fetchedEvent ) );
		$this->assertTrue( $fetchedEventEnvelope->getStreamId()->equals( $identifier ) );
		$this->assertTrue( $fetchedEvent->getStreamId()->equals( $identifier ) );
		$this->assertEquals( 'Unit-Test', $fetchedEvent->getDescription() );
	}
}
