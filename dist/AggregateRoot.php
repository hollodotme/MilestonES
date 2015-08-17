<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\AggregatesObjects;
use hollodotme\MilestonES\Interfaces\CarriesEventData;
use hollodotme\MilestonES\Interfaces\CollectsEventEnvelopes;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;
use hollodotme\MilestonES\Interfaces\ServesEventStreamData;

/**
 * Class AggregateRoot
 *
 * @package hollodotme\MilestonES
 */
abstract class AggregateRoot implements AggregatesObjects
{

	/** @var EventEnvelopeCollection */
	private $trackedChanges;

	/** @var int */
	private $revision;

	final protected function __construct()
	{
		$this->trackedChanges = new EventEnvelopeCollection();
		$this->revision = 0;
	}

	/**
	 * @return IdentifiesObject
	 */
	abstract public function getIdentifier();

	/**
	 * @return EventEnvelopeCollection
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
	 * @param CollectsEventEnvelopes $commitedEvents
	 */
	final public function clearCommittedChanges( CollectsEventEnvelopes $commitedEvents )
	{
		$this->trackedChanges->removeEvents( $commitedEvents );
	}

	/**
	 * @return int
	 */
	public function getRevision()
	{
		return $this->revision;
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
		$eventEnvelope          = $this->getEventEnvelope( $event, $metaData, $file );
		$this->trackedChanges[] = $eventEnvelope;

		$this->applyChange( $eventEnvelope );
	}

	/**
	 * @param CarriesEventData $event
	 * @param \stdClass|array  $metaData
	 * @param string           $file
	 *
	 * @return EventEnvelope
	 */
	protected function getEventEnvelope( CarriesEventData $event, $metaData, $file )
	{
		return new EventEnvelope( $this->revision, $event, $metaData, $file );
	}

	/**
	 * @param ServesEventStreamData $eventEnvelope
	 */
	private function applyChange( ServesEventStreamData $eventEnvelope )
	{
		$event      = $eventEnvelope->getPayload();
		$occurredOn = $eventEnvelope->getOccurredOn();
		$file       = $eventEnvelope->getFile();
		$metaData   = $eventEnvelope->getMetaData();

		$methodName = 'when' . ( new Contract( $event ) )->getClassBasename();
		if ( is_callable( [ $this, $methodName ] ) )
		{
			$this->{$methodName}( $event, $occurredOn, $file, $metaData );

			$this->revision++;
		}
		else
		{
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
