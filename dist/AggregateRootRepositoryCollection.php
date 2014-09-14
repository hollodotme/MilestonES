<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\CollectsAggregateRootRepositories;
use hollodotme\MilestonES\Interfaces\CommitsChanges;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\IsIdentified;

/**
 * Class AggregateRootRepositoryCollection
 *
 * @package hollodotme\MilestonES
 */
class AggregateRootRepositoryCollection implements CollectsAggregateRootRepositories, CommitsChanges
{

	/**
	 * @var AggregateRootRepository[]
	 */
	protected $repositories = [ ];

	public function commitChanges()
	{
		foreach ( $this->repositories as $aggregate_root_repository )
		{
			$aggregate_root_repository->commitChanges();
		}
	}

	/**
	 * @return AggregateRootRepository
	 */
	public function current()
	{
		return current( $this->repositories );
	}

	public function next()
	{
		next( $this->repositories );
	}

	/**
	 * @return string
	 */
	public function key()
	{
		return key( $this->repositories );
	}

	/**
	 * @return bool
	 */
	public function valid()
	{
		return (key( $this->repositories ) !== null);
	}

	public function rewind()
	{
		reset( $this->repositories );
	}

	/**
	 * @param IsIdentified $identified_object
	 */
	public function attach( IsIdentified $identified_object )
	{
		$this->repositories[ $identified_object->getIdentifier()->toString() ] = $identified_object;
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
			return $this->repositories[ $id->toString() ];
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
		return isset($this->repositories[ $id->toString() ]);
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count( $this->repositories );
	}

	/**
	 * @return bool
	 */
	public function hasUncommittedChanges()
	{
		$has_changes = false;
		foreach ( $this->repositories as $aggregate_root_repository )
		{
			$has_changes |= $aggregate_root_repository->hasUncommittedChanges();
		}

		return $has_changes;
	}
}
