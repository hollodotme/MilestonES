<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Test\Unit\Snapshot;

use hollodotme\MilestonES\Contract;
use hollodotme\MilestonES\Snapshots\Snapshot;
use hollodotme\MilestonES\Snapshots\SnapshotId;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestAggregateRoot;

class SnapshotTest extends \PHPUnit_Framework_TestCase
{
	public function testCanGetStreamIdAndContractAfterConstruction()
	{
		$aggregateRoot = UnitTestAggregateRoot::schedule( 'Unit-Test' );
		$snapshot      = new Snapshot( SnapshotId::generate(), $aggregateRoot );

		$this->assertSame( $snapshot->getStreamId(), $aggregateRoot->getIdentifier() );
		$this->assertInstanceOf( Contract::class, $snapshot->getStreamIdContract() );
		$this->assertEquals( new Contract( $aggregateRoot->getIdentifier() ), $snapshot->getStreamIdContract() );
	}

	public function testCanGetAggregateRootRevisionAndContractAfterContruction()
	{
		$aggregateRoot = UnitTestAggregateRoot::schedule( 'Unit-Test' );
		$snapshot      = new Snapshot( SnapshotId::generate(), $aggregateRoot );

		$this->assertSame( $snapshot->getAggregateRoot(), $aggregateRoot );
		$this->assertInstanceOf( Contract::class, $snapshot->getAggregateRootContract() );
		$this->assertEquals( new Contract( $aggregateRoot ), $snapshot->getAggregateRootContract() );
		$this->assertEquals( $aggregateRoot->getRevision(), $snapshot->getAggregateRootRevision() );
	}

	public function testCanGetSnapshotMicrotimeAfterConstruction()
	{
		$aggregateRoot = UnitTestAggregateRoot::schedule( 'Unit-Test' );
		$snapshot      = new Snapshot( SnapshotId::generate(), $aggregateRoot );

		$this->assertInternalType( 'float', $snapshot->getTakenOnMicrotime() );
		$this->assertLessThanOrEqual( microtime( true ), $snapshot->getTakenOnMicrotime() );
	}

	public function testCanGetSnapshotIdAfterContruction()
	{
		$snapshotId    = SnapshotId::generate();
		$aggregateRoot = UnitTestAggregateRoot::schedule( 'Unit-Test' );
		$snapshot      = new Snapshot( $snapshotId, $aggregateRoot );

		$this->assertInstanceOf( SnapshotId::class, $snapshot->getSnapshotId() );
		$this->assertSame( $snapshotId, $snapshot->getSnapshotId() );
	}
}
