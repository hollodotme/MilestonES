<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\WrapsEventForCommit;
use hollodotme\MilestonES\Interfaces\Identifies;

/**
 * Class CommitEventEnvelope
 *
 * @package hollodotme\MilestonES
 */
final class CommitEventEnvelope implements WrapsEventForCommit
{

	/** @var int */
	private $id;

	/** @var Identifies */
	private $commit_id;

	/** @var Identifies */
	private $stream_id;

	/** @var Identifies */
	private $stream_type_id;

	/** @var int */
	private $version;

	/** @var string */
	private $event_name;

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
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param Identifies $stream_id
	 */
	public function setStreamId( Identifies $stream_id )
	{
		$this->stream_id = $stream_id;
	}

	/**
	 * @return Identifies
	 */
	public function getStreamId()
	{
		return $this->stream_id;
	}

	/**
	 * @param Identifies $stream_type_id
	 */
	public function setStreamTypeId( Identifies $stream_type_id )
	{
		$this->stream_type_id = $stream_type_id;
	}

	/**
	 * @return Identifies
	 */
	public function getStreamTypeId()
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
	 * @param Identifies $commit_id
	 */
	public function setCommitId( Identifies $commit_id )
	{
		$this->commit_id = $commit_id;
	}

	/**
	 * @return Identifies
	 */
	public function getCommitId()
	{
		return $this->commit_id;
	}

	/**
	 * @return string
	 */
	public function getEventName()
	{
		return $this->event_name;
	}

	/**
	 * @param string $event_name
	 */
	public function setEventName( $event_name )
	{
		$this->event_name = $event_name;
	}

	/**
	 * @param $payload
	 */
	public function setPayload( $payload )
	{
		$this->payload = $payload;
	}

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

	public function setMetaData( $meta_data )
	{
		$this->meta_data = $meta_data;
	}

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
