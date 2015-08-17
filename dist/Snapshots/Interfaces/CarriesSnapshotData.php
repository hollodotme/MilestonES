<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Snapshots\Interfaces;

use hollodotme\MilestonES\Contract;
use hollodotme\MilestonES\Interfaces\AggregatesObjects;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;
use hollodotme\MilestonES\Snapshots\SnapshotId;

/**
 * Interface CarriesSnapshotData
 *
 * @package hollodotme\MilestonES\Snapshots\Interfaces
 */
interface CarriesSnapshotData
{
	/**
	 * @return SnapshotId
	 */
	public function getSnapshotId();

	/**
	 * @return IdentifiesObject
	 */
	public function getStreamId();

	/**
	 * @return Contract
	 */
	public function getStreamIdContract();

	/**
	 * @return AggregatesObjects
	 */
	public function getAggregateRoot();

	/**
	 * @return Contract
	 */
	public function getAggregateRootContract();

	/**
	 * @return int
	 */
	public function getAggregateRootRevision();

	/**
	 * @return float
	 */
	public function getTakenOnMicrotime();
}