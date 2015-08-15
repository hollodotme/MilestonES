<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\AggregateRoot;

use hollodotme\MilestonES\AggregateRootCollection;
use hollodotme\MilestonES\ApplicationStateStore;
use hollodotme\MilestonES\ApplicationStateStoreConfig;
use hollodotme\MilestonES\EventEnvelope;
use hollodotme\MilestonES\EventEnvelopeCollection;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Interfaces\CollectsAggregateRoots;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;
use hollodotme\MilestonES\Snapshots\Interfaces\CollectsSnapshots;
use hollodotme\MilestonES\Snapshots\SnapshotCollection;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestAggregateRootRepositoryWithInvalidAggregateRootName;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestAggregateRootRepositoryWithTestEventObserver;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestAggregate;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestAggregateRepository;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestEvent;

class AggregateRootRepositoryTest extends \PHPUnit_Framework_TestCase
{

	/** @var ApplicationStateStore */
	private $applicationStateStore;

	/** @var CollectsAggregateRoots */
	private $aggregateRootCollection;

	/** @var CollectsSnapshots */
	private $snapshotCollection;

	public function setUp()
	{
		$this->applicationStateStore   = new ApplicationStateStore( new ApplicationStateStoreConfig() );
		$this->aggregateRootCollection = new AggregateRootCollection();
		$this->snapshotCollection      = new SnapshotCollection();
	}

	public function testCanTrackAnAggregateRoot()
	{
		$repository = new UnitTestAggregateRepository(
			$this->applicationStateStore,
			$this->aggregateRootCollection,
			$this->snapshotCollection
		);

		$aggregateRoot = UnitTestAggregate::schedule( 'Unit-Test' );

		$repository->track( $aggregateRoot );

		$this->assertTrue( $repository->isTracked( $aggregateRoot ) );
	}

	public function testCanGetATrackedAggregateRootById()
	{
		$repository = new UnitTestAggregateRepository(
			$this->applicationStateStore,
			$this->aggregateRootCollection,
			$this->snapshotCollection
		);

		$aggregateRoot = UnitTestAggregate::schedule( 'Unit-Test' );

		$repository->track( $aggregateRoot );

		$tracked = $repository->getWithId( new Identifier( 'Unit-Test-ID' ) );

		$this->assertSame( $aggregateRoot, $tracked );
		$this->assertTrue( $repository->isTracked( $tracked ) );
	}

	public function testCanGetAggregateRootReconstitutedFromHistory()
	{
		$identifier = new Identifier( 'Unit-Test-ID' );
		$this->simulateEventStreamWithID( $identifier );

		$repository = new UnitTestAggregateRepository(
			$this->applicationStateStore,
			$this->aggregateRootCollection,
			$this->snapshotCollection
		);

		/** @var UnitTestAggregate $reconstituted */
		$reconstituted = $repository->getWithId( new Identifier( 'Unit-Test-ID' ) );

		$this->assertInstanceOf( UnitTestAggregate::class, $reconstituted );
		$this->assertTrue( $repository->isTracked( $reconstituted ) );
		$this->assertTrue( $reconstituted->getIdentifier()->equals( $identifier ) );
		$this->assertEquals( 'Unit-Test', $reconstituted->getDescription() );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\NotAnAggregateRoot
	 */
	public function testCanGetAggregateRootFromEventStreamFailsWhenAggregateRootClassIsInvalid()
	{
		$identifier = new Identifier( 'Unit-Test-ID' );
		$this->simulateEventStreamWithID( $identifier );

		$repository = new TestAggregateRootRepositoryWithInvalidAggregateRootName(
			$this->applicationStateStore,
			$this->aggregateRootCollection,
			$this->snapshotCollection
		);

		$repository->getWithId( new Identifier( 'Unit-Test-ID' ) );
	}

	private function simulateEventStreamWithID( IdentifiesObject $id )
	{
		$event = new UnitTestEvent( $id, 'Unit-Test' );

		$collection   = new EventEnvelopeCollection();
		$collection[] = new EventEnvelope( $event, [ ] );

		/** @var EventEnvelopeCollection $collection */
		$this->applicationStateStore->commitEvents( $collection );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\AggregateRootNotFound
	 */
	public function testGetAggregateRootFromEventStreamFailsWhenNothingTrackedAndEventStreamNotFound()
	{
		$repository = new UnitTestAggregateRepository(
			$this->applicationStateStore,
			$this->aggregateRootCollection,
			$this->snapshotCollection
		);

		$repository->getWithId( new Identifier( 'Unit-Test-ID' ) );
	}

	public function testCanRegisterEventListeners()
	{
		new TestAggregateRootRepositoryWithTestEventObserver(
			$this->applicationStateStore,
			$this->aggregateRootCollection,
			$this->snapshotCollection
		);

		$this->simulateEventStreamWithID( new Identifier( 'Unit-Test-ID' ) );

		$this->expectOutputString(
			"hollodotme\\MilestonES\\Test\\Unit\\Fixtures\\UnitTestEvent with ID Unit-Test-ID was committed.\n"
		);
	}
}
