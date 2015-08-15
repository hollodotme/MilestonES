<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\ApplicationStateStore;

use hollodotme\MilestonES\Commit;
use hollodotme\MilestonES\CommitId;
use hollodotme\MilestonES\Contract;
use hollodotme\MilestonES\EventEnvelope;
use hollodotme\MilestonES\EventEnvelopeMapper;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\SerializationStrategy;
use hollodotme\MilestonES\SerializerRegistry;
use hollodotme\MilestonES\Serializers\PhpSerializer;
use hollodotme\MilestonES\Test\Unit\Fixtures\UnitTestEvent;

require_once __DIR__ . '/../Fixtures/UnitTestEvent.php';

class EventEnvelopeMapperTest extends \PHPUnit_Framework_TestCase
{

	const TEST_EVENT_OCCURANCE_TIMESTAMP = '2014-09-19 11:40:00';

	const TEST_COMMIT_TIMESTAMP          = '2014-09-19 11:45:00';

	private $serialization_strategy;

	/** @var Commit */
	private $commit;

	public function setUp()
	{
		$serializer_registry = new SerializerRegistry();
		$serializer_registry->registerSerializerForContract(
			new Contract( PhpSerializer::class ),
			new PhpSerializer()
		);

		$this->serialization_strategy = new SerializationStrategy(
			$serializer_registry,
			new Contract( PhpSerializer::class )
		);

		$this->commit = new Commit(
			CommitId::generate(),
			new \DateTimeImmutable( self::TEST_COMMIT_TIMESTAMP )
		);
	}

	public function testPutEventInEnvelopeForCommit()
	{
		$mapper = new EventEnvelopeMapper( $this->serialization_strategy );

		$event_envelope = $this->getTestEventEnvelope();

		$commit_envelope = $mapper->createCommitEnvelope( $event_envelope, $this->commit );

		$this->assertNull( $commit_envelope->getId() );

		$this->assertEquals( $this->commit->getCommitId(), $commit_envelope->getCommitId() );
		$this->assertNotInstanceOf(
			'\\hollodotme\\MilestonES\\Interfaces\\IdentifiesObject', $commit_envelope->getCommitId()
		);
		$this->assertInternalType( 'string', $commit_envelope->getCommitId() );

		$this->assertSame( $this->commit->getCommittedOn(), $commit_envelope->getCommittedOn() );
		$this->assertSame( $event_envelope->getOccurredOn(), $commit_envelope->getOccurredOn() );

		$this->assertEquals( $event_envelope->getStreamId(), $commit_envelope->getStreamId() );
		$this->assertNotInstanceOf(
			'\\hollodotme\\MilestonES\\Interfaces\\IdentifiesObject', $commit_envelope->getStreamId()
		);
		$this->assertInternalType( 'string', $commit_envelope->getStreamId() );

		$this->assertEquals(
			new Contract( get_class( $event_envelope->getStreamId() ) ), $commit_envelope->getStreamIdContract()
		);
		$this->assertNotInstanceOf(
			'\\hollodotme\\MilestonES\\Interfaces\\IdentifiesObject',
			$commit_envelope->getStreamIdContract()
		);
		$this->assertInternalType( 'string', $commit_envelope->getStreamIdContract() );

		$this->assertEquals( new Contract( PhpSerializer::class ), $commit_envelope->getPayloadContract() );
		$this->assertNotInstanceOf(
			'\\hollodotme\\MilestonES\\Interfaces\\IdentifiesObject',
			$commit_envelope->getPayloadContract()
		);
		$this->assertInternalType( 'string', $commit_envelope->getPayloadContract() );

		$this->assertInternalType( 'string', $commit_envelope->getPayload() );
		$this->assertEquals( serialize( $event_envelope->getPayload() ), $commit_envelope->getPayload() );

		$this->assertEquals( new Contract( PhpSerializer::class ), $commit_envelope->getMetaDataContract() );
		$this->assertNotInstanceOf(
			'\\hollodotme\\MilestonES\\Interfaces\\IdentifiesObject',
			$commit_envelope->getMetaDataContract()
		);
		$this->assertInternalType( 'string', $commit_envelope->getMetaDataContract() );

		$this->assertInternalType( 'string', $commit_envelope->getMetaData() );
		$this->assertEquals( serialize( $event_envelope->getMetaData() ), $commit_envelope->getMetaData() );
	}

	public function testExtractEventFromEnvelope()
	{
		$mapper          = new EventEnvelopeMapper( $this->serialization_strategy );
		$event_envelope  = $this->getTestEventEnvelope();
		$commit_envelope = $mapper->createCommitEnvelope( $event_envelope, $this->commit );

		$extracted_envelopes = $mapper->extractEventEnvelopesFromCommitEnvelopes( [ $commit_envelope ] );

		/** @var EventEnvelope $extracted_envelope */
		$extracted_envelope = $extracted_envelopes[0];

		/** @var UnitTestEvent $extracted_event */
		$extracted_event = $extracted_envelope->getPayload();

		$this->assertCount( 1, $extracted_envelopes );

		$this->assertInstanceOf( UnitTestEvent::class, $extracted_envelope->getPayload() );

		$this->assertEquals( $event_envelope->getStreamId(), $extracted_envelope->getStreamId() );
		$this->assertTrue( $event_envelope->getStreamId()->equals( $extracted_envelope->getStreamId() ) );
		$this->assertInstanceOf( get_class( $event_envelope->getStreamId() ), $extracted_envelope->getStreamId() );

		$this->assertEquals( 'Unit test event', $extracted_event->getDescription() );

		$this->assertInstanceOf( \DateTimeImmutable::class, $extracted_envelope->getOccurredOn() );
	}

	/**
	 * @return EventEnvelope
	 */
	private function getTestEventEnvelope()
	{
		$event          = new UnitTestEvent( new Identifier( 'Unit-Test-ID' ), 'Unit test event' );
		$event_envelope = new EventEnvelope( $event, [ ] );

		return $event_envelope;
	}
}
