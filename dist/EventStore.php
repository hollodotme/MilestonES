<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\CommittingEventsFailed;
use hollodotme\MilestonES\Exceptions\EventEnvelopesNotFound;
use hollodotme\MilestonES\Exceptions\EventStreamNotFound;
use hollodotme\MilestonES\Exceptions\InvalidEventEnvelopeCollection;
use hollodotme\MilestonES\Exceptions\PersistingEventsFailed;
use hollodotme\MilestonES\Interfaces\CarriesCommitData;
use hollodotme\MilestonES\Interfaces\CollectsEventEnvelopes;
use hollodotme\MilestonES\Interfaces\IdentifiesCommit;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;
use hollodotme\MilestonES\Interfaces\ListensForPublishedEvents;
use hollodotme\MilestonES\Interfaces\PersistsEventEnvelopes;
use hollodotme\MilestonES\Interfaces\ServesEventStoreConfig;
use hollodotme\MilestonES\Interfaces\StoresEvents;
use hollodotme\MilestonES\Interfaces\WrapsDomainEvent;

/**
 * Class EventStores
 *
 * @package hollodotme\MilestonES
 */
final class EventStore implements StoresEvents
{

	/** @var ServesEventStoreConfig */
	private $eventStoreConfig;

	/** @var ListensForPublishedEvents[] */
	private $eventListeners = [ ];

	/** @var PersistsEventEnvelopes */
	private $persistence;

	/** @var EventEnvelopeMapper */
	private $envelopeMapper;

	/**
	 * @param ServesEventStoreConfig $eventStoreConfig
	 */
	public function __construct( ServesEventStoreConfig $eventStoreConfig )
	{
		$this->eventStoreConfig = $eventStoreConfig;
		$this->persistence      = $this->getPersistenceStrategy();
		$this->envelopeMapper   = $this->getEventEnvelopeMapper();
	}

	/**
	 * @param CollectsEventEnvelopes $eventEnvelopes
	 *
	 * @throws CommittingEventsFailed
	 */
	public function commitEvents( CollectsEventEnvelopes $eventEnvelopes )
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
	 * @param IdentifiesObject $id
	 *
	 * @throws EventStreamNotFound
	 * @return EventStream
	 */
	public function getEventStreamForId( IdentifiesObject $id )
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
	 * @param IdentifiesObject $id
	 *
	 * @return EventStreamIdentifier
	 */
	protected function getEventStreamId( IdentifiesObject $id )
	{
		return new EventStreamIdentifier( $id );
	}

	/**
	 * @param ListensForPublishedEvents $eventListener
	 */
	public function attachEventListener( ListensForPublishedEvents $eventListener )
	{
		if ( !$this->eventListenerIsAttached( $eventListener ) )
		{
			$this->eventListeners[] = $eventListener;
		}
	}

	/**
	 * @param ListensForPublishedEvents $eventListener
	 *
	 * @return bool
	 */
	private function eventListenerIsAttached( ListensForPublishedEvents $eventListener )
	{
		return in_array( $eventListener, $this->eventListeners, true );
	}

	/**
	 * @param ListensForPublishedEvents $eventListener
	 */
	public function detachEventListener( ListensForPublishedEvents $eventListener )
	{
		if ( $this->eventListenerIsAttached( $eventListener ) )
		{
			$this->eventListeners = array_filter(
				$this->eventListeners,
				function ( ListensForPublishedEvents $obs ) use ( $eventListener )
				{
					return ($eventListener !== $obs);
				}
			);
		}
	}

	/**
	 * @param CollectsEventEnvelopes $events
	 *
	 * @throws \Exception
	 */
	private function persistEvents( CollectsEventEnvelopes $events )
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
	 * @param IdentifiesCommit       $commit
	 * @param CollectsEventEnvelopes $eventEnvelopes
	 */
	private function persistEventsInTransaction(
		IdentifiesCommit $commit,
		CollectsEventEnvelopes $eventEnvelopes
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

		$this->persistence->persistCommitEnvelope( $commitEnvelope );
	}

	/**
	 * @param WrapsDomainEvent $eventEnvelope
	 * @param IdentifiesCommit $commit
	 *
	 * @return CommitEnvelope
	 */
	private function getCommitEventEnvelope( WrapsDomainEvent $eventEnvelope, IdentifiesCommit $commit )
	{
		$mapper = $this->getEventEnvelopeMapper();

		return $mapper->putEventInEnvelopeForCommit( $eventEnvelope, $commit );
	}

	/**
	 * @param CollectsEventEnvelopes $eventEnvelopes
	 */
	private function publishCommitedEvents( CollectsEventEnvelopes $eventEnvelopes )
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
		foreach ( $this->eventListeners as $observer )
		{
			$observer->update( $eventEnvelope );
		}

		$globalObservers = $this->getGlobalObserversForCommitedEvents();

		foreach ( $globalObservers as $observer )
		{
			$observer->update( $eventEnvelope );
		}
	}

	/**
	 * @return Interfaces\ListensForPublishedEvents[]
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
	 * @return CarriesCommitData[]
	 */
	private function getStoredEventEnvelopesWithId( IdentifiesEventStream $id )
	{
		try
		{
			$eventEnvelopes = $this->persistence->getEventStreamWithId( $id );

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
	 * @param CarriesCommitData[] $envelopes
	 *
	 * @throws InvalidEventEnvelopeCollection
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
			throw new InvalidEventEnvelopeCollection();
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
