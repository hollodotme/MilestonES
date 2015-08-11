<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\AggregateRootNotFound;
use hollodotme\MilestonES\Exceptions\AggregateRootWithEqualIdIsAlreadyAttached;
use hollodotme\MilestonES\Interfaces\AggregatesObjects;
use hollodotme\MilestonES\Interfaces\CollectsAggregateRoots;
use hollodotme\MilestonES\Interfaces\CollectsDomainEventEnvelopes;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\WrapsDomainEvent;

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
	private $aggregateRoots = [ ];

	/**
	 * @return AggregateRoot
	 */
	public function current()
	{
		return current( $this->aggregateRoots );
	}

	public function next()
	{
		next( $this->aggregateRoots );
	}

	/**
	 * @return string
	 */
	public function key()
	{
		return key( $this->aggregateRoots );
	}

	/**
	 * @return bool
	 */
	public function valid()
	{
		return (key( $this->aggregateRoots ) !== null);
	}

	public function rewind()
	{
		reset( $this->aggregateRoots );
	}

	/**
	 * @param AggregatesObjects $aggregateRoot
	 *
	 * @throws AggregateRootWithEqualIdIsAlreadyAttached
	 */
	public function attach( AggregatesObjects $aggregateRoot )
	{
		if ( !$this->idExists( $aggregateRoot->getIdentifier() ) )
		{
			$this->aggregateRoots[] = $aggregateRoot;
		}
		elseif ( !$this->isAttached( $aggregateRoot ) )
		{
			throw new AggregateRootWithEqualIdIsAlreadyAttached( (string)$aggregateRoot->getIdentifier() );
		}
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws AggregateRootNotFound
	 * @return AggregateRoot
	 */
	final public function find( Identifies $id )
	{
		if ( $this->idExists( $id ) )
		{
			return $this->getAggregateRootWithId( $id );
		}
		else
		{
			throw new AggregateRootNotFound( (string)$id );
		}
	}

	/**
	 * @param Identifies $id
	 *
	 * @return bool
	 */
	public function idExists( Identifies $id )
	{
		$idExists = false;

		for ( $this->rewind(); ($this->valid() && !$idExists); $this->next() )
		{
			$idExists = $this->current()->getIdentifier()->equals( $id );
		}

		return $idExists;
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws AggregateRootNotFound
	 * @return AggregatesObjects
	 */
	private function getAggregateRootWithId( Identifies $id )
	{
		$aggregateRoot = null;

		for ( $this->rewind(); ($this->valid() && is_null( $aggregateRoot )); $this->next() )
		{
			if ( $this->current()->getIdentifier()->equals( $id ) )
			{
				$aggregateRoot = $this->current();
			}
		}

		return $aggregateRoot;
	}

	/**
	 * @param AggregatesObjects $aggregateRoot
	 *
	 * @return bool
	 */
	public function isAttached( AggregatesObjects $aggregateRoot )
	{
		return in_array( $aggregateRoot, $this->aggregateRoots, true );
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count( $this->aggregateRoots );
	}

	/**
	 * @return CollectsDomainEventEnvelopes
	 */
	public function getChanges()
	{
		$changes = new DomainEventEnvelopeCollection();

		foreach ( $this->aggregateRoots as $aggregateRoot )
		{
			if ( $aggregateRoot->hasChanges() )
			{
				$changes->append( $aggregateRoot->getChanges() );
			}
		}

		$changes->sort( $this->getChangesSortFunction() );

		return $changes;
	}

	/**
	 * @return callable
	 */
	private function getChangesSortFunction()
	{
		return function ( WrapsDomainEvent $envelopeA, WrapsDomainEvent $envelopeB )
		{
			$microtimeA = floatval( $envelopeA->getOccurredOnMicrotime() );
			$microtimeB = floatval( $envelopeB->getOccurredOnMicrotime() );

			if ( $microtimeA < $microtimeB )
			{
				return -1;
			}
			else
			{
				return 1;
			}
		};
	}

	/**
	 * @param CollectsDomainEventEnvelopes $committedChanges
	 */
	public function clearCommittedChanges( CollectsDomainEventEnvelopes $committedChanges )
	{
		foreach ( $this->aggregateRoots as $aggregateRoot )
		{
			$aggregateRoot->clearCommittedChanges( $committedChanges );
		}
	}
}
