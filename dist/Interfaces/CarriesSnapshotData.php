<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\Contract;
use hollodotme\MilestonES\SnapshotId;

/**
 * Interface CarriesSnapshotData
 *
 * @package hollodotme\MilestonES\Interfaces
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
	 * @return float
	 */
	public function getTakenOnMicrotime();
}