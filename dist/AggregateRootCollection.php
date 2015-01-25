<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\AggregateRootNotFound;
use hollodotme\MilestonES\Exceptions\AggregateRootWithEqualIdIsAlreadyAttached;
use hollodotme\MilestonES\Interfaces\AggregatesModels;
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
	private $aggregate_roots = [ ];

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

	/**
	 * @return CollectsDomainEventEnvelopes
	 */
	public function getChanges()
	{
		$changes = new DomainEventEnvelopeCollection();

		foreach ( $this->aggregate_roots as $aggregate_root )
		{
			if ( $aggregate_root->hasChanges() )
			{
				$changes->append( $aggregate_root->getChanges() );
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
		return function ( WrapsDomainEvent $envelope_a, WrapsDomainEvent $envelope_b )
		{
			$microtime_a = floatval( $envelope_a->getOccurredOnMicrotime() );
			$microtime_b = floatval( $envelope_b->getOccurredOnMicrotime() );

			if ( $microtime_a < $microtime_b )
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
	 * @param CollectsDomainEventEnvelopes $committed_changes
	 */
	public function clearCommittedChanges( CollectsDomainEventEnvelopes $committed_changes )
	{
		foreach ( $this->aggregate_roots as $aggregate_root )
		{
			$aggregate_root->clearCommittedChanges( $committed_changes );
		}
	}
}
