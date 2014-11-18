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

	final public function clearCommittedChanges( CollectsDomainEventEnvelopes $commited_events )
	{
		$this->tracked_changes->removeEvents( $commited_events );
	}

	/**
	 * @param EventStream $event_stream
	 */
	final protected function applyEventStream( EventStream $event_stream )
	{
		foreach ( $event_stream as $event )
		{
			$this->applyChange( $event->getPayload() );
		}
	}

	/**
	 * @param RepresentsEvent $event
	 * @param \stdClass|array $meta_data
	 */
	protected function trackThat( RepresentsEvent $event, $meta_data )
	{
		$this->tracked_changes[] = $this->getDomainEventEnvelope( $event, $meta_data );

		$this->applyChange( $event );
	}

	/**
	 * @param RepresentsEvent $event
	 * @param \stdClass|array $meta_data
	 *
	 * @return DomainEventEnvelope
	 */
	protected function getDomainEventEnvelope( RepresentsEvent $event, $meta_data )
	{
		return new DomainEventEnvelope( $event, $meta_data );
	}

	/**
	 * @param RepresentsEvent $event
	 */
	protected function applyChange( RepresentsEvent $event )
	{
		$method_name = 'when' . ( new Contract( $event ) )->getClassBasename();
		if ( is_callable( [ $this, $method_name ] ) )
		{
			$this->{$method_name}( $event );
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
