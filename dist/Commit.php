<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\IdentifiesCommit;

/**
 * Class Commit
 *
 * @package hollodotme\MilestonES
 */
final class Commit implements IdentifiesCommit
{

	/** @var CommitId */
	private $commitId;

	/** @var \DateTimeImmutable */
	private $committedOn;

	/**
	 * @param CommitId           $commitId
	 * @param \DateTimeImmutable $committedOn
	 */
	public function __construct( CommitId $commitId, \DateTimeImmutable $committedOn )
	{
		$this->commitId    = $commitId;
		$this->committedOn = $committedOn;
	}

	/**
	 * @return CommitId
	 */
	public function getCommitId()
	{
		return $this->commitId;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getCommittedOn()
	{
		return $this->committedOn;
	}
}
