<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\EventStore;

use hollodotme\MilestonES\EventEnvelope;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\ImmutableEventEnvelopeCollection;
use hollodotme\MilestonES\Test\Unit\Fixures\UnitTestEvent;

class ImmutableEventCollectionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testAddingEventsByArrayAccessAfterConstructionFails()
	{
		$collection = new ImmutableEventEnvelopeCollection( [ ] );
		$event      = new UnitTestEvent( new Identifier( 'Unit-Test-ID' ), 'Unit-Test' );

		$collection[] = new EventEnvelope( $event, [ ] );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testAddingEventsByOffsetSetAfterConstructionFails()
	{
		$collection = new ImmutableEventEnvelopeCollection( [ ] );
		$event      = new UnitTestEvent( new Identifier( 'Unit-Test-ID' ), 'Unit-Test' );

		$collection->offsetSet( null, new EventEnvelope( $event, [ ] ) );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testRemovingAnEventByUnsetAfterConstructionFails()
	{
		$event          = new UnitTestEvent( new Identifier( 'Unit-Test-ID' ), 'Unit-Test' );
		$event_envelope = new EventEnvelope( $event, [ ] );
		$collection = new ImmutableEventEnvelopeCollection( [ $event_envelope ] );

		unset($collection[0]);
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testRemovingAnEventByOffsetUnsetAfterConstructionFails()
	{
		$event          = new UnitTestEvent( new Identifier( 'Unit-Test-ID' ), 'Unit-Test' );
		$event_envelope = new EventEnvelope( $event, [ ] );
		$collection = new ImmutableEventEnvelopeCollection( [ $event_envelope ] );

		$collection->offsetUnset( 0 );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\NotAnEventEnvelope
	 */
	public function testConstructionWithArrayOfNonDomainEventEnvelopesFails()
	{
		new ImmutableEventEnvelopeCollection( [ 'I am not an event' ] );
	}
}
