<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Snapshots\Interfaces;

/**
 * Interface CollectsSnapshots
 *
 * @package hollodotme\MilestonES\Snapshots\Interfaces
 */
interface CollectsSnapshots extends \Countable, \Iterator
{
	/**
	 * @param CarriesSnapshotData $snapshot
	 */
	public function add( CarriesSnapshotData $snapshot );

	/**
	 * @param CollectsSnapshots|CarriesSnapshotData[] $snapshots
	 */
	public function clearCommitedSnapshots( CollectsSnapshots $snapshots );
}