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
use hollodotme\MilestonES\Interfaces\StoresEvents;

/**
 * Class AggregateRootManager
 *
 * @package hollodotme\MilestonES
 */
class AggregateRootManager implements CommitsChanges
{

	/** @var StoresEvents */
	private $eventStore;

	/** @var CollectsAggregateRoots|AggregatesObjects[] */
	private $aggregateRootCollection;

	/** @var AggregateRootRepository[] */
	private $repositories = [ ];

	/**
	 * @param StoresEvents           $eventStore
	 * @param CollectsAggregateRoots $aggregateRootCollection
	 */
	public function __construct( StoresEvents $eventStore, CollectsAggregateRoots $aggregateRootCollection )
	{
		$this->eventStore              = $eventStore;
		$this->aggregateRootCollection = $aggregateRootCollection;
	}

	/**
	 * @param string $aggregateRootFqcn
	 *
	 * @return AggregateRootRepository
	 */
	public function getRepository( $aggregateRootFqcn )
	{
		$respoitoryFqcn = $this->getAggregateRootRepositoryFqcn( $aggregateRootFqcn );

		if ( $this->isRepositoryTracked( $respoitoryFqcn ) )
		{
			return $this->getTrackedRepository( $respoitoryFqcn );
		}
		else
		{
			$repository = $this->createAggregateRootRepository( $respoitoryFqcn );
			$this->trackRepository( $respoitoryFqcn, $repository );

			return $repository;
		}
	}

	/**
	 * @param string $repositoryFqcn
	 *
	 * @return bool
	 */
	private function isRepositoryTracked( $repositoryFqcn )
	{
		return array_key_exists( $repositoryFqcn, $this->repositories );
	}

	/**
	 * @param string $repositoryFqcn
	 *
	 * @return AggregateRootRepository
	 */
	private function getTrackedRepository( $repositoryFqcn )
	{
		return $this->repositories[ $repositoryFqcn ];
	}

	/**
	 * @param string $repositoryFqcn
	 * @param AggregateRootRepository $repository
	 */
	private function trackRepository( $repositoryFqcn, AggregateRootRepository $repository )
	{
		$this->repositories[ $repositoryFqcn ] = $repository;
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
		return new $repositoryFqcn( $this->eventStore, $this->aggregateRootCollection );
	}

	/**
	 * @throws CommittingEventsFailed
	 */
	final public function commitChanges()
	{
		$changes = $this->aggregateRootCollection->getChanges();

		$this->commitChangesToEventStore( $changes );

		$this->aggregateRootCollection->clearCommittedChanges( $changes );
	}

	/**
	 * @param CollectsEventEnvelopes $events
	 *
	 * @throws CommittingEventsFailed
	 */
	private function commitChangesToEventStore( CollectsEventEnvelopes $events )
	{
		$this->eventStore->commitEvents( $events );
	}
}
