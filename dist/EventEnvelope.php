<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces;
use hollodotme\MilestonES\Interfaces\CarriesEventData;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;

/**
 * Class BaseRepresentsEvent
 *
 * @package hollodotme\MilestonES\Events
 */
class EventEnvelope implements Interfaces\WrapsDomainEvent
{

	/** @var CarriesEventData */
	private $event;

	/** @var \stdClass|array */
	private $metaData;

	/** @var string */
	private $file;

	/** @var \DateTimeImmutable */
	private $occurredOn;

	/** @var float */
	private $occurredOnMicrotime;

	/**
	 * @param CarriesEventData $event
	 * @param \stdClass|array  $metaData
	 * @param string           $file
	 */
	public function __construct( CarriesEventData $event, $metaData, $file = null )
	{
		$this->event      = $event;
		$this->metaData   = $metaData;
		$this->file       = $file;
		$this->occurredOn = new \DateTimeImmutable( 'now' );

		usleep( 1 );
		$this->occurredOnMicrotime = microtime( true );
	}

	/**
	 * @return IdentifiesObject
	 */
	public function getStreamId()
	{
		return $this->event->getStreamId();
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getOccurredOn()
	{
		return $this->occurredOn;
	}

	/**
	 * @return float
	 */
	public function getOccurredOnMicrotime()
	{
		return $this->occurredOnMicrotime;
	}

	/**
	 * @return CarriesEventData
	 */
	public function getPayload()
	{
		return $this->event;
	}

	/**
	 * @return \stdClass|array
	 */
	public function getMetaData()
	{
		return $this->metaData;
	}

	/**
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * @param CarriesEventData   $event
	 * @param \stdClass|array    $metaData
	 * @param string             $file
	 * @param \DateTimeImmutable $occurredOn
	 *
	 * @return EventEnvelope
	 */
	public static function fromRecord( CarriesEventData $event, $metaData, $file, \DateTimeImmutable $occurredOn )
	{
		$envelope             = new self( $event, $metaData, $file );
		$envelope->occurredOn = $occurredOn;

		return $envelope;
	}
}
