<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Events;

use hollodotme\MilestonES\Contract;
use hollodotme\MilestonES\Interfaces;
use hollodotme\MilestonES\Interfaces\Identifies;

/**
 * Class BaseRepresentsEvent
 *
 * @package hollodotme\MilestonES\Events
 */
abstract class BaseEvent implements Interfaces\RepresentsEvent
{

	/** @var Identifies */
	private $id;

	/** @var int */
	private $version;

	/** @var \DateTime */
	private $occured_on;

	/** @var string */
	private $creator;

	/**
	 * @param Interfaces\Identifies $id
	 */
	final public function __construct( Interfaces\Identifies $id )
	{
		$this->id         = $id;
		$this->version    = 0;
		$this->occured_on = new \DateTime( 'now' );
	}

	/**
	 * @return Contract
	 */
	final public function getContract()
	{
		return new Contract( get_class( $this ) );
	}

	/**
	 * @return Identifies
	 */
	final public function getStreamId()
	{
		return $this->id;
	}

	/**
	 * @return \DateTime
	 */
	final public function getOccuredOn()
	{
		return $this->occured_on;
	}

	/**
	 * @param \DateTime $occured_on
	 */
	final public function setOccuredOn( \DateTime $occured_on )
	{
		$this->occured_on = $occured_on;
	}

	/**
	 * @return int
	 */
	final public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @param int $version
	 */
	final public function setVersion( $version )
	{
		$this->version = $version;
	}

	/**
	 * @return string
	 */
	public function getCreator()
	{
		return $this->creator;
	}

	/**
	 * @param string $creator
	 */
	public function setCreator( $creator )
	{
		$this->creator = $creator;
	}

	/**
	 * @return \stdClass
	 */
	public function getMetaData()
	{
		$meta_data          = new \stdClass();
		$meta_data->creator = $this->creator;

		return $meta_data;
	}

	/**
	 * @param \stdClass $meta_data
	 */
	public function reconstituteFromMetaData( $meta_data )
	{
		$this->creator = $meta_data->creator;
	}
}
