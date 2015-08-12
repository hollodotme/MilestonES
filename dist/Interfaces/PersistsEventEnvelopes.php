<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface PersistsEventEnvelopes
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface PersistsEventEnvelopes
{
	public function beginTransaction();

	public function commitTransaction();

	public function rollbackTransaction();

	/**
	 * @return bool
	 */
	public function isInTransaction();

	/**
	 * @param CarriesCommitData $commitEnvelope
	 */
	public function persistCommitEnvelope( CarriesCommitData $commitEnvelope );

	/**
	 * @param IdentifiesEventStream $id
	 *
	 * @return ServesCommitData[]
	 */
	public function getEventStreamWithId( IdentifiesEventStream $id );
}
