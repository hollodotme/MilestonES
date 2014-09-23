<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

require_once __DIR__ . '/TestEvent.php';

use hollodotme\MilestonES\AggregateRoot;

/**
 * Class TestAggregateRoot
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestAggregateRoot extends AggregateRoot
{

	private $description;

	public function testCommand()
	{
		$event = new TestEvent( $this->getIdentifier() );
		$event->setDescription( 'Unit-Test' );

		$this->trackThat( $event );
	}

	protected function whenTestEvent( TestEvent $event )
	{
		$this->description = $event->getDescription();
	}

	public function getDescription()
	{
		return $this->description;
	}
}
