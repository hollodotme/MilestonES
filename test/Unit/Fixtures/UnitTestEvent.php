<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixtures;

use hollodotme\MilestonES\Interfaces\CarriesEventData;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;

/**
 * Class UnitTestEvent
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class UnitTestEvent implements CarriesEventData
{

	/** @var IdentifiesObject */
	private $testId;

	/** @var string */
	private $description;

	/**
	 * @param IdentifiesObject $testId
	 * @param string     $description
	 */
	public function __construct( IdentifiesObject $testId, $description )
	{
		$this->testId = $testId;
		$this->description = $description;
	}

	/**
	 * @return IdentifiesObject
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
	 * @return IdentifiesObject
	 */
	public function getStreamId()
	{
		return $this->testId;
	}
}
