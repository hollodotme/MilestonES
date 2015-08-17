<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\ApplicationStateStore;

use hollodotme\MilestonES\EventEnvelope;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\ImmutableEventEnvelopeCollection;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestEvent;

class ImmutableEventCollectionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testAddingEventsByArrayAccessAfterConstructionFails()
	{
		$collection = new ImmutableEventEnvelopeCollection( [ ] );
		$event      = new UnitTestEvent( new Identifier( 'Unit-Test-ID' ), 'Unit-Test' );

		$collection[] = new EventEnvelope( 0, $event, [ ] );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testAddingEventsByOffsetSetAfterConstructionFails()
	{
		$collection = new ImmutableEventEnvelopeCollection( [ ] );
		$event      = new UnitTestEvent( new Identifier( 'Unit-Test-ID' ), 'Unit-Test' );

		$collection->offsetSet( null, new EventEnvelope( 0, $event, [ ] ) );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testRemovingAnEventByUnsetAfterConstructionFails()
	{
		$event         = new UnitTestEvent( new Identifier( 'Unit-Test-ID' ), 'Unit-Test' );
		$eventEnvelope = new EventEnvelope( 0, $event, [ ] );
		$collection    = new ImmutableEventEnvelopeCollection( [ $eventEnvelope ] );

		unset($collection[0]);
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testRemovingAnEventByOffsetUnsetAfterConstructionFails()
	{
		$event         = new UnitTestEvent( new Identifier( 'Unit-Test-ID' ), 'Unit-Test' );
		$eventEnvelope = new EventEnvelope( 0, $event, [ ] );
		$collection    = new ImmutableEventEnvelopeCollection( [ $eventEnvelope ] );

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
