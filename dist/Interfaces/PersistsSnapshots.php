<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface PersistsSnapshots
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface PersistsSnapshots extends PersistsInTransaction
{
	/**
	 * @param CarriesSnapshotData $snapshot
	 */
	public function persistSnapshot( CarriesSnapshotData $snapshot );

	/**
	 * @param IdentifiesObject $streamId
	 *
	 * @return CarriesSnapshotData
	 */
	public function getLatestSnapshotForStreamId( IdentifiesObject $streamId );
}