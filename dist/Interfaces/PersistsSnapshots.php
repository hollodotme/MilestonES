<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\Snapshots\Interfaces\CarriesSnapshotData;

/**
 * Interface PersistsSnapshots
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface PersistsSnapshots extends GuardsTransaction
{
	/**
	 * @param CarriesSnapshotData $snapshot
	 */
	public function persistSnapshot( CarriesSnapshotData $snapshot );

	/**
	 * @param IdentifiesEventStream $eventStreamId
	 *
	 * @return CarriesSnapshotData
	 */
	public function getLatestSnapshotForStreamId( IdentifiesEventStream $eventStreamId );
}