<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface WrapsDomainEvent
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface WrapsDomainEvent
{
	/**
	 * @return IdentifiesObject
	 */
	public function getStreamId();

	/**
	 * @return \DateTimeImmutable
	 */
	public function getOccurredOn();

	/**
	 * @return float
	 */
	public function getOccurredOnMicrotime();

	/**
	 * @return CarriesEventData
	 */
	public function getPayload();

	/**
	 * @return \stdClass|array
	 */
	public function getMetaData();

	/**
	 * @return string
	 */
	public function getFile();
}