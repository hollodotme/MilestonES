<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\EventClassDoesNotExist;
use hollodotme\MilestonES\Interfaces\Identifies;
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
	private $serialization_config;

	/**
	 * @param SerializationStrategy $serialization_config
	 */
	public function __construct( SerializationStrategy $serialization_config )
	{
		$this->serialization_config = $serialization_config;
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
		$payload_data       = $this->serializeDataFromEventWithContract( $event->getPayload(), $payload_contract );
		$meta_data_contract = $this->getMetaDataContract( $event );
		$meta_data          = $this->serializeDataFromEventWithContract( $event->getMetaData(), $meta_data_contract );

		$envelope = new CommitEventEnvelope();
		$envelope->setCommitId( $commit->getId() );

		$envelope->setOccuredOn( $event->getOccuredOn() );
		$envelope->setCommittedOn( $commit->getDateTime() );

		$envelope->setStreamId( $stream_identifier->getStreamId() );
		$envelope->setStreamTypeId( $stream_identifier->getStreamTypeId() );

		$envelope->setVersion( $event->getVersion() );

		$envelope->setEventName( $event->getName() );

		$envelope->setPayloadContract( $payload_contract->toString() );
		$envelope->setPayload( $payload_data );

		$envelope->setMetaDataContract( $meta_data_contract->toString() );
		$envelope->setMetaData( $meta_data );

		return $envelope;
	}

	public function extractEventFromEnvelope( WrapsEventForCommit $envelope )
	{
		$event = $this->getEventInstanceFromEnvelope( $envelope );
		$event->setVersion( $envelope->getVersion() );
		$event->setOccuredOn( $envelope->getOccuredOn() );
		$event->reconstituteFromPayload( $envelope->getPayload() );
	}

	/**
	 * @param WrapsEventForCommit $envelope
	 *
	 * @throws EventClassDoesNotExist
	 * @return RepresentsEvent
	 */
	private function getEventInstanceFromEnvelope( WrapsEventForCommit $envelope )
	{
		$event_class = $envelope->getEventName();
		if ( class_exists( $event_class, true ) )
		{
			return new $event_class( $envelope->getStreamId(), $envelope->getMetaData() );
		}
		else
		{
			throw new EventClassDoesNotExist( $event_class );
		}
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
	 * @return Identifies
	 */
	private function getPayloadContract()
	{
		return $this->serialization_config->getDefaultContract();
	}

	/**
	 * @return Identifies
	 */
	private function getMetaDataContract()
	{
		return $this->serialization_config->getDefaultContract();
	}

	/**
	 * @param mixed      $data
	 * @param Identifies $contract
	 *
	 * @return string
	 */
	private function serializeDataFromEventWithContract( $data, Identifies $contract )
	{
		$serializer = $this->getSerializerForContract( $contract );

		return $serializer->serializeData( $data );
	}

	/**
	 * @param Identifies $contract
	 *
	 * @return Interfaces\SerializesData
	 */
	private function getSerializerForContract( Identifies $contract )
	{
		return $this->serialization_config->getSerializerForContract( $contract );
	}
}
