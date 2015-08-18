<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Persistence;

use hollodotme\MilestonES\Exceptions\KeyNotFoundInCommittedRecords;
use hollodotme\MilestonES\Exceptions\SnapshotsNotFoundForKey;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;
use hollodotme\MilestonES\Interfaces\PersistsSnapshots;
use hollodotme\MilestonES\Snapshots\Interfaces\CarriesSnapshotData;

/**
 * Class SnapshotMemoryPersistence
 *
 * @package hollodotme\MilestonES\Persistence
 */
class SnapshotMemoryPersistence extends MemoryPersistence implements PersistsSnapshots
{
	/**
	 * @param CarriesSnapshotData $snapshot
	 */
	public function persistSnapshot( CarriesSnapshotData $snapshot )
	{
		$key = $this->buildKey( $snapshot->getStreamIdContract(), $snapshot->getStreamId() );
		$this->addRecordForKey( $key, $snapshot );
	}

	/**
	 * @param IdentifiesObject $streamIdContract
	 * @param IdentifiesObject $streamId
	 *
	 * @return string
	 */
	private function buildKey( IdentifiesObject $streamIdContract, IdentifiesObject $streamId )
	{
		return $streamIdContract . '#' . $streamId;
	}

	/**
	 * @param IdentifiesEventStream $eventStreamId
	 *
	 * @throws SnapshotsNotFoundForKey
	 * @return CarriesSnapshotData
	 */
	public function getLatestSnapshotForStreamId( IdentifiesEventStream $eventStreamId )
	{
		$key = $this->buildKey( $eventStreamId->getStreamIdContract(), $eventStreamId->getStreamId() );

		try
		{
			$committedSnapshots = $this->getCommittedRecordsForKey( $key );

			usort( $committedSnapshots, [ $this, 'sortByRevisionDescending' ] );

			return reset( $committedSnapshots );
		}
		catch ( KeyNotFoundInCommittedRecords $e )
		{
			throw new SnapshotsNotFoundForKey( $key, 0, $e );
		}
	}

	/**
	 * @param CarriesSnapshotData $snapshotA
	 * @param CarriesSnapshotData $snapshotB
	 *
	 * @return int
	 */
	protected function sortByRevisionDescending( CarriesSnapshotData $snapshotA, CarriesSnapshotData $snapshotB )
	{
		if ( $snapshotA->getAggregateRootRevision() > $snapshotB->getAggregateRootRevision() )
		{
			return -1;
		}
		elseif ( $snapshotA->getAggregateRootRevision() < $snapshotB->getAggregateRootRevision() )
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
}