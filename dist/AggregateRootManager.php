<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\CommittingEventsFailed;
use hollodotme\MilestonES\Exceptions\RepositoryWithNameDoesNotExist;
use hollodotme\MilestonES\Interfaces\AggregatesModels;
use hollodotme\MilestonES\Interfaces\CollectsAggregateRoots;
use hollodotme\MilestonES\Interfaces\CollectsDomainEventEnvelopes;
use hollodotme\MilestonES\Interfaces\CommitsChanges;
use hollodotme\MilestonES\Interfaces\StoresEvents;

/**
 * Class AggregateRootManager
 *
 * @package hollodotme\MilestonES
 */
class AggregateRootManager implements CommitsChanges
{

	/** @var CollectsAggregateRoots|AggregatesModels[] */
	private $aggregate_root_collection;

	/** @var StoresEvents */
	private $event_store;

	/** @var AggregateRootRepository[] */
	private $repositories = [ ];

	/**
	 * @param StoresEvents           $event_store
	 * @param CollectsAggregateRoots $collection
	 */
	public function __construct( StoresEvents $event_store, CollectsAggregateRoots $collection )
	{
		$this->event_store               = $event_store;
		$this->aggregate_root_collection = $collection;
	}

	/**
	 * @param string $aggregate_root_fqcn
	 *
	 * @return AggregateRootRepository
	 */
	public function getRepository( $aggregate_root_fqcn )
	{
		$respoitory_fqcn = $this->getAggregateRootRepositoryFqcn( $aggregate_root_fqcn );

		if ( $this->isRepositoryTracked( $respoitory_fqcn ) )
		{
			return $this->getTrackedRepository( $respoitory_fqcn );
		}
		else
		{
			$repository = $this->createAggregateRootRepository( $respoitory_fqcn );
			$this->trackRepository( $respoitory_fqcn, $repository );

			return $repository;
		}
	}

	/**
	 * @param string $repository_fqcn
	 *
	 * @return bool
	 */
	private function isRepositoryTracked( $repository_fqcn )
	{
		return array_key_exists( $repository_fqcn, $this->repositories );
	}

	/**
	 * @param string $repository_fqcn
	 *
	 * @return AggregateRootRepository
	 */
	private function getTrackedRepository( $repository_fqcn )
	{
		return $this->repositories[ $repository_fqcn ];
	}

	/**
	 * @param string                  $repository_fqcn
	 * @param AggregateRootRepository $repository
	 */
	private function trackRepository( $repository_fqcn, AggregateRootRepository $repository )
	{
		$this->repositories[ $repository_fqcn ] = $repository;
	}

	/**
	 * @param string $aggregate_root_fqcn
	 *
	 * @return string
	 */
	protected function getAggregateRootRepositoryFqcn( $aggregate_root_fqcn )
	{
		return $aggregate_root_fqcn . 'Repository';
	}

	/**
	 * @param string $repository_fqcn
	 *
	 * @throws RepositoryWithNameDoesNotExist
	 * @return AggregateRootRepository
	 */
	protected function createAggregateRootRepository( $repository_fqcn )
	{
		if ( class_exists( $repository_fqcn, true ) )
		{
			return $this->createAggregateRootRepositoryByFqcn( $repository_fqcn );
		}
		else
		{
			throw new RepositoryWithNameDoesNotExist( $repository_fqcn );
		}
	}

	/**
	 * @param string $repository_fqcn
	 *
	 * @return AggregateRootRepository
	 */
	protected function createAggregateRootRepositoryByFqcn( $repository_fqcn )
	{
		return new $repository_fqcn( $this->event_store, $this->aggregate_root_collection );
	}

	/**
	 * @throws CommittingEventsFailed
	 */
	final public function commitChanges()
	{
		$changes = $this->aggregate_root_collection->getChanges();

		$this->commitChangesToEventStore( $changes );

		$this->aggregate_root_collection->clearCommittedChanges( $changes );
	}

	/**
	 * @param CollectsDomainEventEnvelopes $events
	 *
	 * @throws CommittingEventsFailed
	 */
	private function commitChangesToEventStore( CollectsDomainEventEnvelopes $events )
	{
		$this->event_store->commitEvents( $events );
	}
}
