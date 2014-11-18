<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Aggregates;

require_once __DIR__ . '/../Fixures/UnitTestAggregate.php';
require_once __DIR__ . '/../Fixures/UnitTestEvent.php';

use hollodotme\MilestonES\DomainEventEnvelope;
use hollodotme\MilestonES\EventStream;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Test\Unit\UnitTestAggregate;
use hollodotme\MilestonES\Test\Unit\UnitTestEvent;

class AggregateRootTest extends \PHPUnit_Framework_TestCase
{
	public function testScheduleTriggersAndAppliesChangeWithId()
	{
		$identifier     = new Identifier( 'Unit-Test-ID' );
		$aggregate_root = UnitTestAggregate::schedule( 'Unit-Test' );

		$this->assertInstanceOf( UnitTestAggregate::class, $aggregate_root );
		$this->assertTrue( $aggregate_root->hasChanges() );
		$this->assertCount( 1, $aggregate_root->getChanges() );
		$this->assertTrue( $aggregate_root->getIdentifier()->equals( $identifier ) );
	}

	public function testCanBeReconstitutedFromHistory()
	{
		$identifier  = new Identifier( 'Unit-Test-ID' );

		$event          = new UnitTestEvent( $identifier, 'Unit-Test' );
		$event_envelope = new DomainEventEnvelope( $event, [ ] );

		$stream = new EventStream( [ $event_envelope ] );

		$aggregate_root = UnitTestAggregate::reconstituteFromHistory( $stream );

		$this->assertSame( $identifier, $aggregate_root->getIdentifier() );
		$this->assertEquals( 'Unit-Test', $aggregate_root->getDescription() );
		$this->assertFalse( $aggregate_root->hasChanges() );
	}

	public function testChangesCanBeCleared()
	{
		$aggregate_root = UnitTestAggregate::schedule( 'Unit-Test' );

		$this->assertCount( 1, $aggregate_root->getChanges() );
		$this->assertTrue( $aggregate_root->hasChanges() );

		$aggregate_root->clearCommittedChanges( $aggregate_root->getChanges() );

		$this->assertCount( 0, $aggregate_root->getChanges() );
		$this->assertFalse( $aggregate_root->hasChanges() );
	}
}
