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

	/** @var \DateTimeImmutable */
	private $occurred_on;

	/**
	 * @param RepresentsEvent $event
	 * @param \stdClass|array $meta_data
	 */
	public function __construct( RepresentsEvent $event, $meta_data )
	{
		$this->event       = $event;
		$this->meta_data   = $meta_data;
		$this->occurred_on = new \DateTimeImmutable( 'now' );
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
	 * @param RepresentsEvent    $event
	 * @param \stdClass|array    $meta_data
	 * @param \DateTimeImmutable $occurred_on
	 *
	 * @return DomainEventEnvelope
	 */
	public static function fromRecord( RepresentsEvent $event, $meta_data, \DateTimeImmutable $occurred_on )
	{
		$envelope              = new self( $event, $meta_data );
		$envelope->occurred_on = $occurred_on;

		return $envelope;
	}
}
