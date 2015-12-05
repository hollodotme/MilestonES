<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\AggregateRoot;

/**
 * Class UnitTestAggregateDiff
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class UnitTestAggregateDiff extends AggregateRoot
{

	/** @var \hollodotme\MilestonES\Interfaces\IdentifiesObject */
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
		$id       = new TestIdentifier( 'Unit-Test-ID' );
		$instance = new self();
		$instance->trackThat( new UnitTestEvent( $id, $text ), [ ] );

		return $instance;
	}

	/**
	 * @param UnitTestEvent $event
	 */
	protected function whenUnitTestEvent( UnitTestEvent $event )
	{
		$this->test_id     = $event->getTestId();
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
	 * @return \hollodotme\MilestonES\Interfaces\IdentifiesObject
	 */
	public function getIdentifier()
	{
		return $this->test_id;
	}

	/**
	 * @return \hollodotme\MilestonES\Interfaces\IdentifiesObject
	 */
	public function getTestId()
	{
		return $this->test_id;
	}
}
