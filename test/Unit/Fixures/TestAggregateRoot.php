<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Test\Unit\Fixures;

use hollodotme\MilestonES\AggregateRoot;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;

/**
 * Class TestAggregateRoot
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
final class TestAggregateRoot extends AggregateRoot
{

	private $id;

	public static function init( $id, $text )
	{
		$instance = new self();
		$instance->trackThat( new UnitTestEvent( new Identifier( $id ), $text ), [ ] );

		return $instance;
	}

	protected function whenUnitTestEvent( UnitTestEvent $event )
	{
		$this->id = $event->getTestId();
	}

	public function test( $text )
	{
		$this->trackThat( new UnitTestEvent( $this->id, $text ), [ ] );
	}

	/**
	 * @return IdentifiesObject
	 */
	public function getIdentifier()
	{
		return $this->id;
	}
}