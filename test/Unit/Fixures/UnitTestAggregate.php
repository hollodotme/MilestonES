<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\AggregateRoot;
use hollodotme\MilestonES\Identifier;

/**
 * Class UnitTestAggregate
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class UnitTestAggregate extends AggregateRoot
{

	/** @var \hollodotme\MilestonES\Interfaces\IdentifiesObject */
	private $testId;

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
		$this->testId = $event->getTestId();
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
		return $this->testId;
	}

	/**
	 * @return \hollodotme\MilestonES\Interfaces\IdentifiesObject
	 */
	public function getTestId()
	{
		return $this->testId;
	}
}
