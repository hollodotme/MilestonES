<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\AggregatesModels;
use hollodotme\MilestonES\Interfaces\CollectsDomainEventEnvelopes;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\RepresentsEvent;
use hollodotme\MilestonES\Interfaces\WrapsDomainEvent;

/**
 * Class AggregateRoot
 *
 * @package hollodotme\MilestonES
 */
abstract class AggregateRoot implements AggregatesModels
{

	/** @var DomainEventEnvelopeCollection */
	private $tracked_changes;

	final protected function __construct()
	{
		$this->tracked_changes = new DomainEventEnvelopeCollection();
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
		return $this->tracked_changes;
	}

	/**
	 * @return bool
	 */
	final public function hasChanges()
	{
		return !$this->tracked_changes->isEmpty();
	}

	/**
	 * @param CollectsDomainEventEnvelopes $commited_events
	 */
	final public function clearCommittedChanges( CollectsDomainEventEnvelopes $commited_events )
	{
		$this->tracked_changes->removeEvents( $commited_events );
	}

	/**
	 * @param EventStream $event_stream
	 */
	final protected function applyEventStream( EventStream $event_stream )
	{
		foreach ( $event_stream as $event_envelope )
		{
			$this->applyChange( $event_envelope );
		}
	}

	/**
	 * @param RepresentsEvent $event
	 * @param \stdClass|array $meta_data
	 * @param string|null     $file
	 */
	protected function trackThat( RepresentsEvent $event, $meta_data, $file = null )
	{
		$domain_event_envelope   = $this->getDomainEventEnvelope( $event, $meta_data, $file );
		$this->tracked_changes[] = $domain_event_envelope;

		$this->applyChange( $domain_event_envelope );
	}

	/**
	 * @param RepresentsEvent $event
	 * @param \stdClass|array $meta_data
	 * @param string          $file
	 *
	 * @return DomainEventEnvelope
	 */
	protected function getDomainEventEnvelope( RepresentsEvent $event, $meta_data, $file )
	{
		return new DomainEventEnvelope( $event, $meta_data, $file );
	}

	/**
	 * @param WrapsDomainEvent $event_envelope
	 */
	protected function applyChange( WrapsDomainEvent $event_envelope )
	{
		$event       = $event_envelope->getPayload();
		$occurred_on = $event_envelope->getOccurredOn();
		$file        = $event_envelope->getFile();

		$method_name = 'when' . ( new Contract( $event ) )->getClassBasename();
		if ( is_callable( [ $this, $method_name ] ) )
		{
			$this->{$method_name}( $event, $occurred_on, $file );
		}
	}

	/**
	 * @param EventStream $event_streem
	 *
	 * @return static
	 */
	public static function reconstituteFromHistory( EventStream $event_streem )
	{
		$instance = new static();

		$instance->applyEventStream( $event_streem );

		return $instance;
	}
}
