<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\EventStore;

require_once __DIR__ . '/../_test_classes/TestAggregateWasDescribed.php';

use hollodotme\MilestonES\EventCollection;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Interfaces\RepresentsEvent;
use hollodotme\MilestonES\Test\Unit\TestAggregateWasDescribed;

class EventCollectionTest extends \PHPUnit_Framework_TestCase
{
	public function testCollectionIsEmptyAfterContruction()
	{
		$collection = new EventCollection();

		$this->assertTrue( $collection->isEmpty() );
		$this->assertCount( 0, $collection );
	}

	public function testCollectionIsNotEmptyAfterAddingEvents()
	{
		$collection = new EventCollection();
		$event = new TestAggregateWasDescribed( new Identifier( 'Unit-Test' ) );

		$collection[] = $event;

		/** @var EventCollection $collection */
		$this->assertFalse( $collection->isEmpty() );
		$this->assertCount( 1, $collection );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\ItemDoesNotRepresentAnEvent
	 */
	public function testAddingAnItemFailsWhenItIsNotAnEventRepresentation()
	{
		$collection = new EventCollection();

		$collection[] = "I am not an event representation";
	}

	public function testCollectionTakesAutoAndConcreteIndex()
	{
		$collection   = new EventCollection();
		$first_event = new TestAggregateWasDescribed( new Identifier( 'Unit-Test-3' ) );
		$second_event = new TestAggregateWasDescribed( new Identifier( 'Unit-Test-2' ) );

		$collection[]    = $first_event;
		$collection["2"] = $second_event;

		/** @var EventCollection $collection */
		$this->assertFalse( $collection->isEmpty() );
		$this->assertCount( 2, $collection );
		$this->assertTrue( isset($collection[0]) );
		$this->assertTrue( isset($collection["2"]) );
		$this->assertSame( $first_event, $collection[0] );
		$this->assertSame( $second_event, $collection["2"] );
	}

	public function testEventsCanBeRemovedFromCollectionUsingUnset()
	{
		$collection   = new EventCollection();
		$first_event = new TestAggregateWasDescribed( new Identifier( 'Unit-Test-3' ) );
		$second_event = new TestAggregateWasDescribed( new Identifier( 'Unit-Test-2' ) );

		$collection[]    = $first_event;
		$collection["2"] = $second_event;

		unset($collection[0]);
		unset($collection["2"]);

		/** @var EventCollection $collection */
		$this->assertTrue( $collection->isEmpty() );
		$this->assertCount( 0, $collection );
		$this->assertFalse( isset($collection[0]) );
		$this->assertFalse( isset($collection["2"]) );
		$this->assertNull( $collection[0] );
		$this->assertNull( $collection["2"] );
	}

	public function testCanLoopMultipleTimesOverEventsInCollection()
	{
		$collection   = new EventCollection();
		$first_event = new TestAggregateWasDescribed( new Identifier( 'Unit-Test-0' ) );
		$second_event = new TestAggregateWasDescribed( new Identifier( 'Unit-Test-2' ) );

		$collection[]  = $first_event;
		$collection[2] = $second_event;

		echo "Loop 1:";

		/** @var RepresentsEvent $event */
		foreach ( $collection as $index => $event )
		{
			echo "\n{$index}: {$event->getStreamId()}";
		}

		echo "\nLoop 2:";

		/** @var RepresentsEvent $event */
		foreach ( $collection as $index => $event )
		{
			echo "\n{$index}: {$event->getStreamId()}";
		}

		$this->expectOutputString( "Loop 1:\n0: Unit-Test-0\n2: Unit-Test-2\nLoop 2:\n0: Unit-Test-0\n2: Unit-Test-2" );
	}
}
 