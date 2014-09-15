<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\AggregateRootsWithUncommittedChangesDetected;
use hollodotme\MilestonES\Exceptions\RepositoryWithNameDoesNotExist;
use hollodotme\MilestonES\Interfaces\CommitsChanges;
use hollodotme\MilestonES\Interfaces\StoresEvents;
use hollodotme\MilestonES\Interfaces\UnitOfWork;

/**
 * Class AggregateRootManager
 *
 * @package hollodotme\MilestonES
 */
class AggregateRootManager implements CommitsChanges
{

	/** @var UnitOfWork */
	private $unit_of_work;

	/** @var StoresEvents */
	private $event_store;

	/** @var bool */
	private $auto_commit_changes_enabled = true;

	/**
	 * @param StoresEvents $event_store
	 * @param UnitOfWork   $unit_of_work
	 */
	public function __construct( StoresEvents $event_store, UnitOfWork $unit_of_work )
	{
		$this->event_store  = $event_store;
		$this->unit_of_work = $unit_of_work;
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
				throw new AggregateRootsWithUncommittedChangesDetected();
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
		$this->unit_of_work->commitChanges( $this->event_store );
	}

	/**
	 * @return bool
	 */
	public function hasUncommittedChanges()
	{
		return $this->unit_of_work->hasUncommittedChanges();
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
		return new $repository_fqcn( $this->event_store, $this->unit_of_work );
	}
}
