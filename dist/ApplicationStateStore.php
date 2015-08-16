<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\CommittingEventsFailed;
use hollodotme\MilestonES\Exceptions\CommittingSnapshotsFailed;
use hollodotme\MilestonES\Exceptions\EventEnvelopesNotFound;
use hollodotme\MilestonES\Exceptions\EventStreamNotFound;
use hollodotme\MilestonES\Exceptions\InvalidEventEnvelopeCollection;
use hollodotme\MilestonES\Exceptions\PersistingEventsFailed;
use hollodotme\MilestonES\Exceptions\PersistingSnapshotsFailed;
use hollodotme\MilestonES\Exceptions\SnapshotNotFound;
use hollodotme\MilestonES\Interfaces\CarriesCommitData;
use hollodotme\MilestonES\Interfaces\CollectsEventEnvelopes;
use hollodotme\MilestonES\Interfaces\IdentifiesCommit;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Interfaces\IdentifiesObject;
use hollodotme\MilestonES\Interfaces\ListensForPublishedEvents;
use hollodotme\MilestonES\Interfaces\PersistsCommitEnvelopes;
use hollodotme\MilestonES\Interfaces\PersistsSnapshots;
use hollodotme\MilestonES\Interfaces\ServesApplicationStateStoreConfig;
use hollodotme\MilestonES\Interfaces\ServesEventStreamData;
use hollodotme\MilestonES\Interfaces\StoresApplicationState;
use hollodotme\MilestonES\Snapshots\Interfaces\CarriesSnapshotData;
use hollodotme\MilestonES\Snapshots\Interfaces\CollectsSnapshots;

/**
 * Class EventStores
 *
 * @package hollodotme\MilestonES
 */
final class ApplicationStateStore implements StoresApplicationState
{
	/** @var ListensForPublishedEvents[] */
	private $eventListeners;

	/** @var ListensForPublishedEvents[] */
	private $globalEventListeners;

	/** @var PersistsCommitEnvelopes */
	private $eventPersistence;

	/** @var PersistsSnapshots */
	private $snapshotPersistence;

	/** @var EventEnvelopeMapper */
	private $envelopeMapper;

	/**
	 * @param ServesApplicationStateStoreConfig $eventStoreConfig
	 */
	public function __construct( ServesApplicationStateStoreConfig $eventStoreConfig )
	{
		$this->eventListeners       = [ ];
		$this->globalEventListeners = $eventStoreConfig->getGlobalEventListeners();
		$this->eventPersistence     = $eventStoreConfig->getEventPersistence();
		$this->snapshotPersistence  = $eventStoreConfig->getSnapshotPersistence();
		$this->envelopeMapper       = new EventEnvelopeMapper( $eventStoreConfig->getSerializationStrategy() );
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
			throw new CommittingEventsFailed( $e->getMessage(), 0, $e );
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
	 * @param CollectsSnapshots $snapshots
	 *
	 * @throws CommittingSnapshotsFailed
	 */
	public function commitSnapshots( CollectsSnapshots $snapshots )
	{
		try
		{
			$this->persistSnapshots( $snapshots );
		}
		catch ( PersistingSnapshotsFailed $e )
		{
			throw new CommittingSnapshotsFailed( $e->getMessage(), 0, $e );
		}
	}

	/**
	 * @param CollectsSnapshots|CarriesSnapshotData[] $snapshots
	 *
	 * @throws PersistingSnapshotsFailed
	 */
	private function persistSnapshots( CollectsSnapshots $snapshots )
	{
		$this->snapshotPersistence->beginTransaction();

		try
		{
			foreach ( $snapshots as $snapshot )
			{
				$this->snapshotPersistence->persistSnapshot( $snapshot );
			}

			$this->snapshotPersistence->commitTransaction();
		}
		catch ( \Exception $e )
		{
			$this->snapshotPersistence->rollbackTransaction();

			throw new PersistingSnapshotsFailed( $e->getMessage(), 0, $e );
		}
	}

	/**
	 * @param IdentifiesObject $streamId
	 *
	 * @return CarriesSnapshotData
	 * @throws SnapshotNotFound
	 */
	public function getLatestSnapshotForStreamId( IdentifiesObject $streamId )
	{
		try
		{
			return $this->snapshotPersistence->getLatestSnapshotForStreamId( $streamId );
		}
		catch ( \Exception $e )
		{
			throw new SnapshotNotFound( $streamId->toString(), 0, $e );
		}
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
		$this->eventPersistence->beginTransaction();

		try
		{
			$commit = $this->getCommit();

			$this->persistEventsInTransaction( $commit, $events );

			$this->eventPersistence->commitTransaction();
		}
		catch ( \Exception $e )
		{
			$this->eventPersistence->rollbackTransaction();

			throw new PersistingEventsFailed( $e->getMessage(), 0, $e );
		}
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
	private function persistEventsInTransaction( IdentifiesCommit $commit, CollectsEventEnvelopes $eventEnvelopes )
	{
		foreach ( $eventEnvelopes as $event )
		{
			$this->commitEvent( $commit, $event );
		}
	}

	/**
	 * @param IdentifiesCommit $commit
	 * @param ServesEventStreamData $eventEnvelope
	 */
	private function commitEvent( IdentifiesCommit $commit, ServesEventStreamData $eventEnvelope )
	{
		$commitEnvelope = $this->getCommitEnvelope( $eventEnvelope, $commit );

		$this->eventPersistence->persistCommitEnvelope( $commitEnvelope );
	}

	/**
	 * @param ServesEventStreamData $eventEnvelope
	 * @param IdentifiesCommit      $commit
	 *
	 * @return CommitEnvelope
	 */
	private function getCommitEnvelope( ServesEventStreamData $eventEnvelope, IdentifiesCommit $commit )
	{
		return $this->envelopeMapper->createCommitEnvelope( $eventEnvelope, $commit );
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
	 * @param ServesEventStreamData $eventEnvelope
	 */
	private function publishEvent( ServesEventStreamData $eventEnvelope )
	{
		$this->notifyAboutCommittedEvent( $eventEnvelope );
	}

	/**
	 * @param ServesEventStreamData $eventEnvelope
	 */
	private function notifyAboutCommittedEvent( ServesEventStreamData $eventEnvelope )
	{
		foreach ( $this->eventListeners as $eventListener )
		{
			$eventListener->update( $eventEnvelope );
		}

		foreach ( $this->globalEventListeners as $eventListener )
		{
			$eventListener->update( $eventEnvelope );
		}
	}

	/**
	 * @param IdentifiesEventStream $id
	 *
	 * @throws EventEnvelopesNotFound
	 * @return ServesEventStreamData[]
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
			$eventEnvelopes = $this->eventPersistence->getEventStreamWithId( $id );

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
	 * @return array|\Countable|Interfaces\ServesEventStreamData[]|\Iterator
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
