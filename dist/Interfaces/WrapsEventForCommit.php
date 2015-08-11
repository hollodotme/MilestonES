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
	 * @param string $streamId
	 */
	public function setStreamId( $streamId );

	/**
	 * @return string
	 */
	public function getStreamId();

	/**
	 * @param string $streamIdContract
	 */
	public function setStreamIdContract( $streamIdContract );

	/**
	 * @return string
	 */
	public function getStreamIdContract();

	/**
	 * @param string $payload
	 */
	public function setPayload( $payload );

	/**
	 * @return string
	 */
	public function getPayload();

	/**
	 * @param string $payloadContract
	 */
	public function setPayloadContract( $payloadContract );

	/**
	 * @return string
	 */
	public function getPayloadContract();

	/**
	 * @param string $metaData
	 */
	public function setMetaData( $metaData );

	/**
	 * @return string
	 */
	public function getMetaData();

	/**
	 * @param string $metaDataContract
	 */
	public function setMetaDataContract( $metaDataContract );

	/**
	 * @return string
	 */
	public function getMetaDataContract();

	/**
	 * @param string $commitId
	 */
	public function setCommitId( $commitId );

	/**
	 * @return string
	 */
	public function getCommitId();

	/**
	 * @param \DateTimeImmutable $occurredOn
	 */
	public function setOccurredOn( \DateTimeImmutable $occurredOn );

	/**
	 * @return \DateTimeImmutable
	 */
	public function getOccurredOn();

	/**
	 * @param \DateTimeImmutable $committedOn
	 */
	public function setCommittedOn( \DateTimeImmutable $committedOn );

	/**
	 * @return \DateTimeImmutable
	 */
	public function getCommittedOn();

	/**
	 * @param string $file
	 */
	public function setFile( $file );

	/**
	 * @return string
	 */
	public function getFile();
}
