<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\AggregateRootIsMarkedAsDeleted;
use hollodotme\MilestonES\Exceptions\AggregateRootNotFound;
use hollodotme\MilestonES\Exceptions\AggregateRootWithEqualIdIsAlreadyAttached;
use hollodotme\MilestonES\Interfaces\AggregatesModels;
use hollodotme\MilestonES\Interfaces\CollectsAggregateRoots;
use hollodotme\MilestonES\Interfaces\Identifies;

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
	private $aggregate_roots = [];

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
	 * @param AggregatesModels $aggregate_root
	 *
	 * @throws AggregateRootWithEqualIdIsAlreadyAttached
	 */
	public function attach( AggregatesModels $aggregate_root )
	{
		if ( !$this->idExists( $aggregate_root->getIdentifier() ) )
		{
			$this->aggregate_roots[] = $aggregate_root;
		}
		elseif ( !$this->isAttached( $aggregate_root ) )
		{
			throw new AggregateRootWithEqualIdIsAlreadyAttached( (string)$aggregate_root->getIdentifier() );
		}
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws AggregateRootIsMarkedAsDeleted
	 * @throws AggregateRootNotFound
	 * @return AggregateRoot
	 */
	final public function find( Identifies $id )
	{
		if ( $this->idExists( $id ) )
		{
			$aggregate_root = $this->getAggregateRootWithId( $id );

			$this->guardAggregateRootIsNotDeleted( $aggregate_root );

			return $aggregate_root;
		}
		else
		{
			throw new AggregateRootNotFound( (string)$id );
		}
	}

	/**
	 * @param AggregatesModels $aggregate_root
	 *
	 * @throws AggregateRootIsMarkedAsDeleted
	 */
	private function guardAggregateRootIsNotDeleted( AggregatesModels $aggregate_root )
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
	public function idExists( Identifies $id )
	{
		$id_exists = false;

		for ( $this->rewind(); ($this->valid() && !$id_exists); $this->next() )
		{
			$id_exists = $this->current()->getIdentifier()->equals( $id );
		}

		return $id_exists;
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws AggregateRootNotFound
	 * @return AggregatesModels
	 */
	private function getAggregateRootWithId( Identifies $id )
	{
		$aggregate_root = null;

		for ( $this->rewind(); ($this->valid() && is_null( $aggregate_root )); $this->next() )
		{
			if ( $this->current()->getIdentifier()->equals( $id ) )
			{
				$aggregate_root = $this->current();
			}
		}

		return $aggregate_root;
	}

	/**
	 * @param AggregatesModels $aggregate_root
	 *
	 * @return bool
	 */
	public function isAttached( AggregatesModels $aggregate_root )
	{
		return in_array( $aggregate_root, $this->aggregate_roots, true );
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count( $this->aggregate_roots );
	}
}
