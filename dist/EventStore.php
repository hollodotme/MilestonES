<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\CommittingEventsFailed;
use hollodotme\MilestonES\Exceptions\EventEnvelopesNotFound;
use hollodotme\MilestonES\Exceptions\EventStreamNotFound;
use hollodotme\MilestonES\Exceptions\PersistingEventsFailed;
use hollodotme\MilestonES\Interfaces\CollectsDomainEventEnvelopes;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\IdentifiesCommit;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;
use hollodotme\MilestonES\Interfaces\PersistsEventEnvelopes;
use hollodotme\MilestonES\Interfaces\ServesEventStoreConfiguration;
use hollodotme\MilestonES\Interfaces\StoresEvents;
use hollodotme\MilestonES\Interfaces\WrapsDomainEvent;
use hollodotme\MilestonES\Interfaces\WrapsEventForCommit;

/**
 * Class EventStores
 *
 * @package hollodotme\MilestonES
 */
final class EventStore implements StoresEvents
{

	/** @var ServesEventStoreConfiguration */
	private $config_delegate;

	/** @var ObservesCommitedEvents[] */
	private $commited_event_observers = [ ];

	/** @var  PersistsEventEnvelopes */
	private $persistence;

	/** @var EventEnvelopeMapper */
	private $envelope_mapper;

	/**
	 * @param ServesEventStoreConfiguration $config_delegate
	 */
	public function __construct( ServesEventStoreConfiguration $config_delegate )
	{
		$this->config_delegate = $config_delegate;

		$this->persistence     = $this->getPersistenceStrategy();
		$this->envelope_mapper = $this->getEventEnvelopeMapper();

		$this->attachGlobalObserversForCommitedEvents();
	}

	/**
	 * @param CollectsDomainEventEnvelopes $event_envelopes
	 *
	 * @throws CommittingEventsFailed
	 */
	public function commitEvents( CollectsDomainEventEnvelopes $event_envelopes )
	{
		try
		{
			$this->persistEvents( $event_envelopes );
		}
		catch ( PersistingEventsFailed $e )
		{
			throw new CommittingEventsFailed( '', 0, $e );
		}

		$this->publishCommitedEvents( $event_envelopes );
	}

	/**
	 * @param Identifies $id
	 *
	 * @throws EventStreamNotFound
	 * @return EventStream
	 */
	public function getEventStreamForId( Identifies $id )
	{
		try
		{
			$event_stream_id = $this->getEventStreamId( $id );
			$events          = $this->getStoredEventsWithId( $event_stream_id );

			return new EventStream( $events );
		}
		catch ( EventEnvelopesNotFound $e )
		{
			throw new EventStreamNotFound( $id, 0, $e );
		}
	}

	/**
	 * @param Identifies $id
	 *
	 * @return EventStreamIdentifier
	 */
	protected function getEventStreamId( Identifies $id )
	{
		return new EventStreamIdentifier( $id );
	}

	/**
	 * @param ObservesCommitedEvents $observer
	 */
	public function attachCommittedEventObserver( ObservesCommitedEvents $observer )
	{
		$this->detachCommittedEventObserver( $observer );
		$this->commited_event_observers[] = $observer;
	}

	/**
	 * @param ObservesCommitedEvents $observer
	 */
	public function detachCommittedEventObserver( ObservesCommitedEvents $observer )
	{
		$this->commited_event_observers = array_filter(
			$this->commited_event_observers,
			function ( ObservesCommitedEvents $obs ) use ( $observer )
			{
				return ($observer !== $obs);
			}
		);
	}

	/**
	 * @param CollectsDomainEventEnvelopes $events
	 *
	 * @throws \Exception
	 */
	private function persistEvents( CollectsDomainEventEnvelopes $events )
	{
		$this->persistence->beginTransaction();

		try
		{
			$commit = $this->getCommit();

			$this->persistEventsInTransaction( $commit, $events );

			$this->persistence->commitTransaction();
		}
		catch ( \Exception $e )
		{
			$this->persistence->rollbackTransaction();

			throw new PersistingEventsFailed( '', 0, $e );
		}
	}

	/**
	 * @return Interfaces\PersistsEventEnvelopes
	 */
	private function getPersistenceStrategy()
	{
		return $this->config_delegate->getPersistenceStrategy();
	}

	/**
	 * @return EventEnvelopeMapper
	 */
	private function getEventEnvelopeMapper()
	{
		$serialization_strategy = $this->getSerializationStrategy();

		return new EventEnvelopeMapper( $serialization_strategy );
	}

	/**
	 * @return SerializationStrategy
	 */
	private function getSerializationStrategy()
	{
		return $this->config_delegate->getSerializationStrategy();
	}

	private function attachGlobalObserversForCommitedEvents()
	{
		$observers = $this->getGlobalObserversForCommitedEvents();

		foreach ( $observers as $observer )
		{
			$this->attachCommittedEventObserver( $observer );
		}
	}

	/**
	 * @return Interfaces\ObservesCommitedEvents[]
	 */
	private function getGlobalObserversForCommitedEvents()
	{
		return $this->config_delegate->getGlobalObserversForCommitedEvents();
	}

	/**
	 * @return IdentifiesCommit
	 */
	private function getCommit()
	{
		return new Commit( CommitId::generate(), new \DateTimeImmutable( 'now' ) );
	}

	/**
	 * @param IdentifiesCommit             $commit
	 * @param CollectsDomainEventEnvelopes $event_envelopes
	 */
	private function persistEventsInTransaction(
		IdentifiesCommit $commit,
		CollectsDomainEventEnvelopes $event_envelopes
	)
	{
		foreach ( $event_envelopes as $event )
		{
			$this->commitEvent( $commit, $event );
		}
	}

	/**
	 * @param IdentifiesCommit $commit
	 * @param WrapsDomainEvent $event_envelope
	 */
	private function commitEvent( IdentifiesCommit $commit, WrapsDomainEvent $event_envelope )
	{
		$commit_envelope = $this->getCommitEventEnvelope( $event_envelope, $commit );

		$this->persistence->persistEventEnvelope( $commit_envelope );
	}

	/**
	 * @param WrapsDomainEvent $event_envelope
	 * @param IdentifiesCommit $commit
	 *
	 * @return CommitEventEnvelope
	 */
	private function getCommitEventEnvelope( WrapsDomainEvent $event_envelope, IdentifiesCommit $commit )
	{
		$mapper = $this->getEventEnvelopeMapper();

		return $mapper->putEventInEnvelopeForCommit( $event_envelope, $commit );
	}

	/**
	 * @param CollectsDomainEventEnvelopes $event_envelopes
	 */
	private function publishCommitedEvents( CollectsDomainEventEnvelopes $event_envelopes )
	{
		foreach ( $event_envelopes as $event_envelope )
		{
			$this->publishEvent( $event_envelope );
		}
	}

	/**
	 * @param WrapsDomainEvent $event_envelope
	 */
	private function publishEvent( WrapsDomainEvent $event_envelope )
	{
		$this->notifyAboutCommittedEvent( $event_envelope );
	}

	/**
	 * @param WrapsDomainEvent $event_envelope
	 */
	private function notifyAboutCommittedEvent( WrapsDomainEvent $event_envelope )
	{
		foreach ( $this->commited_event_observers as $observer )
		{
			$observer->updateForCommitedDomainEventEnvelope( $event_envelope );
		}
	}

	/**
	 * @param IdentifiesEventStream $id
	 *
	 * @throws EventEnvelopesNotFound
	 * @return WrapsDomainEvent[]
	 */
	private function getStoredEventsWithId( IdentifiesEventStream $id )
	{
		$envelopes = $this->getStoredEventEnvelopesWithId( $id );

		return $this->extractEventsFromEnvelopes( $envelopes );
	}

	/**
	 * @param IdentifiesEventStream $id
	 *
	 * @throws EventEnvelopesNotFound
	 * @return WrapsEventForCommit[]
	 */
	private function getStoredEventEnvelopesWithId( IdentifiesEventStream $id )
	{
		try
		{
			return $this->persistence->getEventEnvelopesWithId( $id );
		}
		catch ( \Exception $e )
		{
			throw new EventEnvelopesNotFound( $id->getStreamIdContract() . '#' . $id->getStreamId() );
		}
	}

	/**
	 * @param WrapsEventForCommit[] $envelopes
	 *
	 * @return WrapsDomainEvent[]
	 */
	private function extractEventsFromEnvelopes( array $envelopes )
	{
		return $this->envelope_mapper->extractFromCommitEnvelopes( $envelopes );
	}
}
