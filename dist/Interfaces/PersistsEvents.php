<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface PersistsEvents
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface PersistsEvents
{
	public function beginTransaction();

	public function commitTransaction();

	public function rollbackTransaction();

	public function persistEvent( Event $event );

	public function getEventsWithId( IdentifiesEventStream $id );
}
