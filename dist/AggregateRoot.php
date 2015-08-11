<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\AggregatesObjects;
use hollodotme\MilestonES\Interfaces\CarriesEventData;
use hollodotme\MilestonES\Interfaces\CollectsDomainEventEnvelopes;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\WrapsDomainEvent;

/**
 * Class AggregateRoot
 *
 * @package hollodotme\MilestonES
 */
abstract class AggregateRoot implements AggregatesObjects
{

	/** @var DomainEventEnvelopeCollection */
	private $trackedChanges;

	final protected function __construct()
	{
		$this->trackedChanges = new DomainEventEnvelopeCollection();
	}

	/**
	 * @return Identifies
	 */
	abstract public function getIdentifier();

	/**
	 * @return DomainEventEnvelopeCollection
	 */
	final public function getChanges()
	{
		return $this->trackedChanges;
	}

	/**
	 * @return bool
	 */
	final public function hasChanges()
	{
		return !$this->trackedChanges->isEmpty();
	}

	/**
	 * @param CollectsDomainEventEnvelopes $commitedEvents
	 */
	final public function clearCommittedChanges( CollectsDomainEventEnvelopes $commitedEvents )
	{
		$this->trackedChanges->removeEvents( $commitedEvents );
	}

	/**
	 * @param EventStream $eventStream
	 */
	final protected function applyEventStream( EventStream $eventStream )
	{
		foreach ( $eventStream as $eventEnvelope )
		{
			$this->applyChange( $eventEnvelope );
		}
	}

	/**
	 * @param CarriesEventData $event
	 * @param \stdClass|array  $metaData
	 * @param string|null      $file
	 */
	protected function trackThat( CarriesEventData $event, $metaData, $file = null )
	{
		$domainEventEnvelope    = $this->getDomainEventEnvelope( $event, $metaData, $file );
		$this->trackedChanges[] = $domainEventEnvelope;

		$this->applyChange( $domainEventEnvelope );
	}

	/**
	 * @param CarriesEventData $event
	 * @param \stdClass|array  $metaData
	 * @param string           $file
	 *
	 * @return DomainEventEnvelope
	 */
	protected function getDomainEventEnvelope( CarriesEventData $event, $metaData, $file )
	{
		return new DomainEventEnvelope( $event, $metaData, $file );
	}

	/**
	 * @param WrapsDomainEvent $eventEnvelope
	 */
	protected function applyChange( WrapsDomainEvent $eventEnvelope )
	{
		$event      = $eventEnvelope->getPayload();
		$occurredOn = $eventEnvelope->getOccurredOn();
		$file       = $eventEnvelope->getFile();
		$metaData   = $eventEnvelope->getMetaData();

		$methodName = 'when' . ( new Contract( $event ) )->getClassBasename();
		if ( is_callable( [ $this, $methodName ] ) )
		{
			$this->{$methodName}( $event, $occurredOn, $file, $metaData );
		}
	}

	/**
	 * @param EventStream $eventStream
	 *
	 * @return static
	 */
	public static function reconstituteFromHistory( EventStream $eventStream )
	{
		$instance = new static();

		$instance->applyEventStream( $eventStream );

		return $instance;
	}
}
