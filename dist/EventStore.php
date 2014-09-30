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
use hollodotme\MilestonES\Interfaces\CollectsEvents;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\IdentifiesCommit;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Interfaces\ObservesCommitedEvents;
use hollodotme\MilestonES\Interfaces\PersistsEventEnvelopes;
use hollodotme\MilestonES\Interfaces\RepresentsEvent;
use hollodotme\MilestonES\Interfaces\ServesEventStoreConfiguration;
use hollodotme\MilestonES\Interfaces\StoresEvents;
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
	 * @param CollectsEvents $events
	 *
	 * @throws CommittingEventsFailed
	 */
	public function commitEvents( CollectsEvents $events )
	{
		try
		{
			$this->persistEvents( $events );
		}
		catch ( PersistingEventsFailed $e )
		{
			throw new CommittingEventsFailed( '', 0, $e );
		}

		$this->publishCommitedEvents( $events );
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
	 * @param RepresentsEvent $event
	 */
	private function notifyAboutCommittedEvent( RepresentsEvent $event )
	{
		foreach ( $this->commited_event_observers as $observer )
		{
			$observer->updateForCommitedEvent( $event );
		}
	}

	/**
	 * @param CollectsEvents $events
	 *
	 * @throws \Exception
	 */
	private function persistEvents( CollectsEvents $events )
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
		return new Commit( CommitId::generate(), new \DateTime( 'now' ) );
	}

	/**
	 * @param IdentifiesCommit $commit
	 * @param CollectsEvents   $events
	 */
	private function persistEventsInTransaction( IdentifiesCommit $commit, CollectsEvents $events )
	{
		foreach ( $events as $event )
		{
			$this->commitEvent( $commit, $event );
		}
	}

	/**
	 * @param IdentifiesCommit $commit
	 * @param RepresentsEvent  $event
	 */
	private function commitEvent( IdentifiesCommit $commit, RepresentsEvent $event )
	{
		$event_envelope = $this->getEnvelopeForEventCommit( $event, $commit );

		$this->persistence->persistEventEnvelope( $event_envelope );
	}

	/**
	 * @param RepresentsEvent $event
	 * @param IdentifiesCommit $commit
	 *
	 * @return CommitEventEnvelope
	 */
	private function getEnvelopeForEventCommit( RepresentsEvent $event, IdentifiesCommit $commit )
	{
		$mapper = $this->getEventEnvelopeMapper();

		return $mapper->putEventInEnvelopeForCommit( $event, $commit );
	}

	/**
	 * @param CollectsEvents $events
	 */
	private function publishCommitedEvents( CollectsEvents $events )
	{
		foreach ( $events as $event )
		{
			$this->publishEvent( $event );
		}
	}

	/**
	 * @param IdentifiesEventStream $id
	 *
	 * @throws EventEnvelopesNotFound
	 * @return RepresentsEvent[]
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
	 * @return RepresentsEvent[]
	 */
	private function extractEventsFromEnvelopes( array $envelopes )
	{
		return $this->envelope_mapper->extractEventsFromEnvelopes( $envelopes );
	}

	/**
	 * @param RepresentsEvent $event
	 */
	private function publishEvent( RepresentsEvent $event )
	{
		$this->notifyAboutCommittedEvent( $event );
	}
}
