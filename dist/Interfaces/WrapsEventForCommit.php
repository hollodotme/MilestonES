<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface WrapsEventForCommit
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface WrapsEventForCommit
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
	 * @param string $payload
	 */
	public function setPayload( $payload );

	/**
	 * @return string
	 */
	public function getPayload();

	/**
	 * @param string $payload_contract
	 */
	public function setPayloadContract( $payload_contract );

	/**
	 * @return string
	 */
	public function getPayloadContract();

	/**
	 * @param string $meta_data
	 */
	public function setMetaData( $meta_data );

	/**
	 * @return string
	 */
	public function getMetaData();

	/**
	 * @param string $meta_data_contract
	 */
	public function setMetaDataContract( $meta_data_contract );

	/**
	 * @return string
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
