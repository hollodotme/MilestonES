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
	 * @return Identifies
	 */
	public function getStreamId();

	/**
	 * @return \DateTimeImmutable
	 */
	public function getOccurredOn();

	/**
	 * @return RepresentsEvent
	 */
	public function getPayload();

	/**
	 * @return \stdClass|array
	 */
	public function getMetaData();
}