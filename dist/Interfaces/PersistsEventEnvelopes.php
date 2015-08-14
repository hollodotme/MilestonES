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
interface PersistsEventEnvelopes extends PersistsInTransaction
{
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
