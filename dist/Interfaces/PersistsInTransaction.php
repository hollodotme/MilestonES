<?php
/**
 *
 * @author hollodotme
 */
namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface PersistsInTransaction
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface PersistsInTransaction
{
	public function beginTransaction();

	public function commitTransaction();

	public function rollbackTransaction();

	/**
	 * @return bool
	 */
	public function isInTransaction();
}