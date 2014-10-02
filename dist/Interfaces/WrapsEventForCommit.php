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
	/**
	 * @param string $stream_id
	 */
	public function setStreamId( $stream_id );

	/**
	 * @return string
	 */
	public function getStreamId();

	/**
	 * @param string $stream_id_contract
	 */
	public function setStreamIdContract( $stream_id_contract );

	/**
	 * @return string
	 */
	public function getStreamIdContract();

	/**
	 * @param int $version
	 */
	public function setVersion( $version );

	/**
	 * @return int
	 */
	public function getVersion();

	/**
	 * @param string $event_contract
	 */
	public function setEventContract( $event_contract );

	/**
	 * @return string
	 */
	public function getEventContract();

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
	 * @param string $commit_id
	 */
	public function setCommitId( $commit_id );

	/**
	 * @return string
	 */
	public function getCommitId();

	/**
	 * @param \DateTimeImmutable $occured_on
	 */
	public function setOccuredOn( \DateTimeImmutable $occured_on );

	/**
	 * @return \DateTimeImmutable
	 */
	public function getOccuredOn();

	/**
	 * @param \DateTimeImmutable $committed_on
	 */
	public function setCommittedOn( \DateTimeImmutable $committed_on );

	/**
	 * @return \DateTimeImmutable
	 */
	public function getCommittedOn();
}
