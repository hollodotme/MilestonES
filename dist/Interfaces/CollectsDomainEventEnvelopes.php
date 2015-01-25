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
	 * @param callable $cmp_function
	 */
	public function sort( callable $cmp_function );
}
