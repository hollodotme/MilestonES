<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\EventStore;

require_once __DIR__ . '/../_test_classes/TestEvent.php';

use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\ImmutableEventCollection;
use hollodotme\MilestonES\Test\Unit\TestEvent;

class ImmutableEventCollectionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testAddingEventsByArrayAccessAfterConstructionFails()
	{
		$collection = new ImmutableEventCollection( [] );
		$event      = new TestEvent( new Identifier( 'Unit-Test' ) );

		$collection[] = $event;
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testAddingEventsByOffsetSetAfterConstructionFails()
	{
		$collection = new ImmutableEventCollection( [] );
		$event      = new TestEvent( new Identifier( 'Unit-Test' ) );

		$collection->offsetSet( null, $event );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testRemovingAnEventByUnsetAfterConstructionFails()
	{
		$event      = new TestEvent( new Identifier( 'Unit-Test' ) );
		$collection = new ImmutableEventCollection( [$event] );

		unset($collection[0]);
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testRemovingAnEventByOffsetUnsetAfterConstructionFails()
	{
		$event      = new TestEvent( new Identifier( 'Unit-Test' ) );
		$collection = new ImmutableEventCollection( [$event] );

		$collection->offsetUnset( 0 );
	}
}
 