<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Aggregates;

use hollodotme\MilestonES\EventEnvelope;
use hollodotme\MilestonES\EventStream;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Test\Unit\Fixures\UnitTestAggregate;
use hollodotme\MilestonES\Test\Unit\Fixures\UnitTestEvent;

class AggregateRootTest extends \PHPUnit_Framework_TestCase
{
	public function testScheduleTriggersAndAppliesChangeWithId()
	{
		$identifier    = new Identifier( 'Unit-Test-ID' );
		$aggregateRoot = UnitTestAggregate::schedule( 'Unit-Test' );

		$this->assertInstanceOf( UnitTestAggregate::class, $aggregateRoot );
		$this->assertTrue( $aggregateRoot->hasChanges() );
		$this->assertCount( 1, $aggregateRoot->getChanges() );
		$this->assertTrue( $aggregateRoot->getIdentifier()->equals( $identifier ) );
	}

	public function testCanBeReconstitutedFromHistory()
	{
		$identifier = new Identifier( 'Unit-Test-ID' );

		$event         = new UnitTestEvent( $identifier, 'Unit-Test' );
		$eventEnvelope = new EventEnvelope( $event, [ ] );

		$stream = new EventStream( [ $eventEnvelope ] );

		$aggregateRoot = UnitTestAggregate::reconstituteFromHistory( $stream );

		$this->assertSame( $identifier, $aggregateRoot->getIdentifier() );
		$this->assertEquals( 'Unit-Test', $aggregateRoot->getDescription() );
		$this->assertFalse( $aggregateRoot->hasChanges() );
	}

	public function testChangesCanBeCleared()
	{
		$aggregateRoot = UnitTestAggregate::schedule( 'Unit-Test' );

		$this->assertCount( 1, $aggregateRoot->getChanges() );
		$this->assertTrue( $aggregateRoot->hasChanges() );

		$aggregateRoot->clearCommittedChanges( $aggregateRoot->getChanges() );

		$this->assertCount( 0, $aggregateRoot->getChanges() );
		$this->assertFalse( $aggregateRoot->hasChanges() );
	}
}
