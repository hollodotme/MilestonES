<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\CommittingChangesOfAggregateRootFailed;
use hollodotme\MilestonES\Exceptions\CommittingEventsFailed;
use hollodotme\MilestonES\Exceptions\RepositoryWithNameDoesNotExist;
use hollodotme\MilestonES\Exceptions\SharedInstanceOfAggregateRootManagerAlreadyCreated;
use hollodotme\MilestonES\Exceptions\SharedInstanceOfAggregateRootManagerNotYetCreated;
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

	/** @var AggregateRootRepository[] */
	private $repositories = [];

	/** @var null|AggregateRootManager */
	private static $shared_instance = null;

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
	 * @param StoresEvents           $event_store
	 * @param CollectsAggregateRoots $collection
	 *
	 * @return AggregateRootManager|null
	 * @throws SharedInstanceOfAggregateRootManagerAlreadyCreated
	 */
	final public static function createSharedInstance( StoresEvents $event_store, CollectsAggregateRoots $collection )
	{
		if ( is_null( self::$shared_instance ) )
		{
			self::$shared_instance = new self( $event_store, $collection );

			return self::$shared_instance;
		}
		else
		{
			throw new SharedInstanceOfAggregateRootManagerAlreadyCreated();
		}
	}

	/**
	 * @return AggregateRootManager
	 * @throws SharedInstanceOfAggregateRootManagerNotYetCreated
	 */
	final public static function shared()
	{
		if ( self::$shared_instance instanceof AggregateRootManager )
		{
			return self::$shared_instance;
		}
		else
		{
			throw new SharedInstanceOfAggregateRootManagerNotYetCreated();
		}
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
		return $this->repositories[$repository_fqcn];
	}

	/**
	 * @param string                  $repository_fqcn
	 * @param AggregateRootRepository $repository
	 */
	private function trackRepository( $repository_fqcn, AggregateRootRepository $repository )
	{
		$this->repositories[$repository_fqcn] = $repository;
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
}
