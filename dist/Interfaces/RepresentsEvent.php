<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\Contract;

/**
 * Interface RepresentsEvent
 * @package hollodotme\MilestonES\Interfaces
 */
interface RepresentsEvent
{
	/**
	 * @return Identifies
	 */
	public function getStreamId();

	/**
	 * @return Contract
	 */
	public function getContract();

	/**
	 * @param \DateTimeImmutable $occured_on
	 */
	public function setOccuredOn( \DateTimeImmutable $occured_on );

	/**
	 * @return \DateTime
	 */
	public function getOccuredOn();

	/**
	 * @return \stdClass
	 */
	public function getPayloadDTO();

	/**
	 * @param \stdClass $payload_dto
	 */
	public function setPayloadDTO( \stdClass $payload_dto );

	/**
	 * @return \stdClass
	 */
	public function getMetaDTO();

	/**
	 * @param \stdClass $meta_dto
	 */
	public function setMetaDTO( \stdClass $meta_dto );

	/**
	 * @return int
	 */
	public function getVersion();

	/**
	 * @param int $version
	 */
	public function setVersion( $version );
}
