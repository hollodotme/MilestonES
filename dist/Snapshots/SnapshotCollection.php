<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Snapshots;

use hollodotme\MilestonES\Snapshots\Interfaces\CarriesSnapshotData;
use hollodotme\MilestonES\Snapshots\Interfaces\CollectsSnapshots;

/**
 * Class SnapshotCollection
 *
 * @package hollodotme\MilestonES\Snapshots
 */
final class SnapshotCollection implements CollectsSnapshots
{
	/** @var array|CarriesSnapshotData[] */
	private $snapshots;

	public function __construct()
	{
		$this->snapshots = [ ];
	}

	/**
	 * @param CarriesSnapshotData $snapshot
	 */
	public function add( CarriesSnapshotData $snapshot )
	{
		$this->snapshots[] = $snapshot;
	}

	/**
	 * @param CollectsSnapshots $snapshots
	 */
	public function clearCommitedSnapshots( CollectsSnapshots $snapshots )
	{
		$this->snapshots = array_filter(
			$this->snapshots,
			function ( CarriesSnapshotData $snapshot ) use ( $snapshots )
			{
				return !in_array( $snapshot, iterator_to_array( $snapshots ) );
			}
		);
	}

	/**
	 * @return CarriesSnapshotData
	 */
	public function current()
	{
		return current( $this->snapshots );
	}

	public function next()
	{
		next( $this->snapshots );
	}

	/**
	 * @return int|null
	 */
	public function key()
	{
		return key( $this->snapshots );
	}

	/**
	 * @return bool
	 */
	public function valid()
	{
		return (key( $this->snapshots ) !== null);
	}

	public function rewind()
	{
		reset( $this->snapshots );
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count( $this->snapshots );
	}
}