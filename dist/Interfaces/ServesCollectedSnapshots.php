<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\Snapshots\Interfaces\CarriesSnapshotData;
use hollodotme\MilestonES\Snapshots\Interfaces\CollectsSnapshots;

/**
 * Interface ServesCollectedSnapshots
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface ServesCollectedSnapshots
{
	/**
	 * @return CollectsSnapshots|CarriesSnapshotData[]
	 */
	public function getCollectedSnapshots();
}