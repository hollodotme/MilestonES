<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

require_once __DIR__ . '/TestAggregateWasDescribed.php';
require_once __DIR__ . '/TestAggregateWasDeleted.php';

use hollodotme\MilestonES\AggregateRoot;

/**
 * Class TestAggregateRoot
 *
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestAggregateRoot extends AggregateRoot
{

	private $description;

	public function describe()
	{
		$event = new TestAggregateWasDescribed( $this->getIdentifier() );
		$event->setDescription( 'Unit-Test' );

		$this->trackThat( $event );
	}

	public function delete()
	{
		$this->trackThat( new TestAggregateWasDeleted( $this->getIdentifier() ) );
	}

	protected function whenTestAggregateWasDeleted( TestAggregateWasDeleted $event )
	{
		$this->markAsDeleted();
	}

	protected function whenTestAggregateWasDescribed( TestAggregateWasDescribed $event )
	{
		$this->description = $event->getDescription();
	}

	public function getDescription()
	{
		return $this->description;
	}
}
