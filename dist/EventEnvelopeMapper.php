<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\EnvelopesEvent;
use hollodotme\MilestonES\Interfaces\Event;
use hollodotme\MilestonES\Interfaces\IdentifiesCommit;

/**
 * Class EventEnvelopeMapper
 *
 * @package hollodotme\MilestonES
 */
class EventEnvelopeMapper
{
	/**
	 * @param Event            $event
	 * @param IdentifiesCommit $commit
	 *
	 * @return EventEnvelope
	 */
	public function putEventInEnvelopeForCommit( Event $event, IdentifiesCommit $commit )
	{
		$stream_type_id     = $this->getStreamTypeIdForEvent( $event );
		$payload_contract   = $this->getPayloadContractForEvent( $event );
		$meta_data_contract = $this->getMetaDataContractForEvent( $event );

		$envelope = new EventEnvelope();
		$envelope->setCommitId( $commit->getId() );

		$envelope->setOccuredOn( $event->getOccuredOn() );
		$envelope->setCommittedOn( $commit->getDateTime() );

		$envelope->setStreamId( $event->getStreamId() );
		$envelope->setStreamTypeId( $stream_type_id );

		$envelope->setVersion( $event->getVersion() );

		$envelope->setEventName( $event->getName() );

		$envelope->setPayload( $event->getPayload() );
		$envelope->setPayloadContract( $payload_contract );

		$envelope->setMetaData( $event->getMetaData() );
		$envelope->setMetaDataContract( $meta_data_contract );

		return $envelope;
	}

	public function extractEventFromEnvelope( EnvelopesEvent $envelope )
	{
	}

	/**
	 * @param Event $event
	 *
	 * @return ClassNameIdentifier
	 */
	private function getStreamTypeIdForEvent( Event $event )
	{
		/** @todo use AggregateRootTypeMap here to determine the correct type */
		return new ClassNameIdentifier( get_class( $event->getStreamId() ) );
	}

	/**
	 * @param Event $event
	 *
	 * @return ClassNameIdentifier
	 */
	private function getPayloadContractForEvent( Event $event )
	{
		return new ClassNameIdentifier( 'PHP\\Json' );
	}

	/**
	 * @param Event $event
	 *
	 * @return ClassNameIdentifier
	 */
	private function getMetaDataContractForEvent( Event $event )
	{
		return new ClassNameIdentifier( 'PHP\\Json' );
	}
}
