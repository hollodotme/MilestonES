<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\CarriesCommitData;
use hollodotme\MilestonES\Interfaces\ServesCommitData;

/**
 * Class CommitEnvelope
 *
 * @package hollodotme\MilestonES
 */
final class CommitEnvelope implements CarriesCommitData
{

	/** @var int */
	private $id;

	/** @var string */
	private $commitId;

	/** @var string */
	private $streamId;

	/** @var string */
	private $streamIdContract;

	/** @var string */
	private $payload;

	/** @var string */
	private $payloadContract;

	/** @var string */
	private $metaData;

	/** @var string */
	private $metaDataContract;

	/** @var string */
	private $file;

	/** @var int */
	private $lastRevision;

	/** @var \DateTimeImmutable */
	private $occurredOn;

	/** @var \DateTimeImmutable */
	private $committedOn;

	/**
	 * @return int|null
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $streamId
	 */
	public function setStreamId( $streamId )
	{
		$this->streamId = (string)$streamId;
	}

	/**
	 * @return string
	 */
	public function getStreamId()
	{
		return $this->streamId;
	}

	/**
	 * @param string $streamIdContract
	 */
	public function setStreamIdContract( $streamIdContract )
	{
		$this->streamIdContract = (string)$streamIdContract;
	}

	/**
	 * @return string
	 */
	public function getStreamIdContract()
	{
		return $this->streamIdContract;
	}

	/**
	 * @param string $commitId
	 */
	public function setCommitId( $commitId )
	{
		$this->commitId = (string)$commitId;
	}

	/**
	 * @return string
	 */
	public function getCommitId()
	{
		return $this->commitId;
	}

	/**
	 * @param string $payload
	 */
	public function setPayload( $payload )
	{
		$this->payload = $payload;
	}

	/**
	 * @return string
	 */
	public function getPayload()
	{
		return $this->payload;
	}

	/**
	 * @param string $payloadContract
	 */
	public function setPayloadContract( $payloadContract )
	{
		$this->payloadContract = $payloadContract;
	}

	/**
	 * @return string
	 */
	public function getPayloadContract()
	{
		return $this->payloadContract;
	}

	/**
	 * @param string $metaData
	 */
	public function setMetaData( $metaData )
	{
		$this->metaData = $metaData;
	}

	/**
	 * @return string
	 */
	public function getMetaData()
	{
		return $this->metaData;
	}

	/**
	 * @param string $metaDataContract
	 */
	public function setMetaDataContract( $metaDataContract )
	{
		$this->metaDataContract = $metaDataContract;
	}

	/**
	 * @return string
	 */
	public function getMetaDataContract()
	{
		return $this->metaDataContract;
	}

	/**
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * @param string $file
	 */
	public function setFile( $file )
	{
		$this->file = $file;
	}

	/**
	 * @return int
	 */
	public function getLastRevision()
	{
		return $this->lastRevision;
	}

	/**
	 * @param int $lastRevision
	 */
	public function setLastRevision( $lastRevision )
	{
		$this->lastRevision = $lastRevision;
	}

	/**
	 * @param \DateTimeImmutable $occurredOn
	 */
	public function setOccurredOn( \DateTimeImmutable $occurredOn )
	{
		$this->occurredOn = $occurredOn;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getOccurredOn()
	{
		return $this->occurredOn;
	}

	/**
	 * @param \DateTimeImmutable $committedOn
	 */
	public function setCommittedOn( \DateTimeImmutable $committedOn )
	{
		$this->committedOn = $committedOn;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getCommittedOn()
	{
		return $this->committedOn;
	}

	/**
	 * @param ServesCommitData $commitData
	 *
	 * @return static
	 */
	public static function fromCommitData( ServesCommitData $commitData )
	{
		$commitEnvelope                   = new self();
		$commitEnvelope->id               = $commitData->getId();
		$commitEnvelope->commitId         = $commitData->getCommitId();
		$commitEnvelope->streamId         = $commitData->getStreamId();
		$commitEnvelope->streamIdContract = $commitData->getStreamIdContract();
		$commitEnvelope->payload          = $commitData->getPayload();
		$commitEnvelope->payloadContract  = $commitData->getPayloadContract();
		$commitEnvelope->metaData         = $commitData->getMetaData();
		$commitEnvelope->metaDataContract = $commitData->getMetaDataContract();
		$commitEnvelope->file             = $commitData->getFile();
		$commitEnvelope->lastRevision = $commitData->getLastRevision();
		$commitEnvelope->occurredOn       = $commitData->getOccurredOn();
		$commitEnvelope->committedOn      = $commitData->getCommittedOn();

		return $commitEnvelope;
	}
}
