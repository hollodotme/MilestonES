<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\Snapshots\Interfaces\CarriesSnapshotData;
use hollodotme\MilestonES\Snapshots\Interfaces\CollectsSnapshots;

/**
 * Interface StoresSnapshots
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface StoresSnapshots
{
	/**
	 * @param CollectsSnapshots $snapshots
	 */
	public function commitSnapshots( CollectsSnapshots $snapshots );

	/**
	 * @param IdentifiesObject $streamId
	 *
	 * @return CarriesSnapshotData
	 */
	public function getLatestSnapshotForStreamId( IdentifiesObject $streamId );
}