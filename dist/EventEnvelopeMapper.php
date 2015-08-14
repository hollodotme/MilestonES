<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Interfaces\CarriesCommitData;
use hollodotme\MilestonES\Interfaces\CarriesEventData;
use hollodotme\MilestonES\Interfaces\IdentifiesCommit;
use hollodotme\MilestonES\Interfaces\IdentifiesEventStream;
use hollodotme\MilestonES\Interfaces\ServesEventStreamData;

/**
 * Class EventEnvelopeMapper
 *
 * @package hollodotme\MilestonES
 */
class EventEnvelopeMapper
{

	/** @var SerializationStrategy */
	private $serializationStrategy;

	/**
	 * @param SerializationStrategy $serializationStrategy
	 */
	public function __construct( SerializationStrategy $serializationStrategy )
	{
		$this->serializationStrategy = $serializationStrategy;
	}

	/**
	 * @param ServesEventStreamData $eventEnvelope
	 * @param IdentifiesCommit $commit
	 *
	 * @return CommitEnvelope
	 */
	public function putEventInEnvelopeForCommit( ServesEventStreamData $eventEnvelope, IdentifiesCommit $commit )
	{
		$streamIdentifier = $this->getStreamIdentifierForEventEnvelope( $eventEnvelope );
		$payloadContract  = $this->getPayloadContract();
		$payloadData      = $this->serializeDataWithContract( $eventEnvelope->getPayload(), $payloadContract );
		$metaDataContract = $this->getMetaDataContract();
		$metaData         = $this->serializeDataWithContract( $eventEnvelope->getMetaData(), $metaDataContract );

		$envelope = new CommitEnvelope();
		$envelope->setCommitId( $commit->getCommitId() );

		$envelope->setOccurredOn( $eventEnvelope->getOccurredOn() );
		$envelope->setCommittedOn( $commit->getCommittedOn() );

		$envelope->setStreamId( $streamIdentifier->getStreamId() );
		$envelope->setStreamIdContract( $streamIdentifier->getStreamIdContract() );

		$envelope->setPayloadContract( $payloadContract->toString() );
		$envelope->setPayload( $payloadData );

		$envelope->setMetaDataContract( $metaDataContract->toString() );
		$envelope->setMetaData( $metaData );

		$envelope->setFile( $eventEnvelope->getFile() );

		return $envelope;
	}

	/**
	 * @param CarriesCommitData[] $commitEnvelopes
	 *
	 * @return array|\Iterator|\Countable|ServesEventStreamData[]
	 */
	public function extractEventEnvelopesFromCommitEnvelopes( $commitEnvelopes )
	{
		$events = [ ];

		foreach ( $commitEnvelopes as $commitEnvelope )
		{
			$events[] = $this->extractEventEnvelopeFromCommitEnvelope( $commitEnvelope );
		}

		return $events;
	}

	/**
	 * @param CarriesCommitData $commitEnvelope
	 *
	 * @return CarriesEventData
	 */
	private function extractEventEnvelopeFromCommitEnvelope( CarriesCommitData $commitEnvelope )
	{
		$event      = $this->getEventFromCommitEnvelope( $commitEnvelope );
		$metaData   = $this->getMetaDataFromCommitEnvelope( $commitEnvelope );
		$occurredOn = $commitEnvelope->getOccurredOn();
		$file       = $commitEnvelope->getFile();

		return EventEnvelope::fromRecord( $event, $metaData, $file, $occurredOn );
	}

	/**
	 * @param CarriesCommitData $commitEnvelope
	 *
	 * @return mixed
	 */
	private function getEventFromCommitEnvelope( CarriesCommitData $commitEnvelope )
	{
		$payloadContract = Contract::fromString( $commitEnvelope->getPayloadContract() );

		return $this->unserializeDataWithContract( $commitEnvelope->getPayload(), $payloadContract );
	}

	/**
	 * @param CarriesCommitData $commitEnvelope
	 *
	 * @return mixed
	 */
	private function getMetaDataFromCommitEnvelope( CarriesCommitData $commitEnvelope )
	{
		$metaDataContract = Contract::fromString( $commitEnvelope->getMetaDataContract() );

		return $this->unserializeDataWithContract( $commitEnvelope->getMetaData(), $metaDataContract );
	}

	/**
	 * @param ServesEventStreamData $eventEnvelope
	 *
	 * @return IdentifiesEventStream
	 */
	private function getStreamIdentifierForEventEnvelope( ServesEventStreamData $eventEnvelope )
	{
		return new EventStreamIdentifier( $eventEnvelope->getStreamId() );
	}

	/**
	 * @return Contract
	 */
	private function getPayloadContract()
	{
		return $this->serializationStrategy->getDefaultContract();
	}

	/**
	 * @return Contract
	 */
	private function getMetaDataContract()
	{
		return $this->serializationStrategy->getDefaultContract();
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
		return $this->serializationStrategy->getSerializerForContract( $contract );
	}
}
