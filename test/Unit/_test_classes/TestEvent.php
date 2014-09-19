<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\Events\BaseEvent;

/**
 * Class TestEvent
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEvent extends BaseEvent
{

	/** @var string */
	private $description;

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription( $description )
	{
		$this->description = $description;
	}

	/**
	 * @return \stdClass
	 */
	public function getPayload()
	{
		$payload              = new \stdClass();
		$payload->description = $this->description;

		return $payload;
	}

	/**
	 * @param \stdClass $payload
	 */
	public function reconstituteFromPayload( $payload )
	{
		$this->description = $payload->description;
	}
}