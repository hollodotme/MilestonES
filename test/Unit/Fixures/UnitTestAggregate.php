<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

require_once __DIR__ . '/UnitTestEvent.php';

use hollodotme\MilestonES\AggregateRoot;
use hollodotme\MilestonES\Identifier;

/**
 * Class UnitTestAggregate
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class UnitTestAggregate extends AggregateRoot
{

	/** @var \hollodotme\MilestonES\Interfaces\Identifies */
	private $test_id;

	/** @var string */
	private $description;

	/**
	 * @param string $text
	 *
	 * @return UnitTestAggregate
	 */
	public static function schedule( $text )
	{
		$id       = new Identifier( 'Unit-Test-ID' );
		$instance = new self();
		$instance->trackThat( new UnitTestEvent( $id, $text ), [ ] );

		return $instance;
	}

	/**
	 * @param UnitTestEvent $event
	 */
	protected function whenUnitTestEvent( UnitTestEvent $event )
	{
		$this->test_id = $event->getTestId();
		$this->description = $event->getDescription();
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @return \hollodotme\MilestonES\Interfaces\Identifies
	 */
	public function getIdentifier()
	{
		return $this->test_id;
	}

	/**
	 * @return \hollodotme\MilestonES\Interfaces\Identifies
	 */
	public function getTestId()
	{
		return $this->test_id;
	}
}