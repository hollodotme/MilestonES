<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Aggregates;

use hollodotme\MilestonES\AggregateRootCollection;
use hollodotme\MilestonES\DomainEventEnvelope;
use hollodotme\MilestonES\DomainEventEnvelopeCollection;
use hollodotme\MilestonES\EventStore;
use hollodotme\MilestonES\EventStoreConfigDelegate;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Interfaces\CollectsAggregateRoots;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Test\Unit\Fixures\TestAggregateRootRepositoryWithInvalidAggregateRootName;
use hollodotme\MilestonES\Test\Unit\Fixures\TestAggregateRootRepositoryWithTestEventObserver;
use hollodotme\MilestonES\Test\Unit\Fixures\UnitTestAggregate;
use hollodotme\MilestonES\Test\Unit\Fixures\UnitTestAggregateRepository;
use hollodotme\MilestonES\Test\Unit\Fixures\UnitTestEvent;

class AggregateRootRepositoryTest extends \PHPUnit_Framework_TestCase
{

	/** @var EventStore */
	private $event_store;

	/** @var CollectsAggregateRoots */
	private $collection;

	public function setUp()
	{
		$this->event_store = new EventStore( new EventStoreConfigDelegate() );
		$this->collection  = new AggregateRootCollection();
	}

	public function testCanTrackAnAggregateRoot()
	{
		$repository     = new UnitTestAggregateRepository( $this->event_store, $this->collection );
		$aggregate_root = UnitTestAggregate::schedule( 'Unit-Test' );

		$repository->track( $aggregate_root );

		$this->assertTrue( $repository->isTracked( $aggregate_root ) );
	}

	public function testCanGetATrackedAggregateRootById()
	{
		$repository     = new UnitTestAggregateRepository( $this->event_store, $this->collection );
		$aggregate_root = UnitTestAggregate::schedule( 'Unit-Test' );

		$repository->track( $aggregate_root );

		$tracked = $repository->getWithId( new Identifier( 'Unit-Test-ID' ) );

		$this->assertSame( $aggregate_root, $tracked );
		$this->assertTrue( $repository->isTracked( $tracked ) );
	}

	public function testCanGetAggregateRootReconstitutedFromHistory()
	{
		$identifier = new Identifier( 'Unit-Test-ID' );
		$this->simulateEventStreamWithID( $identifier );

		$repository = new UnitTestAggregateRepository( $this->event_store, $this->collection );

		/** @var UnitTestAggregate $reconstituted */
		$reconstituted = $repository->getWithId( new Identifier( 'Unit-Test-ID' ) );

		$this->assertInstanceOf( UnitTestAggregate::class, $reconstituted );
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
		$event = new UnitTestEvent( $id, 'Unit-Test' );

		$collection   = new DomainEventEnvelopeCollection();
		$collection[] = new DomainEventEnvelope( $event, [ ] );

		/** @var DomainEventEnvelopeCollection $collection */
		$this->event_store->commitEvents( $collection );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\AggregateRootNotFound
	 */
	public function testGetAggregateRootFromEventStreamFailsWhenNothingTrackedAndEventStreamNotFound()
	{
		$repository = new UnitTestAggregateRepository( $this->event_store, $this->collection );

		$repository->getWithId( new Identifier( 'Unit-Test-ID' ) );
	}

	public function testCanRegisterEventObservers()
	{
		new TestAggregateRootRepositoryWithTestEventObserver( $this->event_store, $this->collection );

		$this->simulateEventStreamWithID( new Identifier( 'Unit-Test-ID' ) );

		$this->expectOutputString(
			"hollodotme\\MilestonES\\Test\\Unit\\Fixures\\UnitTestEvent with ID Unit-Test-ID was committed.\n"
		);
	}
}
