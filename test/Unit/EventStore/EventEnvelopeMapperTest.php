<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\EventStore;

use hollodotme\MilestonES\Commit;
use hollodotme\MilestonES\CommitId;
use hollodotme\MilestonES\Contract;
use hollodotme\MilestonES\EventEnvelopeMapper;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\SerializationStrategy;
use hollodotme\MilestonES\SerializerRegistry;
use hollodotme\MilestonES\Serializers\JsonSerializer;
use hollodotme\MilestonES\Test\Unit\TestAggregateWasDescribed;

require_once __DIR__ . '/../_test_classes/TestAggregateWasDescribed.php';

class EventEnvelopeMapperTest extends \PHPUnit_Framework_TestCase
{
	const TEST_EVENT_OCCURANCE_TIMESTAMP = '2014-09-19 11:40:00';

	const TEST_COMMIT_TIMESTAMP = '2014-09-19 11:45:00';

	private $serialization_strategy;

	/** @var Commit */
	private $commit;

	public function setUp()
	{
		$serializer_registry = new SerializerRegistry();
		$serializer_registry->registerSerializerForContract(
			new Contract( JsonSerializer::class ),
			new JsonSerializer()
		);

		$this->serialization_strategy = new SerializationStrategy( $serializer_registry, new Contract( JsonSerializer::class ) );

		$this->commit = new Commit( CommitId::generate(), new \DateTime( self::TEST_COMMIT_TIMESTAMP ) );
	}

	public function testPutEventInEnvelopeForCommit()
	{
		$mapper = new EventEnvelopeMapper( $this->serialization_strategy );

		$event = $this->getTestEvent();

		$envelope = $mapper->putEventInEnvelopeForCommit( $event, $this->commit );

		$this->assertNull( $envelope->getId() );

		$this->assertEquals( $this->commit->getId(), $envelope->getCommitId() );
		$this->assertNotInstanceOf( '\\hollodotme\\MilestonES\\Interfaces\\Identifies', $envelope->getCommitId() );
		$this->assertInternalType( 'string', $envelope->getCommitId() );

		$this->assertEquals( $this->commit->getDateTime(), $envelope->getCommittedOn() );

		$this->assertEquals( 134, $envelope->getVersion() );

		$this->assertEquals( $event->getOccuredOn(), $envelope->getOccuredOn() );

		$this->assertEquals( $event->getStreamId(), $envelope->getStreamId() );
		$this->assertNotInstanceOf( '\\hollodotme\\MilestonES\\Interfaces\\Identifies', $envelope->getStreamId() );
		$this->assertInternalType( 'string', $envelope->getStreamId() );

		$this->assertEquals( new Contract( get_class( $event->getStreamId() ) ), $envelope->getStreamIdContract() );
		$this->assertNotInstanceOf( '\\hollodotme\\MilestonES\\Interfaces\\Identifies', $envelope->getStreamIdContract() );
		$this->assertInternalType( 'string', $envelope->getStreamIdContract() );

		$this->assertEquals( $event->getContract(), $envelope->getEventContract() );
		$this->assertNotInstanceOf( '\\hollodotme\\MilestonES\\Interfaces\\Identifies', $envelope->getEventContract() );
		$this->assertInternalType( 'string', $envelope->getEventContract() );

		$this->assertEquals( new Contract( JsonSerializer::class ), $envelope->getPayloadContract() );
		$this->assertNotInstanceOf( '\\hollodotme\\MilestonES\\Interfaces\\Identifies', $envelope->getPayloadContract() );
		$this->assertInternalType( 'string', $envelope->getPayloadContract() );

		$this->assertInternalType( 'string', $envelope->getPayload() );
		$this->assertJsonStringEqualsJsonString( '{"description":"Unit test event"}', $envelope->getPayload() );

		$this->assertEquals( new Contract( JsonSerializer::class ), $envelope->getMetaDataContract() );
		$this->assertNotInstanceOf( '\\hollodotme\\MilestonES\\Interfaces\\Identifies', $envelope->getMetaDataContract() );
		$this->assertInternalType( 'string', $envelope->getMetaDataContract() );

		$this->assertInternalType( 'string', $envelope->getMetaData() );
		$this->assertJsonStringEqualsJsonString( '{"creator":"Unit test creator"}', $envelope->getMetaData() );
	}

	public function testExtractEventFromEnvelope()
	{
		$mapper   = new EventEnvelopeMapper( $this->serialization_strategy );
		$event    = $this->getTestEvent();
		$envelope = $mapper->putEventInEnvelopeForCommit( $event, $this->commit );

		/** @var TestAggregateWasDescribed $extracted_event */
		$extracted_event = $mapper->extractEventFromEnvelope( $envelope );

		$this->assertInstanceOf( TestAggregateWasDescribed::class, $extracted_event );

		$this->assertEquals( $event->getStreamId(), $extracted_event->getStreamId() );
		$this->assertTrue( $event->getStreamId()->equals( $extracted_event->getStreamId() ) );
		$this->assertInstanceOf( get_class( $event->getStreamId() ), $extracted_event->getStreamId() );

		$this->assertEquals( 'Unit test creator', $extracted_event->getCreator() );
		$this->assertEquals( 'Unit test event', $extracted_event->getDescription() );

		$this->assertEquals( new \DateTime( self::TEST_EVENT_OCCURANCE_TIMESTAMP ), $extracted_event->getOccuredOn() );

		$this->assertEquals( 134, $extracted_event->getVersion() );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventClassDoesNotExist
	 */
	public function testExtractEventFromEnvelopeFailsWhenEventClassDoesNotExist()
	{
		$mapper   = new EventEnvelopeMapper( $this->serialization_strategy );
		$event    = $this->getTestEvent();
		$envelope = $mapper->putEventInEnvelopeForCommit( $event, $this->commit );

		$envelope->setEventContract( new Contract( 'Some\\Invalid\\Class\\Name' ) );

		$mapper->extractEventFromEnvelope( $envelope );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\IdentifierClassDoesNotExist
	 */
	public function testExtractEventFromEnvelopeFailsWhenStreamIdClassDoesNotExist()
	{
		$mapper   = new EventEnvelopeMapper( $this->serialization_strategy );
		$event    = $this->getTestEvent();
		$envelope = $mapper->putEventInEnvelopeForCommit( $event, $this->commit );

		$envelope->setStreamIdContract( new Contract( 'Some\\Invalid\\Class\\Name' ) );

		$mapper->extractEventFromEnvelope( $envelope );
	}

	private function getTestEvent()
	{
		$event = new TestAggregateWasDescribed( new Identifier( 'Unit\\Test\\ID' ) );
		$event->setVersion( 134 );
		$event->setOccuredOn( new \DateTime( self::TEST_EVENT_OCCURANCE_TIMESTAMP ) );
		$event->setCreator( 'Unit test creator' );
		$event->setDescription( 'Unit test event' );

		return $event;
	}
}
 