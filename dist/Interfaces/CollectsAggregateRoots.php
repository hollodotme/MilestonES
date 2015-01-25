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
	 * @param AggregatesModels $aggregate_root
	 */
	public function attach( AggregatesModels $aggregate_root );

	/**
	 * @param Identifies $id
	 *
	 * @return AggregatesModels
	 */
	public function find( Identifies $id );

	/**
	 * @param AggregatesModels $aggregate_root
	 *
	 * @return bool
	 */
	public function isAttached( AggregatesModels $aggregate_root );

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
	 * @param CollectsDomainEventEnvelopes $committed_changes
	 */
	public function clearCommittedChanges( CollectsDomainEventEnvelopes $committed_changes );
}
