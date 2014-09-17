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
	 * @param WrapsEventForCommit $event
	 */
	public function persistEventEnvelope( WrapsEventForCommit $event );

	/**
	 * @param IdentifiesEventStream $id
	 *
	 * @return WrapsEventForCommit[]
	 */
	public function getEventEnvelopesWithId( IdentifiesEventStream $id );
}
