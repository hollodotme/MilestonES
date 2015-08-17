<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\AggregateRoot;

use hollodotme\MilestonES\EventEnvelope;
use hollodotme\MilestonES\EventStream;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestAggregateRoot;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestAggregateRoot;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestEvent;

class AggregateRootTest extends \PHPUnit_Framework_TestCase
{
	public function testScheduleTriggersAndAppliesChangeWithId()
	{
		$identifier    = new Identifier( 'Unit-Test-ID' );
		$aggregateRoot = UnitTestAggregateRoot::schedule( 'Unit-Test' );

		$this->assertInstanceOf( UnitTestAggregateRoot::class, $aggregateRoot );
		$this->assertTrue( $aggregateRoot->hasChanges() );
		$this->assertCount( 1, $aggregateRoot->getChanges() );
		$this->assertTrue( $aggregateRoot->getIdentifier()->equals( $identifier ) );
	}

	public function testCanBeReconstitutedFromHistory()
	{
		$identifier = new Identifier( 'Unit-Test-ID' );

		$event         = new UnitTestEvent( $identifier, 'Unit-Test' );
		$eventEnvelope = new EventEnvelope( 0, $event, [ ] );

		$stream = new EventStream( [ $eventEnvelope ] );

		$aggregateRoot = UnitTestAggregateRoot::reconstituteFromHistory( $stream );

		$this->assertSame( $identifier, $aggregateRoot->getIdentifier() );
		$this->assertEquals( 'Unit-Test', $aggregateRoot->getDescription() );
		$this->assertFalse( $aggregateRoot->hasChanges() );
	}

	public function testChangesCanBeCleared()
	{
		$aggregateRoot = UnitTestAggregateRoot::schedule( 'Unit-Test' );

		$this->assertCount( 1, $aggregateRoot->getChanges() );
		$this->assertTrue( $aggregateRoot->hasChanges() );

		$aggregateRoot->clearCommittedChanges( $aggregateRoot->getChanges() );

		$this->assertCount( 0, $aggregateRoot->getChanges() );
		$this->assertFalse( $aggregateRoot->hasChanges() );
	}

	public function testChangesRaiseAggregateRootRevision()
	{
		$aggregateRoot = TestAggregateRoot::init( 'Unit-Test', 'Unit-Test' );

		$this->assertEquals( 1, $aggregateRoot->getRevision() );

		$aggregateRoot->test( 'Test-Unit' );

		$this->assertEquals( 2, $aggregateRoot->getRevision() );
		$this->assertCount( 2, $aggregateRoot->getChanges() );
	}

	public function testChangesContainLastAggregateRootRevision()
	{
		$aggregateRoot = TestAggregateRoot::init( 'Unit-Test', 'Unit-Test' );
		$aggregateRoot->test( 'Test-Unit' );

		$this->assertEquals( 2, $aggregateRoot->getRevision() );

		foreach ( $aggregateRoot->getChanges() as $change )
		{
			echo '.' . $change->getLastRevision() . '.';
		}

		$this->expectOutputString( '.0..1.' );
	}
}
