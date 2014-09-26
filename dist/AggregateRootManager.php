<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\CommittingChangesOfAggregateRootFailed;
use hollodotme\MilestonES\Exceptions\CommittingEventsFailed;
use hollodotme\MilestonES\Exceptions\RepositoryWithNameDoesNotExist;
use hollodotme\MilestonES\Interfaces\AggregatesModels;
use hollodotme\MilestonES\Interfaces\CollectsAggregateRoots;
use hollodotme\MilestonES\Interfaces\CollectsEvents;
use hollodotme\MilestonES\Interfaces\CommitsChanges;
use hollodotme\MilestonES\Interfaces\StoresEvents;

/**
 * Class AggregateRootManager
 *
 * @package hollodotme\MilestonES
 */
class AggregateRootManager implements CommitsChanges
{

	/** @var CollectsAggregateRoots */
	private $aggregate_root_collection;

	/** @var StoresEvents */
	private $event_store;

	/**
	 * @param StoresEvents $event_store
	 * @param CollectsAggregateRoots $collection
	 */
	public function __construct( StoresEvents $event_store, CollectsAggregateRoots $collection )
	{
		$this->event_store  = $event_store;
		$this->aggregate_root_collection = $collection;
	}

	final public function commitChanges()
	{
		foreach ( $this->aggregate_root_collection as $aggregate_root )
		{
			$this->commitChangesOfAggregateRootIfNecessary( $aggregate_root );
		}
	}

	/**
	 * @param AggregatesModels $aggregate_root
	 *
	 * @throws CommittingChangesOfAggregateRootFailed
	 */
	private function commitChangesOfAggregateRootIfNecessary( AggregatesModels $aggregate_root )
	{
		if ( $aggregate_root->hasChanges() )
		{
			$this->commitChangesAndClearAggregateRoot( $aggregate_root );
		}
	}

	/**
	 * @param AggregatesModels $aggregate_root
	 *
	 * @throws CommittingChangesOfAggregateRootFailed
	 */
	private function commitChangesAndClearAggregateRoot( AggregatesModels $aggregate_root )
	{
		try
		{
			$this->commitChangesToEventStore( $aggregate_root->getChanges() );
		}
		catch ( CommittingEventsFailed $e )
		{
			throw new CommittingChangesOfAggregateRootFailed( (string)$aggregate_root->getIdentifier(), 0, $e );
		}

		$aggregate_root->clearChanges();
	}

	/**
	 * @param CollectsEvents $events
	 *
	 * @throws CommittingEventsFailed
	 */
	private function commitChangesToEventStore( CollectsEvents $events )
	{
		$this->event_store->commitEvents( $events );
	}

	/**
	 * @param string $aggregate_root_fqcn
	 *
	 * @return AggregateRootRepository
	 */
	public function getRepository( $aggregate_root_fqcn )
	{
		$respoitory_fqcn = $this->getAggregateRootRepositoryFqcn( $aggregate_root_fqcn );
		$repository      = $this->createAggregateRootRepository( $respoitory_fqcn );

		return $repository;
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
}
