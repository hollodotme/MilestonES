<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\CollectsAggregateRoots;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\IsIdentified;
use hollodotme\MilestonES\Interfaces\StoresEvents;

/**
 * Class AggregateRootCollection
 *
 * @package hollodotme\MilestonES
 */
class AggregateRootCollection implements CollectsAggregateRoots
{

	/**
	 * @var AggregateRoot[]
	 */
	protected $aggregate_roots = [ ];

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
	 * @param IsIdentified $identified_object
	 */
	public function attach( IsIdentified $identified_object )
	{
		if ( !$this->isAttached( $identified_object->getIdentifier() ) )
		{
			$this->aggregate_roots[ $identified_object->getIdentifier()->toString() ] = $identified_object;
		}
	}

	/**
	 * @param Identifies $id
	 *
	 * @return IsIdentified
	 */
	public function find( Identifies $id )
	{
		if ( $this->isAttached( $id ) )
		{
			return $this->aggregate_roots[ $id->toString() ];
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param Identifies $id
	 *
	 * @return bool
	 */
	public function isAttached( Identifies $id )
	{
		return isset($this->aggregate_roots[ $id->toString() ]);
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
