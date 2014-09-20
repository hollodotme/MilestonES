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

	/** @var \stdClass */
	private $payload_dto;

	/** @var \stdClass */
	private $meta_dto;

	/**
	 * @param Interfaces\Identifies $id
	 */
	final public function __construct( Interfaces\Identifies $id )
	{
		$this->id         = $id;
		$this->version    = 0;
		$this->occured_on = new \DateTime( 'now' );

		$this->payload_dto = new \stdClass();
		$this->meta_dto    = new \stdClass();
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
		return $this->meta_dto->creator;
	}

	/**
	 * @param string $creator
	 */
	public function setCreator( $creator )
	{
		$this->meta_dto->creator = $creator;
	}

	/**
	 * @return \stdClass
	 */
	final public function getPayloadDTO()
	{
		return $this->payload_dto;
	}

	/**
	 * @param \stdClass $payload_dto
	 */
	final public function setPayloadDTO( \stdClass $payload_dto )
	{
		$this->payload_dto = $payload_dto;
	}

	/**
	 * @return \stdClass
	 */
	final public function getMetaDTO()
	{
		return $this->meta_dto;
	}

	/**
	 * @param \stdClass $meta_dto
	 */
	final public function setMetaDTO( \stdClass $meta_dto )
	{
		$this->meta_dto = $meta_dto;
	}
}
