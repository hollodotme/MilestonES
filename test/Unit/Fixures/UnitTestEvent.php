<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\Interfaces\CarriesEventData;
use hollodotme\MilestonES\Interfaces\Identifies;

/**
 * Class UnitTestEvent
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class UnitTestEvent implements CarriesEventData
{

	/** @var Identifies */
	private $testId;

	/** @var string */
	private $description;

	/**
	 * @param Identifies $testId
	 * @param string     $description
	 */
	public function __construct( Identifies $testId, $description )
	{
		$this->testId = $testId;
		$this->description = $description;
	}

	/**
	 * @return Identifies
	 */
	public function getTestId()
	{
		return $this->testId;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @return Identifies
	 */
	public function getStreamId()
	{
		return $this->testId;
	}
}
