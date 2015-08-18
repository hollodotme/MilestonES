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
interface PersistsCommitEnvelopes extends GuardsTransaction
{
	/**
	 * @param CarriesCommitData $commitEnvelope
	 */
	public function persistCommitEnvelope( CarriesCommitData $commitEnvelope );

	/**
	 * @param IdentifiesEventStream $eventStreamId
	 * @param int                   $startRevision
	 *
	 * @return ServesCommitData[]
	 */
	public function getCommitEnvelopesForStreamId( IdentifiesEventStream $eventStreamId, $startRevision = 0 );
}
