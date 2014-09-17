<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Events;

use hollodotme\MilestonES\AggregateRootIdentifier;
use hollodotme\MilestonES\Interfaces\Identifies;

/**
 * Class AggregateRootWasAllocated
 *
 * @package hollodotme\MilestonES\Events
 */
class AggregateRootWasAllocated extends Event
{

	/** @var Identifies */
	private $identifier;

	/**
	 * @param Identifies $id
	 */
	public function __construct( Identifies $id )
	{
		$this->identifier = $id;
	}

	/**
	 * @return Identifies
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * @return Identifies
	 */
	public function getStreamId()
	{
		return $this->identifier;
	}
}
