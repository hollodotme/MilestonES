<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Test\Unit\Snapshot;

use hollodotme\MilestonES\Snapshots\Interfaces\CarriesSnapshotData;
use hollodotme\MilestonES\Snapshots\Snapshot;
use hollodotme\MilestonES\Snapshots\SnapshotCollection;
use hollodotme\MilestonES\Snapshots\SnapshotId;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestAggregate;

class SnapshotCollectionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param array $snapshots
	 * @param int   $expectedCount
	 *
	 * @dataProvider snapshotProvider
	 */
	public function testCanAddSnapshots( array $snapshots, $expectedCount )
	{
		$collection = new SnapshotCollection();
		foreach ( $snapshots as $snapshot )
		{
			$collection->add( $snapshot );
		}

		$this->assertCount( $expectedCount, $collection );
		$this->assertEquals( $expectedCount, $collection->count() );
	}

	public function snapshotProvider()
	{
		return [
			[
				[ ], 0,
			],
			[
				[
					new Snapshot( SnapshotId::generate(), UnitTestAggregate::schedule( 'Unit-Test' ) ),
				],
				1,
			],
			[
				[
					new Snapshot( SnapshotId::generate(), UnitTestAggregate::schedule( 'Unit-Test-1' ) ),
					new Snapshot( SnapshotId::generate(), UnitTestAggregate::schedule( 'Unit-Test-2' ) ),
				],
				2,
			],
		];
	}

	/**
	 * @param array $snapshots
	 * @param int   $expectedCount
	 *
	 * @dataProvider snapshotProvider
	 */
	public function testCanIterateOverSnapshotCollectionMultipleTimes( array $snapshots, $expectedCount )
	{
		$collection = new SnapshotCollection();
		foreach ( $snapshots as $snapshot )
		{
			$collection->add( $snapshot );
		}

		$count = 0;
		for ( $collection->rewind(); $collection->valid(); $collection->next() )
		{
			$count++;
			$this->assertInstanceOf( CarriesSnapshotData::class, $collection->current() );
		}

		$this->assertEquals( $expectedCount, $count );

		$count = 0;
		for ( $collection->rewind(); $collection->valid(); $collection->next() )
		{
			$count++;
			$this->assertInstanceOf( CarriesSnapshotData::class, $collection->current() );
		}

		$this->assertEquals( $expectedCount, $count );
	}

	/**
	 * @param array $snapshots
	 *
	 * @dataProvider snapshotProvider
	 */
	public function testIterationKeyIsAlwaysNumeric( array $snapshots )
	{
		$collection = new SnapshotCollection();
		foreach ( $snapshots as $snapshot )
		{
			$collection->add( $snapshot );
		}

		for ( $collection->rewind(); $collection->valid(); $collection->next() )
		{
			$this->assertInternalType( 'integer', $collection->key() );
		}

		foreach ( $collection as $key => $snapshot )
		{
			$this->assertInternalType( 'integer', $key );
		}
	}

	public function testCanClearCommittedSnapshots()
	{
		$snapshot1 = new Snapshot( SnapshotId::generate(), UnitTestAggregate::schedule( 'Unit-Test-1' ) );
		$snapshot2 = new Snapshot( SnapshotId::generate(), UnitTestAggregate::schedule( 'Unit-Test-2' ) );
		$snapshot3 = new Snapshot( SnapshotId::generate(), UnitTestAggregate::schedule( 'Unit-Test-3' ) );

		$collection = new SnapshotCollection();
		$collection->add( $snapshot1 );
		$collection->add( $snapshot2 );
		$collection->add( $snapshot3 );

		$committedCollection = new SnapshotCollection();
		$committedCollection->add( $snapshot1 );
		$committedCollection->add( $snapshot2 );

		$collection->clearCommitedSnapshots( $committedCollection );

		$this->assertCount( 1, $collection );
		$this->assertSame( $collection->current(), $snapshot3 );
	}
}
