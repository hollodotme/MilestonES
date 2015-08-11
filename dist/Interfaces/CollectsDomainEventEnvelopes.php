<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface CollectsDomainEventEnvelopes
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface CollectsDomainEventEnvelopes extends \Iterator, \ArrayAccess, \Countable
{
	/**
	 * @param CollectsDomainEventEnvelopes $envelopes
	 */
	public function append( CollectsDomainEventEnvelopes $envelopes );

	/**
	 * @param callable $compareFunction
	 */
	public function sort( callable $compareFunction );
}
