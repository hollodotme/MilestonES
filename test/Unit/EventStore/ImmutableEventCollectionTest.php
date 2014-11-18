<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\EventStore;

require_once __DIR__ . '/../Fixures/UnitTestEvent.php';

use hollodotme\MilestonES\DomainEventEnvelope;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\ImmutableDomainEventEnvelopeCollection;
use hollodotme\MilestonES\Test\Unit\UnitTestEvent;

class ImmutableEventCollectionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testAddingEventsByArrayAccessAfterConstructionFails()
	{
		$collection = new ImmutableDomainEventEnvelopeCollection( [ ] );
		$event      = new UnitTestEvent( new Identifier( 'Unit-Test-ID' ), 'Unit-Test' );

		$collection[] = new DomainEventEnvelope( $event, [ ] );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testAddingEventsByOffsetSetAfterConstructionFails()
	{
		$collection = new ImmutableDomainEventEnvelopeCollection( [ ] );
		$event      = new UnitTestEvent( new Identifier( 'Unit-Test-ID' ), 'Unit-Test' );

		$collection->offsetSet( null, new DomainEventEnvelope( $event, [ ] ) );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testRemovingAnEventByUnsetAfterConstructionFails()
	{
		$event          = new UnitTestEvent( new Identifier( 'Unit-Test-ID' ), 'Unit-Test' );
		$event_envelope = new DomainEventEnvelope( $event, [ ] );
		$collection     = new ImmutableDomainEventEnvelopeCollection( [ $event_envelope ] );

		unset($collection[0]);
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventCollectionIsImmutable
	 */
	public function testRemovingAnEventByOffsetUnsetAfterConstructionFails()
	{
		$event          = new UnitTestEvent( new Identifier( 'Unit-Test-ID' ), 'Unit-Test' );
		$event_envelope = new DomainEventEnvelope( $event, [ ] );
		$collection     = new ImmutableDomainEventEnvelopeCollection( [ $event_envelope ] );

		$collection->offsetUnset( 0 );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\ItemDoesNotRepresentADomainEventEnvelope
	 */
	public function testConstructionWithArrayOfNonDomainEventEnvelopesFails()
	{
		new ImmutableDomainEventEnvelopeCollection( [ 'I am not an event' ] );
	}
}
