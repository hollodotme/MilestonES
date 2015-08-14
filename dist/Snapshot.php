<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\AggregatesObjects;
use hollodotme\MilestonES\Interfaces\CarriesSnapshotData;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;

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
	 * @return float
	 */
	public function getTakenOnMicrotime()
	{
		return $this->takenOnMicrotime;
	}
}