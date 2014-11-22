<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\IdentifiesCommit;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Interfaces\RepresentsEvent;
use hollodotme\MilestonES\Interfaces\WrapsDomainEvent;
use hollodotme\MilestonES\Interfaces\WrapsEventForCommit;

/**
 * Class EventEnvelopeMapper
 *
 * @package hollodotme\MilestonES
 */
class EventEnvelopeMapper
{

	/** @var SerializationStrategy */
	private $serialization_strategy;

	/**
	 * @param SerializationStrategy $serialization_strategy
	 */
	public function __construct( SerializationStrategy $serialization_strategy )
	{
		$this->serialization_strategy = $serialization_strategy;
	}

	/**
	 * @param WrapsDomainEvent $event_envelope
	 * @param IdentifiesCommit $commit
	 *
	 * @return CommitEventEnvelope
	 */
	public function putEventInEnvelopeForCommit( WrapsDomainEvent $event_envelope, IdentifiesCommit $commit )
	{
		$stream_identifier  = $this->getStreamIdentifierForEventEnvelope( $event_envelope );
		$payload_contract   = $this->getPayloadContract();
		$payload_data       = $this->serializeDataWithContract( $event_envelope->getPayload(), $payload_contract );
		$meta_data_contract = $this->getMetaDataContract();
		$meta_data          = $this->serializeDataWithContract( $event_envelope->getMetaData(), $meta_data_contract );

		$envelope = new CommitEventEnvelope();
		$envelope->setCommitId( $commit->getId() );

		$envelope->setOccurredOn( $event_envelope->getOccurredOn() );
		$envelope->setCommittedOn( $commit->getDateTime() );

		$envelope->setStreamId( $stream_identifier->getStreamId() );
		$envelope->setStreamIdContract( $stream_identifier->getStreamIdContract() );

		$envelope->setPayloadContract( $payload_contract->toString() );
		$envelope->setPayload( $payload_data );

		$envelope->setMetaDataContract( $meta_data_contract->toString() );
		$envelope->setMetaData( $meta_data );

		$envelope->setFile( $event_envelope->getFile() );

		return $envelope;
	}

	/**
	 * @param WrapsEventForCommit[] $commit_envelopes
	 *
	 * @return WrapsDomainEvent[]
	 */
	public function extractFromCommitEnvelopes( array $commit_envelopes )
	{
		$events = [ ];

		foreach ( $commit_envelopes as $commit_envelope )
		{
			$events[] = $this->extractEventEnvelopeFromCommitEnvelope( $commit_envelope );
		}

		return $events;
	}

	/**
	 * @param WrapsEventForCommit $commit_envelope
	 *
	 * @return RepresentsEvent
	 */
	private function extractEventEnvelopeFromCommitEnvelope( WrapsEventForCommit $commit_envelope )
	{
		$event       = $this->getEventFromCommitEnvelope( $commit_envelope );
		$meta_data   = $this->getMetaDataFromCommitEnvelope( $commit_envelope );
		$occurred_on = $commit_envelope->getOccurredOn();
		$file = $commit_envelope->getFile();

		return DomainEventEnvelope::fromRecord( $event, $meta_data, $file, $occurred_on );
	}

	/**
	 * @param WrapsEventForCommit $commit_envelope
	 *
	 * @return mixed
	 */
	private function getEventFromCommitEnvelope( WrapsEventForCommit $commit_envelope )
	{
		$payload_contract = Contract::fromString( $commit_envelope->getPayloadContract() );

		return $this->unserializeDataWithContract( $commit_envelope->getPayload(), $payload_contract );
	}

	/**
	 * @param WrapsEventForCommit $commit_envelope
	 *
	 * @return mixed
	 */
	private function getMetaDataFromCommitEnvelope( WrapsEventForCommit $commit_envelope )
	{
		$meta_data_contract = Contract::fromString( $commit_envelope->getMetaDataContract() );

		return $this->unserializeDataWithContract( $commit_envelope->getMetaData(), $meta_data_contract );
	}

	/**
	 * @param WrapsDomainEvent $event_envelope
	 *
	 * @return IdentifiesEventStream
	 */
	private function getStreamIdentifierForEventEnvelope( WrapsDomainEvent $event_envelope )
	{
		return new EventStreamIdentifier( $event_envelope->getStreamId() );
	}

	/**
	 * @return Contract
	 */
	private function getPayloadContract()
	{
		return $this->serialization_strategy->getDefaultContract();
	}

	/**
	 * @return Contract
	 */
	private function getMetaDataContract()
	{
		return $this->serialization_strategy->getDefaultContract();
	}

	/**
	 * @param mixed    $data
	 * @param Contract $contract
	 *
	 * @return string
	 */
	private function serializeDataWithContract( $data, Contract $contract )
	{
		$serializer = $this->getSerializerForContract( $contract );

		return $serializer->serializeData( $data );
	}

	/**
	 * @param string   $data
	 * @param Contract $contract
	 *
	 * @return mixed
	 */
	private function unserializeDataWithContract( $data, Contract $contract )
	{
		$serializer = $this->getSerializerForContract( $contract );

		return $serializer->unserializeData( $data );
	}

	/**
	 * @param Contract $contract
	 *
	 * @return Interfaces\SerializesData
	 */
	private function getSerializerForContract( Contract $contract )
	{
		return $this->serialization_strategy->getSerializerForContract( $contract );
	}
}
