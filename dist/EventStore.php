<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\CommittingEventsFailed;
use hollodotme\MilestonES\Exceptions\EventEnvelopesNotFound;
use hollodotme\MilestonES\Exceptions\EventStreamNotFound;
use hollodotme\MilestonES\Exceptions\InvalidEventEnvelopesCollection;
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
	private $eventStoreConfig;

	/** @var ObservesCommitedEvents[] */
	private $commitedEventObservers = [ ];

	/** @var PersistsEventEnvelopes */
	private $persistence;

	/** @var EventEnvelopeMapper */
	private $envelopeMapper;

	/**
	 * @param ServesEventStoreConfiguration $eventStoreConfig
	 */
	public function __construct( ServesEventStoreConfiguration $eventStoreConfig )
	{
		$this->eventStoreConfig = $eventStoreConfig;
		$this->persistence      = $this->getPersistenceStrategy();
		$this->envelopeMapper   = $this->getEventEnvelopeMapper();
	}

	/**
	 * @param CollectsDomainEventEnvelopes $eventEnvelopes
	 *
	 * @throws CommittingEventsFailed
	 */
	public function commitEvents( CollectsDomainEventEnvelopes $eventEnvelopes )
	{
		try
		{
			$this->persistEvents( $eventEnvelopes );
		}
		catch ( PersistingEventsFailed $e )
		{
			throw new CommittingEventsFailed( '', 0, $e );
		}

		$this->publishCommitedEvents( $eventEnvelopes );
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
			$eventStreamId = $this->getEventStreamId( $id );
			$events        = $this->getStoredEventsWithId( $eventStreamId );

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
		$this->commitedEventObservers[] = $observer;
	}

	/**
	 * @param ObservesCommitedEvents $observer
	 */
	public function detachCommittedEventObserver( ObservesCommitedEvents $observer )
	{
		$this->commitedEventObservers = array_filter(
			$this->commitedEventObservers,
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
		return $this->eventStoreConfig->getPersistenceStrategy();
	}

	/**
	 * @return EventEnvelopeMapper
	 */
	private function getEventEnvelopeMapper()
	{
		$serializationStrategy = $this->getSerializationStrategy();

		return new EventEnvelopeMapper( $serializationStrategy );
	}

	/**
	 * @return SerializationStrategy
	 */
	private function getSerializationStrategy()
	{
		return $this->eventStoreConfig->getSerializationStrategy();
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
	 * @param CollectsDomainEventEnvelopes $eventEnvelopes
	 */
	private function persistEventsInTransaction(
		IdentifiesCommit $commit,
		CollectsDomainEventEnvelopes $eventEnvelopes
	)
	{
		foreach ( $eventEnvelopes as $event )
		{
			$this->commitEvent( $commit, $event );
		}
	}

	/**
	 * @param IdentifiesCommit $commit
	 * @param WrapsDomainEvent $eventEnvelope
	 */
	private function commitEvent( IdentifiesCommit $commit, WrapsDomainEvent $eventEnvelope )
	{
		$commitEnvelope = $this->getCommitEventEnvelope( $eventEnvelope, $commit );

		$this->persistence->persistEventEnvelope( $commitEnvelope );
	}

	/**
	 * @param WrapsDomainEvent $eventEnvelope
	 * @param IdentifiesCommit $commit
	 *
	 * @return CommitEventEnvelope
	 */
	private function getCommitEventEnvelope( WrapsDomainEvent $eventEnvelope, IdentifiesCommit $commit )
	{
		$mapper = $this->getEventEnvelopeMapper();

		return $mapper->putEventInEnvelopeForCommit( $eventEnvelope, $commit );
	}

	/**
	 * @param CollectsDomainEventEnvelopes $eventEnvelopes
	 */
	private function publishCommitedEvents( CollectsDomainEventEnvelopes $eventEnvelopes )
	{
		foreach ( $eventEnvelopes as $eventEnvelope )
		{
			$this->publishEvent( $eventEnvelope );
		}
	}

	/**
	 * @param WrapsDomainEvent $eventEnvelope
	 */
	private function publishEvent( WrapsDomainEvent $eventEnvelope )
	{
		$this->notifyAboutCommittedEvent( $eventEnvelope );
	}

	/**
	 * @param WrapsDomainEvent $eventEnvelope
	 */
	private function notifyAboutCommittedEvent( WrapsDomainEvent $eventEnvelope )
	{
		foreach ( $this->commitedEventObservers as $observer )
		{
			$observer->updateForCommitedDomainEventEnvelope( $eventEnvelope );
		}

		$globalObservers = $this->getGlobalObserversForCommitedEvents();

		foreach ( $globalObservers as $observer )
		{
			$observer->updateForCommitedDomainEventEnvelope( $eventEnvelope );
		}
	}

	/**
	 * @return Interfaces\ObservesCommitedEvents[]
	 */
	private function getGlobalObserversForCommitedEvents()
	{
		return $this->eventStoreConfig->getGlobalObserversForCommitedEvents();
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
			$eventEnvelopes = $this->persistence->getEventEnvelopesWithId( $id );

			if ( count( $eventEnvelopes ) == 0 )
			{
				throw new \Exception(
					'No event envelopes found for ' . $id->getStreamIdContract() . '#' . $id->getStreamId()
				);
			}
			else
			{
				return $eventEnvelopes;
			}
		}
		catch ( \Exception $e )
		{
			throw new EventEnvelopesNotFound( $id->getStreamIdContract() . '#' . $id->getStreamId() );
		}
	}

	/**
	 * @param WrapsEventForCommit[] $envelopes
	 *
	 * @throws InvalidEventEnvelopesCollection
	 * @return array|\Countable|Interfaces\WrapsDomainEvent[]|\Iterator
	 */
	private function extractEventsFromEnvelopes( $envelopes )
	{
		if ( $this->guardIsArrayOrCountableIterator( $envelopes ) )
		{
			return $this->envelopeMapper->extractEventEnvelopesFromCommitEnvelopes( $envelopes );
		}
		else
		{
			throw new InvalidEventEnvelopesCollection();
		}
	}

	/**
	 * @param mixed $envelopes
	 *
	 * @return bool
	 */
	private function guardIsArrayOrCountableIterator( $envelopes )
	{
		if ( is_array( $envelopes ) )
		{
			return true;
		}
		elseif ( $envelopes instanceof \Iterator )
		{
			if ( $envelopes instanceof \Countable )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}
