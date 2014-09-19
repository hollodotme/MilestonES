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
	private $stream_type_id;

	/** @var int */
	private $version;

	/** @var string */
	private $event_contract;

	/** @var string */
	private $payload;

	/** @var string */
	private $payload_contract;

	/** @var string */
	private $meta_data;

	/** @var string */
	private $meta_data_contract;

	/** @var \DateTime */
	private $occured_on;

	/** @var \DateTime */
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
		$this->stream_type_id = (string)$stream_id_contract;
	}

	/**
	 * @return string
	 */
	public function getStreamIdContract()
	{
		return $this->stream_type_id;
	}

	/**
	 * @param int $version
	 */
	public function setVersion( $version )
	{
		$this->version = $version;
	}

	/**
	 * @return int
	 */
	public function getVersion()
	{
		return $this->version;
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
	 * @return string
	 */
	public function getEventContract()
	{
		return $this->event_contract;
	}

	/**
	 * @param string $event_contract
	 */
	public function setEventContract( $event_contract )
	{
		$this->event_contract = (string)$event_contract;
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
	 * @param \DateTime $occured_on
	 */
	public function setOccuredOn( \DateTime $occured_on )
	{
		$this->occured_on = $occured_on;
	}

	/**
	 * @return \DateTime
	 */
	public function getOccuredOn()
	{
		return $this->occured_on;
	}

	/**
	 * @param \DateTime $committed_on
	 */
	public function setCommittedOn( \DateTime $committed_on )
	{
		$this->committed_on = $committed_on;
	}

	/**
	 * @return \DateTime
	 */
	public function getCommittedOn()
	{
		return $this->committed_on;
	}
}
