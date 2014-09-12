<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Events;

use hollodotme\MilestonES\Identifier;

/**
 * Class AggregateRootWasAllocated
 *
 * @package hollodotme\MilestonES\Events
 */
class AggregateRootWasAllocated extends Event
{

	/**
	 * @var Identifier
	 */
	protected $id;

	public function __construct( Identifier $id )
	{
		$this->id = $id;
	}

	/**
	 * @return Identifier
	 */
	public function getId()
	{
		return $this->id;
	}
}
