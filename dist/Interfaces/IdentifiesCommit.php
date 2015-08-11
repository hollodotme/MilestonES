<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface IdentifiesCommit
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface IdentifiesCommit
{
	/**
	 * @return Identifies
	 */
	public function getCommitId();

	/**
	 * @return \DateTimeImmutable
	 */
	public function getCommittedOn();
}
