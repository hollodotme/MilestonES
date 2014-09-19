<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Interfaces;

use hollodotme\MilestonES\Contract;

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
	 * @return Contract
	 */
	public function getContract();

	/**
	 * @param \DateTime $occured_on
	 */
	public function setOccuredOn( \DateTime $occured_on );

	/**
	 * @return \DateTime
	 */
	public function getOccuredOn();

	/**
	 * @return mixed
	 */
	public function getPayload();

	/**
	 * @param mixed $payload
	 */
	public function reconstituteFromPayload( $payload );

	/**
	 * @return mixed
	 */
	public function getMetaData();

	/**
	 * @param mixed $meta_data
	 */
	public function reconstituteFromMetaData( $meta_data );

	/**
	 * @return int
	 */
	public function getVersion();

	/**
	 * @param int $version
	 */
	public function setVersion( $version );
}
