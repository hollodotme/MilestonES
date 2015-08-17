<?php
/**
 *
 * @author hollodotme
 */
namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface ServesCommitData
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface ServesCommitData
{
	/**
	 * @return string
	 */
	public function getId();

	/**
	 * @return string
	 */
	public function getStreamId();

	/**
	 * @return string
	 */
	public function getStreamIdContract();

	/**
	 * @return string
	 */
	public function getPayload();

	/**
	 * @return string
	 */
	public function getPayloadContract();

	/**
	 * @return string
	 */
	public function getMetaData();

	/**
	 * @return string
	 */
	public function getMetaDataContract();

	/**
	 * @return string
	 */
	public function getCommitId();

	/**
	 * @return \DateTimeImmutable
	 */
	public function getOccurredOn();

	/**
	 * @return \DateTimeImmutable
	 */
	public function getCommittedOn();

	/**
	 * @return string
	 */
	public function getFile();

	/**
	 * @return int
	 */
	public function getLastRevision();
}