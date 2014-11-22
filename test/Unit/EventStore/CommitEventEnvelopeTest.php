<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Test\Unit\EventStore;

use hollodotme\MilestonES\CommitEventEnvelope;

class CommitEventEnvelopeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider recordProvider
	 */
	public function testFromRecord( array $record )
	{
		$envelope = CommitEventEnvelope::fromRecord( $record );

		$this->assertEquals( $record['id'], $envelope->getId() );
		$this->assertEquals( $record['commit_id'], $envelope->getCommitId() );
		$this->assertEquals( $record['stream_id'], $envelope->getStreamId() );
		$this->assertEquals( $record['stream_id_contract'], $envelope->getStreamIdContract() );
		$this->assertEquals( $record['payload'], $envelope->getPayload() );
		$this->assertEquals( $record['payload_contract'], $envelope->getPayloadContract() );
		$this->assertEquals( $record['meta_data'], $envelope->getMetaData() );
		$this->assertEquals( $record['meta_data_contract'], $envelope->getMetaDataContract() );
		$this->assertEquals(
			\DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $record['occurred_on'] ),
			$envelope->getOccurredOn()
		);
		$this->assertEquals(
			\DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $record['committed_on'] ),
			$envelope->getCommittedOn()
		);
	}

	public function recordProvider()
	{
		return [
			[
				[
					'id'                 => '1',
					'commit_id'          => 'unit-test-commit-id',
					'stream_id'          => 'unit-test-stream-id',
					'stream_id_contract' => 'hollodotme.MilestonES.Identifier',
					'payload'            => json_encode( [ 'description' => 'Unit-Test' ] ),
					'payload_contract'   => 'hollodotme.MilestonES.Serializers.PhpSerializer',
					'meta_data'          => json_encode( [ 'creator' => 'Tester' ] ),
					'meta_data_contract' => 'hollodotme.MilestonES.Serializers.PhpSerializer',
					'file' => '/tmp/Unit_Test_123456',
					'occurred_on'        => '2014-11-17 14:01:02',
					'committed_on'       => '2014-11-17 14:01:12',
				],
			]
		];
	}
}
 