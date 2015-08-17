<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\ApplicationStateStore;

use hollodotme\MilestonES\EventEnvelope;
use hollodotme\MilestonES\EventEnvelopeCollection;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Interfaces\CarriesEventData;
use hollodotme\MilestonES\Interfaces\ServesEventStreamData;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestEvent;

class DomainEventEnvelopeCollectionTest extends \PHPUnit_Framework_TestCase
{
	public function getTestEvent( $id )
	{
		return new UnitTestEvent( new Identifier( $id ), 'Unit-Test-Description' );
	}

	public function testCollectionIsEmptyAfterContruction()
	{
		$collection = new EventEnvelopeCollection();

		$this->assertTrue( $collection->isEmpty() );
		$this->assertCount( 0, $collection );
	}

	public function testCollectionIsNotEmptyAfterAddingEvents()
	{
		$collection    = new EventEnvelopeCollection();
		$event         = $this->getTestEvent( 'Unit-Test-ID' );
		$eventEnvelope = new EventEnvelope( 0, $event, [ ] );

		$collection[] = $eventEnvelope;

		/** @var EventEnvelopeCollection $collection */
		$this->assertFalse( $collection->isEmpty() );
		$this->assertCount( 1, $collection );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\NotAnEventEnvelope
	 */
	public function testAddingAnItemFailsWhenItIsNotADomainEventEnvelope()
	{
		$collection = new EventEnvelopeCollection();

		$collection[] = "I am not an event representation";
	}

	public function testCollectionTakesAutoAndConcreteIndex()
	{
		$collection = new EventEnvelopeCollection();

		$firstEvent  = $this->getTestEvent( 'Unit-Test-3' );
		$secondEvent = $this->getTestEvent( 'Unit-Test-2' );

		$firstEnvelope  = new EventEnvelope( 0, $firstEvent, [ ] );
		$secondEnvelope = new EventEnvelope( 1, $secondEvent, [ ] );

		$collection[]    = $firstEnvelope;
		$collection["2"] = $secondEnvelope;

		/** @var EventEnvelopeCollection $collection */
		$this->assertFalse( $collection->isEmpty() );
		$this->assertCount( 2, $collection );
		$this->assertTrue( isset($collection[0]) );
		$this->assertTrue( isset($collection["2"]) );
		$this->assertSame( $firstEnvelope, $collection[0] );
		$this->assertSame( $secondEnvelope, $collection["2"] );
	}

	public function testEnvelopesCanBeRemovedFromCollectionUsingUnset()
	{
		$collection = new EventEnvelopeCollection();

		$firstEvent  = $this->getTestEvent( 'Unit-Test-3' );
		$secondEvent = $this->getTestEvent( 'Unit-Test-2' );

		$firstEnvelope  = new EventEnvelope( 0, $firstEvent, [ ] );
		$secondEnvelope = new EventEnvelope( 1, $secondEvent, [ ] );

		$collection[]    = $firstEnvelope;
		$collection["2"] = $secondEnvelope;

		unset($collection[0]);
		unset($collection["2"]);

		/** @var EventEnvelopeCollection $collection */
		$this->assertTrue( $collection->isEmpty() );
		$this->assertCount( 0, $collection );
		$this->assertFalse( isset($collection[0]) );
		$this->assertFalse( isset($collection["2"]) );
		$this->assertNull( $collection[0] );
		$this->assertNull( $collection["2"] );
	}

	public function testCanLoopMultipleTimesOverCollection()
	{
		$collection = new EventEnvelopeCollection();

		$firstEvent  = $this->getTestEvent( 'Unit-Test-0' );
		$secondEvent = $this->getTestEvent( 'Unit-Test-2' );

		$firstEnvelope  = new EventEnvelope( 0, $firstEvent, [ ] );
		$secondEnvelope = new EventEnvelope( 1, $secondEvent, [ ] );

		$collection[]    = $firstEnvelope;
		$collection["2"] = $secondEnvelope;

		echo "Loop 1:";

		/** @var CarriesEventData $event */
		foreach ( $collection as $index => $event )
		{
			echo "\n{$index}: {$event->getStreamId()}";
		}

		echo "\nLoop 2:";

		/** @var CarriesEventData $event */
		foreach ( $collection as $index => $event )
		{
			echo "\n{$index}: {$event->getStreamId()}";
		}

		$this->expectOutputString( "Loop 1:\n0: Unit-Test-0\n2: Unit-Test-2\nLoop 2:\n0: Unit-Test-0\n2: Unit-Test-2" );
	}

	public function testCanSortCollectionByCallable()
	{
		$collection = new EventEnvelopeCollection();

		$firstEvent  = $this->getTestEvent( 'Unit-Test-1' );
		$secondEvent = $this->getTestEvent( 'Unit-Test-2' );

		$firstEnvelope  = new EventEnvelope( 0, $firstEvent, [ ] );
		$secondEnvelope = new EventEnvelope( 1, $secondEvent, [ ] );

		$collection[] = $firstEnvelope;
		$collection[] = $secondEnvelope;

		/**
		 * Sort in reverse order
		 */

		/** @var EventEnvelopeCollection $collection */
		$collection->sort(
			function ( ServesEventStreamData $a, ServesEventStreamData $b )
			{
				if ( $a->getOccurredOnMicrotime() < $b->getOccurredOnMicrotime() )
				{
					return 1;
				}
				elseif ( $a->getOccurredOnMicrotime() > $b->getOccurredOnMicrotime() )
				{
					return -1;
				}
				else
				{
					return 0;
				}
			}
		);

		$this->assertSame( $secondEnvelope, $collection[0] );
		$this->assertSame( $firstEnvelope, $collection[1] );
	}

	public function testCanAppendCollectionToCollection()
	{
		$collection = new EventEnvelopeCollection();

		$firstEvent  = $this->getTestEvent( 'Unit-Test-1' );
		$secondEvent = $this->getTestEvent( 'Unit-Test-2' );

		$firstEnvelope  = new EventEnvelope( 0, $firstEvent, [ ] );
		$secondEnvelope = new EventEnvelope( 1, $secondEvent, [ ] );

		$collection[] = $firstEnvelope;
		$collection[] = $secondEnvelope;

		$collection_to_append = new EventEnvelopeCollection();

		$thirdEvent  = $this->getTestEvent( 'Unit-Test-3' );
		$fourthEvent = $this->getTestEvent( 'Unit-Test-4' );

		$thirdEnvelope  = new EventEnvelope( 2, $thirdEvent, [ ] );
		$fourthEnvelope = new EventEnvelope( 3, $fourthEvent, [ ] );

		$collection_to_append[] = $thirdEnvelope;
		$collection_to_append[] = $fourthEnvelope;

		/** @var EventEnvelopeCollection $collection */
		/** @var EventEnvelopeCollection $collection_to_append */
		$collection->append( $collection_to_append );

		$this->assertSame( $firstEnvelope, $collection[0] );
		$this->assertSame( $secondEnvelope, $collection[1] );
		$this->assertSame( $thirdEnvelope, $collection[2] );
		$this->assertSame( $fourthEnvelope, $collection[3] );
	}
}
