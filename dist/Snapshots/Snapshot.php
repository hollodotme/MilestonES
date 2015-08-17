<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Snapshots;

use hollodotme\MilestonES\Contract;
use hollodotme\MilestonES\Interfaces\AggregatesObjects;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;
use hollodotme\MilestonES\Snapshots\Interfaces\CarriesSnapshotData;

/**
 * Class Snapshot
 *
 * @package hollodotme\MilestonES
 */
final class Snapshot implements CarriesSnapshotData
{
	/** @var SnapshotId */
	private $snapshotId;

	/** @var IdentifiesObject */
	private $streamId;

	/** @var Contract */
	private $streamIdContract;

	/** @var AggregatesObjects */
	private $aggregateRoot;

	/** @var Contract */
	private $aggregateRootContract;

	/** @var int */
	private $aggregateRootRevision;

	/** @var float */
	private $takenOnMicrotime;

	/**
	 * @param SnapshotId        $snapshotId
	 * @param AggregatesObjects $aggregateRoot
	 */
	public function __construct( SnapshotId $snapshotId, AggregatesObjects $aggregateRoot )
	{
		$this->snapshotId            = $snapshotId;
		$this->streamId              = $aggregateRoot->getIdentifier();
		$this->streamIdContract      = new Contract( $aggregateRoot->getIdentifier() );
		$this->aggregateRoot         = $aggregateRoot;
		$this->aggregateRootContract = new Contract( $aggregateRoot );
		$this->aggregateRootRevision = $aggregateRoot->getRevision();
		$this->takenOnMicrotime      = microtime( true );
	}

	/**
	 * @return SnapshotId
	 */
	public function getSnapshotId()
	{
		return $this->snapshotId;
	}

	/**
	 * @return IdentifiesObject
	 */
	public function getStreamId()
	{
		return $this->streamId;
	}

	/**
	 * @return Contract
	 */
	public function getStreamIdContract()
	{
		return $this->streamIdContract;
	}

	/**
	 * @return AggregatesObjects
	 */
	public function getAggregateRoot()
	{
		return $this->aggregateRoot;
	}

	/**
	 * @return Contract
	 */
	public function getAggregateRootContract()
	{
		return $this->aggregateRootContract;
	}

	/**
	 * @return int
	 */
	public function getAggregateRootRevision()
	{
		return $this->aggregateRootRevision;
	}

	/**
	 * @return float
	 */
	public function getTakenOnMicrotime()
	{
		return $this->takenOnMicrotime;
	}
}