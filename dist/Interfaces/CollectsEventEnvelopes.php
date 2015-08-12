<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface CollectsEventEnvelopes
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface CollectsEventEnvelopes extends \Iterator, \ArrayAccess, \Countable
{
	/**
	 * @param CollectsEventEnvelopes $envelopes
	 */
	public function append( CollectsEventEnvelopes $envelopes );

	/**
	 * @param callable $compareFunction
	 */
	public function sort( callable $compareFunction );
}
