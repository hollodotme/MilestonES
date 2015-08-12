<?php
/**
 *
 * @author hollodotme
 */
namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface ConsumesCommitData
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface ConsumesCommitData
{
	/**
	 * @param string $streamId
	 */
	public function setStreamId( $streamId );

	/**
	 * @param string $streamIdContract
	 */
	public function setStreamIdContract( $streamIdContract );

	/**
	 * @param string $payload
	 */
	public function setPayload( $payload );

	/**
	 * @param string $payloadContract
	 */
	public function setPayloadContract( $payloadContract );

	/**
	 * @param string $metaData
	 */
	public function setMetaData( $metaData );

	/**
	 * @param string $metaDataContract
	 */
	public function setMetaDataContract( $metaDataContract );

	/**
	 * @param string $commitId
	 */
	public function setCommitId( $commitId );

	/**
	 * @param \DateTimeImmutable $occurredOn
	 */
	public function setOccurredOn( \DateTimeImmutable $occurredOn );

	/**
	 * @param \DateTimeImmutable $committedOn
	 */
	public function setCommittedOn( \DateTimeImmutable $committedOn );

	/**
	 * @param string $file
	 */
	public function setFile( $file );
}