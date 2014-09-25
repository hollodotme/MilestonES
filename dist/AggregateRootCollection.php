<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\AggregateRootIsMarkedAsDeleted;
use hollodotme\MilestonES\Exceptions\AggregateRootNotFound;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\StoresEvents;
use hollodotme\MilestonES\Interfaces\UnitOfWork;

/**
 * Class AggregateRootCollection
 *
 * @package hollodotme\MilestonES
 */
class AggregateRootCollection implements UnitOfWork
{

	/**
	 * @var AggregateRoot[]
	 */
	protected $aggregate_roots = [];

	/**
	 * @param StoresEvents $event_store
	 */
	public function commitChanges( StoresEvents $event_store )
	{
		foreach ( $this->aggregate_roots as $aggregate_root )
		{
			$this->commitChangesOfAggregateRoot( $aggregate_root, $event_store );
		}
	}

	/**
	 * @return AggregateRoot
	 */
	public function current()
	{
		return current( $this->aggregate_roots );
	}

	public function next()
	{
		next( $this->aggregate_roots );
	}

	/**
	 * @return string
	 */
	public function key()
	{
		return key( $this->aggregate_roots );
	}

	/**
	 * @return bool
	 */
	public function valid()
	{
		return (key( $this->aggregate_roots ) !== null);
	}

	public function rewind()
	{
		reset( $this->aggregate_roots );
	}

	/**
	 * @param AggregateRoot $aggregate_root
	 */
	public function attach( AggregateRoot $aggregate_root )
	{
		if ( !$this->isAttached( $aggregate_root->getIdentifier() ) )
		{
			$this->aggregate_roots[(string)$aggregate_root->getIdentifier()] = $aggregate_root;
		}
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws AggregateRootIsMarkedAsDeleted
	 * @throws AggregateRootNotFound
	 * @return AggregateRoot
	 */
	public function find( Identifies $id )
	{
		if ( $this->isAttached( $id ) )
		{
			$aggregate_root = $this->aggregate_roots[(string)$id];

			$this->guardAggregateRootIsNotDeleted( $aggregate_root );

			return $aggregate_root;
		}
		else
		{
			throw new AggregateRootNotFound( (string)$id );
		}
	}

	/**
	 * @param AggregateRoot $aggregate_root
	 *
	 * @throws AggregateRootIsMarkedAsDeleted
	 */
	protected function guardAggregateRootIsNotDeleted( AggregateRoot $aggregate_root )
	{
		if ( $aggregate_root->isDeleted() )
		{
			throw new AggregateRootIsMarkedAsDeleted();
		}
	}

	/**
	 * @param Identifies $id
	 *
	 * @return bool
	 */
	public function isAttached( Identifies $id )
	{
		return isset($this->aggregate_roots[(string)$id]);
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count( $this->aggregate_roots );
	}

	/**
	 * @return bool
	 */
	public function hasUncommittedChanges()
	{
		$has_changes = false;
		foreach ( $this->aggregate_roots as $aggregate_root )
		{
			$has_changes |= $aggregate_root->hasChanges();
		}

		return $has_changes;
	}

	/**
	 * @param AggregateRoot $aggregate_root
	 * @param StoresEvents  $event_store
	 */
	protected function commitChangesOfAggregateRoot( AggregateRoot $aggregate_root, StoresEvents $event_store )
	{
		if ( $aggregate_root->hasChanges() )
		{
			$events = $aggregate_root->getChanges();
			$event_store->commitEvents( $events );

			$aggregate_root->clearChanges();
		}
	}
}
