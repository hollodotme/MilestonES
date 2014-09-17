<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface RepresentsEvent
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface RepresentsEvent
{
	/**
	 * @return Identifies
	 */
	public function getStreamId();

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @param \DateTime $occured_on
	 */
	public function setOccuredOn( \DateTime $occured_on );

	/**
	 * @return \DateTime
	 */
	public function getOccuredOn();

	public function getPayload();

	/**
	 * @param mixed $payload
	 */
	public function reconstituteFromPayload( $payload );

	public function getMetaData();

	public function getVersion();

	/**
	 * @param int $version
	 */
	public function setVersion( $version );
}
