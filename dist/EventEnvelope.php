<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\CarriesEventData;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;
use hollodotme\MilestonES\Interfaces\ServesEventStreamData;

/**
 * Class BaseRepresentsEvent
 *
 * @package hollodotme\MilestonES\Events
 */
class EventEnvelope implements ServesEventStreamData
{

	/** @var CarriesEventData */
	private $payload;

	/** @var \stdClass|array */
	private $metaData;

	/** @var string */
	private $file;

	/** @var int */
	private $lastRevision;

	/** @var \DateTimeImmutable */
	private $occurredOn;

	/** @var float */
	private $occurredOnMicrotime;

	/**
	 * @param int              $lastRevision
	 * @param CarriesEventData $payload
	 * @param \stdClass|array  $metaData
	 * @param string           $file
	 */
	public function __construct( $lastRevision, CarriesEventData $payload, $metaData, $file = null )
	{
		$this->lastRevision = $lastRevision;
		$this->payload      = $payload;
		$this->metaData     = $metaData;
		$this->file         = $file;
		$this->occurredOn   = new \DateTimeImmutable( 'now' );

		usleep( 1 );
		$this->occurredOnMicrotime = microtime( true );
	}

	/**
	 * @return IdentifiesObject
	 */
	public function getStreamId()
	{
		return $this->payload->getStreamId();
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
		return $this->payload;
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
	 * @return int
	 */
	public function getLastRevision()
	{
		return $this->lastRevision;
	}

	/**
	 * @param int $lastRevision
	 * @param CarriesEventData   $event
	 * @param \stdClass|array    $metaData
	 * @param string             $file
	 * @param \DateTimeImmutable $occurredOn
	 *
	 * @return EventEnvelope
	 */
	public static function fromRecord(
		$lastRevision, CarriesEventData $event, $metaData, $file, \DateTimeImmutable $occurredOn
	)
	{
		$envelope = new self( $lastRevision, $event, $metaData, $file );
		$envelope->occurredOn = $occurredOn;

		return $envelope;
	}
}
