<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\Events\BaseEvent;

/**
 * Class TestAggregateWasDescribed
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestAggregateWasDescribed extends BaseEvent
{

	/** @var string */
	private $description;

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->getPayloadDTO()->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription( $description )
	{
		$this->getPayloadDTO()->description = $description;
	}
}
