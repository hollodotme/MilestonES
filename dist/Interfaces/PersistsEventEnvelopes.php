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
	 * @param WrapsEventForCommit $event_envelope
	 */
	public function persistEventEnvelope( WrapsEventForCommit $event_envelope );

	/**
	 * @param IdentifiesEventStream $id
	 *
	 * @return WrapsEventForCommit[]
	 */
	public function getEventEnvelopesWithId( IdentifiesEventStream $id );
}
