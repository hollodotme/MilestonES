<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface EnvelopesEvent
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface EnvelopesEvent
{
	public function setStreamId( Identifies $stream_id );

	/**
	 * @return Identifies
	 */
	public function getStreamId();

	/**
	 * @param Identifies $stream_type_id
	 */
	public function setStreamTypeId( Identifies $stream_type_id );

	/**
	 * @return Identifies
	 */
	public function getStreamTypeId();

	/**
	 * @param int $version
	 */
	public function setVersion( $version );

	/**
	 * @return int
	 */
	public function getVersion();

	/**
	 * @param string $event_name
	 */
	public function setEventName( $event_name );

	/**
	 * @return string
	 */
	public function getEventName();

	/**
	 * @param $payload
	 */
	public function setPayload( $payload );

	public function getPayload();

	/**
	 * @param Identifies $payload_contract
	 */
	public function setPayloadContract( Identifies $payload_contract );

	/**
	 * @return Identifies
	 */
	public function getPayloadContract();

	public function setMetaData( $meta_data );

	public function getMetaData();

	/**
	 * @param Identifies $meta_data_contract
	 */
	public function setMetaDataContract( Identifies $meta_data_contract );

	/**
	 * @return Identifies
	 */
	public function getMetaDataContract();

	/**
	 * @param Identifies $commit_id
	 */
	public function setCommitId( Identifies $commit_id );

	/**
	 * @return Identifies
	 */
	public function getCommitId();

	/**
	 * @param \DateTime $occured_on
	 */
	public function setOccuredOn( \DateTime $occured_on );

	/**
	 * @return \DateTime
	 */
	public function getOccuredOn();

	/**
	 * @param \DateTime $committed_on
	 */
	public function setCommittedOn( \DateTime $committed_on );

	/**
	 * @return \DateTime
	 */
	public function getCommittedOn();
}
