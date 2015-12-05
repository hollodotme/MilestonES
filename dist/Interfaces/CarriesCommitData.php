<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface CarriesCommitData
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface CarriesCommitData extends ServesCommitData, ConsumesCommitData
{
	/**
	 * @param ServesCommitData $commitData
	 *
	 * @return static
	 */
	public static function fromCommitData( ServesCommitData $commitData );
}
