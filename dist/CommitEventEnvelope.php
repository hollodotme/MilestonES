<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\WrapsEventForCommit;

/**
 * Class CommitEventEnvelope
 *
 * @package hollodotme\MilestonES
 */
final class CommitEventEnvelope implements WrapsEventForCommit
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
	 * @param array $record
	 *
	 * @return CommitEventEnvelope
	 */
	public static function fromRecord( array $record )
	{
		$envelope     = new self();
		$envelope->id = $record['id'];
		$envelope->setCommitId( $record['commit_id'] );
		$envelope->setStreamId( $record['stream_id'] );
		$envelope->setStreamIdContract( $record['stream_id_contract'] );
		$envelope->setPayload( $record['payload'] );
		$envelope->setPayloadContract( $record['payload_contract'] );
		$envelope->setMetaData( $record['meta_data'] );
		$envelope->setMetaDataContract( $record['meta_data_contract'] );
		$envelope->setFile( $record['file'] );
		$envelope->setOccurredOn( \DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $record['occurred_on'] ) );
		$envelope->setCommittedOn( \DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $record['committed_on'] ) );

		return $envelope;
	}
}
