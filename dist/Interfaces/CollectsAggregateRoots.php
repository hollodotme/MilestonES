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
	 * @param IdentifiesObject $id
	 *
	 * @return AggregatesObjects
	 */
	public function find( IdentifiesObject $id );

	/**
	 * @param AggregatesObjects $aggregateRoot
	 *
	 * @return bool
	 */
	public function isAttached( AggregatesObjects $aggregateRoot );

	/**
	 * @param IdentifiesObject $id
	 *
	 * @return bool
	 */
	public function idExists( IdentifiesObject $id );

	/**
	 * @return CollectsEventEnvelopes
	 */
	public function getChanges();

	/**
	 * @param CollectsEventEnvelopes $committedChanges
	 */
	public function clearCommittedChanges( CollectsEventEnvelopes $committedChanges );
}
