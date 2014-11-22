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
	private $commit_id;

	/** @var string */
	private $stream_id;

	/** @var string */
	private $stream_id_contract;

	/** @var string */
	private $payload;

	/** @var string */
	private $payload_contract;

	/** @var string */
	private $meta_data;

	/** @var string */
	private $meta_data_contract;

	/** @var string */
	private $file;

	/** @var \DateTimeImmutable */
	private $occurred_on;

	/** @var \DateTimeImmutable */
	private $committed_on;

	/**
	 * @return int|null
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $stream_id
	 */
	public function setStreamId( $stream_id )
	{
		$this->stream_id = (string)$stream_id;
	}

	/**
	 * @return string
	 */
	public function getStreamId()
	{
		return $this->stream_id;
	}

	/**
	 * @param string $stream_id_contract
	 */
	public function setStreamIdContract( $stream_id_contract )
	{
		$this->stream_id_contract = (string)$stream_id_contract;
	}

	/**
	 * @return string
	 */
	public function getStreamIdContract()
	{
		return $this->stream_id_contract;
	}

	/**
	 * @param string $commit_id
	 */
	public function setCommitId( $commit_id )
	{
		$this->commit_id = (string)$commit_id;
	}

	/**
	 * @return string
	 */
	public function getCommitId()
	{
		return $this->commit_id;
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
	 * @param string $payload_contract
	 */
	public function setPayloadContract( $payload_contract )
	{
		$this->payload_contract = $payload_contract;
	}

	/**
	 * @return string
	 */
	public function getPayloadContract()
	{
		return $this->payload_contract;
	}

	/**
	 * @param string $meta_data
	 */
	public function setMetaData( $meta_data )
	{
		$this->meta_data = $meta_data;
	}

	/**
	 * @return string
	 */
	public function getMetaData()
	{
		return $this->meta_data;
	}

	/**
	 * @param string $meta_data_contract
	 */
	public function setMetaDataContract( $meta_data_contract )
	{
		$this->meta_data_contract = $meta_data_contract;
	}

	/**
	 * @return string
	 */
	public function getMetaDataContract()
	{
		return $this->meta_data_contract;
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
	 * @param \DateTimeImmutable $occured_on
	 */
	public function setOccurredOn( \DateTimeImmutable $occured_on )
	{
		$this->occurred_on = $occured_on;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getOccurredOn()
	{
		return $this->occurred_on;
	}

	/**
	 * @param \DateTimeImmutable $committed_on
	 */
	public function setCommittedOn( \DateTimeImmutable $committed_on )
	{
		$this->committed_on = $committed_on;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getCommittedOn()
	{
		return $this->committed_on;
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
