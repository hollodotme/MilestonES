<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface PersistsCommitEnvelopes
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface PersistsCommitEnvelopes extends PersistsInTransaction
{
	/**
	 * @param CarriesCommitData $commitEnvelope
	 */
	public function persistCommitEnvelope( CarriesCommitData $commitEnvelope );

	/**
	 * @param IdentifiesEventStream $eventStreamId
	 * @param int                   $revisionOffset
	 *
	 * @return ServesCommitData[]
	 */
	public function getEventStreamWithId( IdentifiesEventStream $eventStreamId, $revisionOffset = 0 );
}
