<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\CannotCreateEventStoreForRepositoryId;
use hollodotme\MilestonES\Exceptions\ObjectLifetimeEndedWithUncommittedChanges;
use hollodotme\MilestonES\Exceptions\RepositoryWithNameDoesNotExist;
use hollodotme\MilestonES\Interfaces\CollectsAggregateRootRepositories;
use hollodotme\MilestonES\Interfaces\CommitsChanges;
use hollodotme\MilestonES\Interfaces\Identifies;

/**
 * Class AggregateRootManager
 *
 * @package hollodotme\MilestonES
 */
class AggregateRootManager implements CommitsChanges
{

	/** @var AggregateRootRepositoryCollection */
	protected $respository_collection;

	/** @var bool */
	private $auto_commit_changes_enabled = true;

	/**
	 * @param CollectsAggregateRootRepositories $collection
	 */
	public function __construct( CollectsAggregateRootRepositories $collection )
	{
		$this->respository_collection = $collection;
	}

	public function __destruct()
	{
		if ( $this->hasUncommittedChanges() )
		{
			if ( $this->isAutoCommitOfChangesEnabled() )
			{
				$this->commitChanges();
			}
			else
			{
				throw new ObjectLifetimeEndedWithUncommittedChanges();
			}
		}
	}

	public function enableAutoCommitOfChanges()
	{
		$this->auto_commit_changes_enabled = true;
	}

	public function disableAutoCommitOfChanges()
	{
		$this->auto_commit_changes_enabled = false;
	}

	/**
	 * @return bool
	 */
	public function isAutoCommitOfChangesEnabled()
	{
		return $this->auto_commit_changes_enabled;
	}

	public function commitChanges()
	{
		$this->respository_collection->commitChanges();
	}

	/**
	 * @return bool
	 */
	public function hasUncommittedChanges()
	{
		return $this->respository_collection->hasUncommittedChanges();
	}

	/**
	 * @param string $aggregate_root_fqcn
	 *
	 * @return AggregateRootRepository
	 */
	public function getRepository( $aggregate_root_fqcn )
	{
		$respoitory_id = $this->getAggregateRootRepositoryId( $aggregate_root_fqcn );

		if ( $this->isAggregateRootRepositoryAttached( $respoitory_id ) )
		{
			return $this->getAttachedAggregateRootRepository( $respoitory_id );
		}
		else
		{
			$repository = $this->createAggregateRootRepository( $respoitory_id );

			$this->attachAggregateRootRepository( $respoitory_id, $repository );

			return $repository;
		}
	}

	/**
	 * @param string $aggregate_root_fqcn
	 *
	 * @return ClassNameIdentifier
	 */
	protected function getAggregateRootRepositoryId( $aggregate_root_fqcn )
	{
		return new ClassNameIdentifier( $aggregate_root_fqcn . 'Repository' );
	}

	/**
	 * @param $repository_id
	 *
	 * @throws CannotCreateEventStoreForRepositoryId
	 * @throws RepositoryWithNameDoesNotExist
	 * @return AggregateRootRepository|null
	 */
	protected function createAggregateRootRepository( ClassNameIdentifier $repository_id )
	{
		$repository_fqcn = $repository_id->getFullQualifiedClassName();
		if ( class_exists( $repository_fqcn, true ) )
		{
			$repository = $this->createAggregateRootRepositoryById( $repository_id );
		}
		else
		{
			$repository = null;
			throw new RepositoryWithNameDoesNotExist( $repository_id->toString() );
		}

		return $repository;
	}

	/**
	 * @param ClassNameIdentifier $repository_id
	 *
	 * @throws CannotCreateEventStoreForRepositoryId
	 * @return AggregateRootRepository|null
	 */
	protected function createAggregateRootRepositoryById( ClassNameIdentifier $repository_id )
	{
		$event_store = $this->createEventStoreForAggregateRootRepositoryId( $repository_id );
		if ( !is_null( $event_store ) )
		{
			$repository_fqcn = $repository_id->getFullQualifiedClassName();

			return new $repository_fqcn( $repository_id, $event_store );
		}
		else
		{
			throw new CannotCreateEventStoreForRepositoryId( $repository_id->toString() );
		}
	}

	/**
	 * @param AggregateRootRepository $repository
	 */
	protected function attachAggregateRootRepository( AggregateRootRepository $repository )
	{
		$this->respository_collection->attach( $repository );
	}

	/**
	 * @param Identifies $repository_id
	 *
	 * @return bool
	 */
	protected function isAggregateRootRepositoryAttached( ClassNameIdentifier $repository_id )
	{
		return $this->respository_collection->isAttached( $repository_id );
	}

	/**
	 * @param ClassNameIdentifier $repository_id
	 *
	 * @return AggregateRootRepository
	 */
	protected function getAttachedAggregateRootRepository( ClassNameIdentifier $repository_id )
	{
		return $this->respository_collection->find( $repository_id );
	}

	/**
	 * @param ClassNameIdentifier $repository_id
	 *
	 * @return Interfaces\StoresEvents|null
	 */
	protected function createEventStoreForAggregateRootRepositoryId( ClassNameIdentifier $repository_id )
	{
		return EventStore::factoryForAggregateRootRepositoryId( $repository_id );
	}

	private function __clone()
	{
	}

	/**
	 * @return static
	 */
	public static function shared()
	{
		static $instance = null;

		if ( is_null( $instance ) )
		{
			$instance = new static( new AggregateRootRepositoryCollection() );
		}

		return $instance;
	}
}
