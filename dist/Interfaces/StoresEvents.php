<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\Exceptions\CommitingEventStreamFailed;
use hollodotme\MilestonES\ImmutableEventStream;
use Interfaces\IdentifiesStream;

/**
 * Interface StoresEvents
 * @package hollodotme\MilestonES\Interfaces
 */
interface StoresEvents
{
	/**
	 * @param StreamsEvents $pending_events
	 *
	 * @throws CommitingEventStreamFailed
	 *
	 * @return void
	 */
	public function commit( StreamsEvents $pending_events );

	/**
	 * @param StreamsEvents $committed_events
	 *
	 * @return void
	 */
	public function publish( StreamsEvents $committed_events );

	/**
	 * @param IdentifiesStream $stream_id
	 *
	 * @return ImmutableEventStream
	 */
	public function fetch( IdentifiesStream $stream_id );
} 