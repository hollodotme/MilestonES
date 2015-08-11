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
	 * @param WrapsEventForCommit $commitEnvelope
	 */
	public function persistEventEnvelope( WrapsEventForCommit $commitEnvelope );

	/**
	 * @param IdentifiesEventStream $id
	 *
	 * @return WrapsEventForCommit[]
	 */
	public function getEventEnvelopesWithId( IdentifiesEventStream $id );
}
