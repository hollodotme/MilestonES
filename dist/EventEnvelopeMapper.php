<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\EventClassDoesNotExist;
use hollodotme\MilestonES\Exceptions\IdentifierClassDoesNotExist;
use hollodotme\MilestonES\Interfaces\IdentifiesCommit;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Interfaces\RepresentsEvent;
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
	 * @param RepresentsEvent  $event
	 * @param IdentifiesCommit $commit
	 *
	 * @return CommitEventEnvelope
	 */
	public function putEventInEnvelopeForCommit( RepresentsEvent $event, IdentifiesCommit $commit )
	{
		$stream_identifier  = $this->getStreamIdentifierForEvent( $event );

		$payload_contract   = $this->getPayloadContract( $event );
		$payload_data = $this->serializeDataFromEventWithContract( $event->getPayloadDTO(), $payload_contract );

		$meta_data_contract = $this->getMetaDataContract( $event );
		$meta_data = $this->serializeDataFromEventWithContract( $event->getMetaDTO(), $meta_data_contract );

		$envelope = new CommitEventEnvelope();
		$envelope->setCommitId( $commit->getId() );

		$envelope->setOccurredOn( $event->getOccuredOn() );
		$envelope->setCommittedOn( $commit->getDateTime() );

		$envelope->setStreamId( $stream_identifier->getStreamId() );
		$envelope->setStreamIdContract( $stream_identifier->getStreamIdContract() );

		$envelope->setStreamVersion( $event->getVersion() );

		$envelope->setEventContract( $event->getContract() );

		$envelope->setPayloadContract( $payload_contract->toString() );
		$envelope->setPayload( $payload_data );

		$envelope->setMetaDataContract( $meta_data_contract->toString() );
		$envelope->setMetaData( $meta_data );

		return $envelope;
	}

	/**
	 * @param WrapsEventForCommit[] $envelopes
	 *
	 * @throws EventClassDoesNotExist
	 * @return RepresentsEvent[]
	 */
	public function extractEventsFromEnvelopes( array $envelopes )
	{
		$events = [ ];

		foreach ( $envelopes as $envelope )
		{
			$events[] = $this->extractEventFromEnvelope( $envelope );
		}

		return $events;
	}

	/**
	 * @param WrapsEventForCommit $envelope
	 *
	 * @throws EventClassDoesNotExist
	 * @return RepresentsEvent
	 */
	private function extractEventFromEnvelope( WrapsEventForCommit $envelope )
	{
		$event     = $this->getEventInstanceFromEnvelope( $envelope );
		$payload   = $this->getPayloadFromEnvelope( $envelope );
		$meta_data = $this->getMetaDataFromEnvelope( $envelope );

		$event->setVersion( $envelope->getStreamVersion() );
		$event->setOccuredOn( $envelope->getOccurredOn() );
		$event->setPayloadDTO( $payload );
		$event->setMetaDTO( $meta_data );

		return $event;
	}

	/**
	 * @param WrapsEventForCommit $envelope
	 *
	 * @return mixed
	 */
	private function getPayloadFromEnvelope( WrapsEventForCommit $envelope )
	{
		$payload_contract = $this->getContractFromString( $envelope->getPayloadContract() );

		return $this->unserializeDataFromEnvelopeWithContract( $envelope->getPayload(), $payload_contract );
	}

	/**
	 * @param WrapsEventForCommit $envelope
	 *
	 * @return mixed
	 */
	private function getMetaDataFromEnvelope( WrapsEventForCommit $envelope )
	{
		$meta_data_contract = $this->getContractFromString( $envelope->getMetaDataContract() );

		return $this->unserializeDataFromEnvelopeWithContract( $envelope->getMetaData(), $meta_data_contract );
	}

	/**
	 * @param string $contract_string
	 *
	 * @return Contract
	 */
	private function getContractFromString( $contract_string )
	{
		return Contract::fromString( $contract_string );
	}

	/**
	 * @param WrapsEventForCommit $envelope
	 *
	 * @throws EventClassDoesNotExist
	 * @return RepresentsEvent
	 */
	private function getEventInstanceFromEnvelope( WrapsEventForCommit $envelope )
	{
		$event_class = $this->getEventClassNameFromEnvelope( $envelope );
		if ( class_exists( $event_class, true ) )
		{
			$stream_id = $this->getStreamIdFromEnvelope( $envelope );

			return new $event_class( $stream_id );
		}
		else
		{
			throw new EventClassDoesNotExist( $event_class );
		}
	}

	/**
	 * @param WrapsEventForCommit $envelope
	 *
	 * @return string
	 */
	private function getEventClassNameFromEnvelope( WrapsEventForCommit $envelope )
	{
		$event_contract = Contract::fromString( $envelope->getEventContract() );

		return $event_contract->getFullQualifiedClassName();
	}

	/**
	 * @param WrapsEventForCommit $envelope
	 *
	 * @throws IdentifierClassDoesNotExist
	 * @return Interfaces\Identifies
	 */
	private function getStreamIdFromEnvelope( WrapsEventForCommit $envelope )
	{
		$id_class = $this->getStreamIdClassNameFromEnvelope( $envelope );
		if ( class_exists( $id_class, true ) && is_callable( [ $id_class, 'fromString' ] ) )
		{
			/** @var $id_class Interfaces\Identifies */
			return $id_class::fromString( $envelope->getStreamId() );
		}
		else
		{
			throw new IdentifierClassDoesNotExist( $id_class );
		}
	}

	/**
	 * @param WrapsEventForCommit $envelope
	 *
	 * @return string
	 */
	private function getStreamIdClassNameFromEnvelope( WrapsEventForCommit $envelope )
	{
		$stream_id_contract = Contract::fromString( $envelope->getStreamIdContract() );

		return $stream_id_contract->getFullQualifiedClassName();
	}

	/**
	 * @param RepresentsEvent $event
	 *
	 * @return IdentifiesEventStream
	 */
	private function getStreamIdentifierForEvent( RepresentsEvent $event )
	{
		return new EventStreamIdentifier( $event->getStreamId() );
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
	private function serializeDataFromEventWithContract( $data, Contract $contract )
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
	private function unserializeDataFromEnvelopeWithContract( $data, Contract $contract )
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
