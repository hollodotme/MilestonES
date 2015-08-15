<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\CommittingEventsFailed;
use hollodotme\MilestonES\Exceptions\RepositoryWithNameDoesNotExist;
use hollodotme\MilestonES\Interfaces\AggregatesObjects;
use hollodotme\MilestonES\Interfaces\CollectsAggregateRoots;
use hollodotme\MilestonES\Interfaces\CollectsEventEnvelopes;
use hollodotme\MilestonES\Interfaces\CommitsChanges;
use hollodotme\MilestonES\Interfaces\StoresApplicationState;
use hollodotme\MilestonES\Interfaces\TracksAggregateRootRepositories;
use hollodotme\MilestonES\Interfaces\TracksAggregateRoots;
use hollodotme\MilestonES\Snapshots\Interfaces\CarriesSnapshotData;
use hollodotme\MilestonES\Snapshots\Interfaces\CollectsSnapshots;
use hollodotme\MilestonES\Snapshots\Interfaces\TakesSnapshots;
use hollodotme\MilestonES\Snapshots\SnapshotCollection;

/**
 * Class AggregateRootManager
 *
 * @package hollodotme\MilestonES
 */
class AggregateRootManager implements CommitsChanges
{

	/** @var StoresApplicationState */
	private $applicationStateStore;

	/** @var CollectsAggregateRoots|AggregatesObjects[] */
	private $aggregateRootCollection;

	/** @var TracksAggregateRootRepositories|TracksAggregateRoots[] */
	private $aggregateRootRepositoryMap;

	/** @var CollectsSnapshots|CarriesSnapshotData[] */
	private $snapshotCollection;

	/**
	 * @param StoresApplicationState $applicationStateStore
	 */
	public function __construct( StoresApplicationState $applicationStateStore )
	{
		$this->applicationStateStore      = $applicationStateStore;
		$this->aggregateRootCollection    = new AggregateRootCollection();
		$this->aggregateRootRepositoryMap = new AggregateRootRepositoryMap();
		$this->snapshotCollection         = new SnapshotCollection();
	}

	/**
	 * @param string $aggregateRootFqcn
	 *
	 * @return AggregateRootRepository
	 */
	public function getRepository( $aggregateRootFqcn )
	{
		$respoitoryFqcn = $this->getAggregateRootRepositoryFqcn( $aggregateRootFqcn );

		if ( $this->aggregateRootRepositoryMap->isTracked( $respoitoryFqcn ) )
		{
			return $this->aggregateRootRepositoryMap->getTracked( $respoitoryFqcn );
		}
		else
		{
			$repository = $this->createAggregateRootRepository( $respoitoryFqcn );
			$this->aggregateRootRepositoryMap->track( $respoitoryFqcn, $repository );

			return $repository;
		}
	}

	/**
	 * @param string $aggregateRootFqcn
	 *
	 * @return string
	 */
	protected function getAggregateRootRepositoryFqcn( $aggregateRootFqcn )
	{
		return $aggregateRootFqcn . 'Repository';
	}

	/**
	 * @param string $repositoryFqcn
	 *
	 * @throws RepositoryWithNameDoesNotExist
	 * @return AggregateRootRepository
	 */
	protected function createAggregateRootRepository( $repositoryFqcn )
	{
		if ( class_exists( $repositoryFqcn, true ) )
		{
			return $this->createAggregateRootRepositoryByFqcn( $repositoryFqcn );
		}
		else
		{
			throw new RepositoryWithNameDoesNotExist( $repositoryFqcn );
		}
	}

	/**
	 * @param string $repositoryFqcn
	 *
	 * @return AggregateRootRepository
	 */
	protected function createAggregateRootRepositoryByFqcn( $repositoryFqcn )
	{
		/** @var TracksAggregateRoots|TakesSnapshots $repositoryFqcn */
		return new $repositoryFqcn(
			$this->applicationStateStore, $this->aggregateRootCollection, $this->snapshotCollection
		);
	}

	/**
	 * @throws CommittingEventsFailed
	 */
	final public function commitChanges()
	{
		$changes = $this->aggregateRootCollection->getChanges();

		$this->commitChangesToEventStore( $changes );
		$this->commitSnapshotsToEventStore( $this->snapshotCollection );

		$this->aggregateRootCollection->clearCommittedChanges( $changes );
	}

	/**
	 * @param CollectsEventEnvelopes $events
	 *
	 * @throws CommittingEventsFailed
	 */
	private function commitChangesToEventStore( CollectsEventEnvelopes $events )
	{
		$this->applicationStateStore->commitEvents( $events );
	}

	/**
	 * @param CollectsSnapshots $snapshots
	 */
	private function commitSnapshotsToEventStore( CollectsSnapshots $snapshots )
	{
		$this->applicationStateStore->commitSnapshots( $snapshots );
	}
}
