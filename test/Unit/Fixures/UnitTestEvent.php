<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\RepresentsEvent;

/**
 * Class UnitTestEvent
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class UnitTestEvent implements RepresentsEvent
{

	/** @var Identifies */
	private $test_id;

	/** @var string */
	private $description;

	/**
	 * @param Identifies $test_id
	 * @param string     $description
	 */
	public function __construct( Identifies $test_id, $description )
	{
		$this->test_id     = $test_id;
		$this->description = $description;
	}

	/**
	 * @return Identifies
	 */
	public function getTestId()
	{
		return $this->test_id;
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
		return $this->test_id;
	}
}
