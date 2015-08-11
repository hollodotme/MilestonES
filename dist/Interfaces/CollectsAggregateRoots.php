<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface CollectsAggregateRoots
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface CollectsAggregateRoots extends \Iterator, \Countable
{
	/**
	 * @param AggregatesObjects $aggregateRoot
	 */
	public function attach( AggregatesObjects $aggregateRoot );

	/**
	 * @param Identifies $id
	 *
	 * @return AggregatesObjects
	 */
	public function find( Identifies $id );

	/**
	 * @param AggregatesObjects $aggregateRoot
	 *
	 * @return bool
	 */
	public function isAttached( AggregatesObjects $aggregateRoot );

	/**
	 * @param Identifies $id
	 *
	 * @return bool
	 */
	public function idExists( Identifies $id );

	/**
	 * @return CollectsDomainEventEnvelopes
	 */
	public function getChanges();

	/**
	 * @param CollectsDomainEventEnvelopes $committedChanges
	 */
	public function clearCommittedChanges( CollectsDomainEventEnvelopes $committedChanges );
}
