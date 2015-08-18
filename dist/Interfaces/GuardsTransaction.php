<?php
/**
 *
 * @author hollodotme
 */
namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface GuardsTransaction
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface GuardsTransaction
{
	public function beginTransaction();

	public function commitTransaction();

	public function rollbackTransaction();

	/**
	 * @return bool
	 */
	public function isInTransaction();
}