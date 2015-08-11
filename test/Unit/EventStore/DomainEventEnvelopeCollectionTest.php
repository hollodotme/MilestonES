<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\EventStore;

use hollodotme\MilestonES\DomainEventEnvelope;
use hollodotme\MilestonES\DomainEventEnvelopeCollection;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Interfaces\CarriesEventData;
use hollodotme\MilestonES\Interfaces\WrapsDomainEvent;
use hollodotme\MilestonES\Test\Unit\Fixures\UnitTestEvent;

class DomainEventEnvelopeCollectionTest extends \PHPUnit_Framework_TestCase
{
	public function getTestEvent( $id )
	{
		return new UnitTestEvent( new Identifier( $id ), 'Unit-Test-Description' );
	}

	public function testCollectionIsEmptyAfterContruction()
	{
		$collection = new DomainEventEnvelopeCollection();

		$this->assertTrue( $collection->isEmpty() );
		$this->assertCount( 0, $collection );
	}

	public function testCollectionIsNotEmptyAfterAddingEvents()
	{
		$collection     = new DomainEventEnvelopeCollection();
		$event          = $this->getTestEvent( 'Unit-Test-ID' );
		$event_envelope = new DomainEventEnvelope( $event, [ ] );

		$collection[] = $event_envelope;

		/** @var DomainEventEnvelopeCollection $collection */
		$this->assertFalse( $collection->isEmpty() );
		$this->assertCount( 1, $collection );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\ItemDoesNotRepresentADomainEventEnvelope
	 */
	public function testAddingAnItemFailsWhenItIsNotADomainEventEnvelope()
	{
		$collection = new DomainEventEnvelopeCollection();

		$collection[] = "I am not an event representation";
	}

	public function testCollectionTakesAutoAndConcreteIndex()
	{
		$collection = new DomainEventEnvelopeCollection();

		$first_event  = $this->getTestEvent( 'Unit-Test-3' );
		$second_event = $this->getTestEvent( 'Unit-Test-2' );

		$first_envelope  = new DomainEventEnvelope( $first_event, [ ] );
		$second_envelope = new DomainEventEnvelope( $second_event, [ ] );

		$collection[]    = $first_envelope;
		$collection["2"] = $second_envelope;

		/** @var DomainEventEnvelopeCollection $collection */
		$this->assertFalse( $collection->isEmpty() );
		$this->assertCount( 2, $collection );
		$this->assertTrue( isset($collection[0]) );
		$this->assertTrue( isset($collection["2"]) );
		$this->assertSame( $first_envelope, $collection[0] );
		$this->assertSame( $second_envelope, $collection["2"] );
	}

	public function testEnvelopesCanBeRemovedFromCollectionUsingUnset()
	{
		$collection = new DomainEventEnvelopeCollection();

		$first_event  = $this->getTestEvent( 'Unit-Test-3' );
		$second_event = $this->getTestEvent( 'Unit-Test-2' );

		$first_envelope  = new DomainEventEnvelope( $first_event, [ ] );
		$second_envelope = new DomainEventEnvelope( $second_event, [ ] );

		$collection[]    = $first_envelope;
		$collection["2"] = $second_envelope;

		unset($collection[0]);
		unset($collection["2"]);

		/** @var DomainEventEnvelopeCollection $collection */
		$this->assertTrue( $collection->isEmpty() );
		$this->assertCount( 0, $collection );
		$this->assertFalse( isset($collection[0]) );
		$this->assertFalse( isset($collection["2"]) );
		$this->assertNull( $collection[0] );
		$this->assertNull( $collection["2"] );
	}

	public function testCanLoopMultipleTimesOverCollection()
	{
		$collection = new DomainEventEnvelopeCollection();

		$first_event  = $this->getTestEvent( 'Unit-Test-0' );
		$second_event = $this->getTestEvent( 'Unit-Test-2' );

		$first_envelope  = new DomainEventEnvelope( $first_event, [ ] );
		$second_envelope = new DomainEventEnvelope( $second_event, [ ] );

		$collection[]    = $first_envelope;
		$collection["2"] = $second_envelope;

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
		$collection = new DomainEventEnvelopeCollection();

		$first_event  = $this->getTestEvent( 'Unit-Test-1' );
		$second_event = $this->getTestEvent( 'Unit-Test-2' );

		$first_envelope  = new DomainEventEnvelope( $first_event, [ ] );
		$second_envelope = new DomainEventEnvelope( $second_event, [ ] );

		$collection[] = $first_envelope;
		$collection[] = $second_envelope;

		/**
		 * Sort in reverse order
		 */

		/** @var DomainEventEnvelopeCollection $collection */
		$collection->sort(
			function ( WrapsDomainEvent $a, WrapsDomainEvent $b )
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

		$this->assertSame( $second_envelope, $collection[0] );
		$this->assertSame( $first_envelope, $collection[1] );
	}

	public function testCanAppendCollectionToCollection()
	{
		$collection = new DomainEventEnvelopeCollection();

		$first_event  = $this->getTestEvent( 'Unit-Test-1' );
		$second_event = $this->getTestEvent( 'Unit-Test-2' );

		$first_envelope  = new DomainEventEnvelope( $first_event, [ ] );
		$second_envelope = new DomainEventEnvelope( $second_event, [ ] );

		$collection[] = $first_envelope;
		$collection[] = $second_envelope;

		$collection_to_append = new DomainEventEnvelopeCollection();

		$third_event  = $this->getTestEvent( 'Unit-Test-3' );
		$fourth_event = $this->getTestEvent( 'Unit-Test-4' );

		$third_envelope  = new DomainEventEnvelope( $third_event, [ ] );
		$fourth_envelope = new DomainEventEnvelope( $fourth_event, [ ] );

		$collection_to_append[] = $third_envelope;
		$collection_to_append[] = $fourth_envelope;

		/** @var DomainEventEnvelopeCollection $collection */
		/** @var DomainEventEnvelopeCollection $collection_to_append */
		$collection->append( $collection_to_append );

		$this->assertSame( $first_envelope, $collection[0] );
		$this->assertSame( $second_envelope, $collection[1] );
		$this->assertSame( $third_envelope, $collection[2] );
		$this->assertSame( $fourth_envelope, $collection[3] );
	}
}
