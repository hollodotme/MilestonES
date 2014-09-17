<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

/**
 * Interface Event
 *
 * @package hollodotme\MilestonES\Interfaces
 */
interface Event
{
	/**
	 * @return Identifies
	 */
	public function getStreamId();

	public function getName();

	/**
	 * @return \DateTime
	 */
	public function getOccuredOn();

	public function getPayload();

	public function getMetaData();

	public function getVersion();

	/**
	 * @param int $version
	 */
	public function setVersion( $version );
}
