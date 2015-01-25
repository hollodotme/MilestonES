<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\RepresentsEvent;

/**
 * Class BaseRepresentsEvent
 *
 * @package hollodotme\MilestonES\Events
 */
class DomainEventEnvelope implements Interfaces\WrapsDomainEvent
{

	/** @var RepresentsEvent */
	private $event;

	/** @var \stdClass|array */
	private $meta_data;

	/** @var string */
	private $file;

	/** @var \DateTimeImmutable */
	private $occurred_on;

	/** @var float */
	private $occurred_on_microtime;

	/**
	 * @param RepresentsEvent $event
	 * @param \stdClass|array $meta_data
	 * @param string          $file
	 */
	public function __construct( RepresentsEvent $event, $meta_data, $file = null )
	{
		$this->event       = $event;
		$this->meta_data   = $meta_data;
		$this->file = $file;
		$this->occurred_on = new \DateTimeImmutable( 'now' );

		usleep( 1 );
		$this->occurred_on_microtime = microtime( true );
	}

	/**
	 * @return Identifies
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
		return $this->occurred_on;
	}

	/**
	 * @return float
	 */
	public function getOccurredOnMicrotime()
	{
		return $this->occurred_on_microtime;
	}

	/**
	 * @return RepresentsEvent
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
		return $this->meta_data;
	}

	/**
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * @param RepresentsEvent    $event
	 * @param \stdClass|array    $meta_data
	 * @param string             $file
	 * @param \DateTimeImmutable $occurred_on
	 *
	 * @return DomainEventEnvelope
	 */
	public static function fromRecord( RepresentsEvent $event, $meta_data, $file, \DateTimeImmutable $occurred_on )
	{
		$envelope = new self( $event, $meta_data, $file );
		$envelope->occurred_on = $occurred_on;

		return $envelope;
	}
}
