<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\IdentifiesCommit;

/**
 * Class Commit
 *
 * @package hollodotme\MilestonES
 */
final class Commit implements IdentifiesCommit
{

	/** @var Identifies */
	private $id;

	/** @var \DateTime */
	private $date_time;

	/**
	 * @param Identifies $commid_id
	 * @param \DateTime  $commit_date_time
	 */
	public function __construct( Identifies $commid_id, \DateTime $commit_date_time )
	{
		$this->id        = $commid_id;
		$this->date_time = $commit_date_time;
	}

	/**
	 * @return Identifies
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateTime()
	{
		return $this->date_time;
	}
}
