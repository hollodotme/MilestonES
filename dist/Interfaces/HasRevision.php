<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface HasRevision
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface HasRevision
{
	/**
	 * @return int
	 */
	public function getRevision();
}