<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Events;

use hollodotme\MilestonES\AggregateRootIdentifier;

/**
 * Class AggregateRootWasAllocated
 *
 * @package hollodotme\MilestonES\Events
 */
class AggregateRootWasAllocated extends Event
{

	/**
	 * @var AggregateRootIdentifier
	 */
	protected $identifier;

	public function __construct( AggregateRootIdentifier $id )
	{
		$this->identifier = $id;
	}

	/**
	 * @return AggregateRootIdentifier
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * @return AggregateRootIdentifier
	 */
	public function getStreamId()
	{
		return $this->identifier;
	}
}
