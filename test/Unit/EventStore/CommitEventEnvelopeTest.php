<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Test\Unit\EventStore;

use hollodotme\MilestonES\CommitEnvelope;
use hollodotme\MilestonES\Interfaces\ServesCommitData;

class CommitEventEnvelopeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param array $record
	 *
	 * @dataProvider commitDataProvider
	 */
	public function testFromRecord( array $record )
	{
		$commitData = $this->getMock( ServesCommitData::class );
		$commitData->expects( $this->once() )->method( 'getId' )->willReturn( $record['id'] );
		$commitData->expects( $this->once() )->method( 'getCommitId' )->willReturn( $record['commit_id'] );
		$commitData->expects( $this->once() )->method( 'getStreamId' )->willReturn( $record['stream_id'] );
		$commitData->expects( $this->once() )->method( 'getStreamIdContract' )->willReturn(
			$record['stream_id_contract']
		);
		$commitData->expects( $this->once() )->method( 'getPayload' )->willReturn( $record['payload'] );
		$commitData->expects( $this->once() )->method( 'getPayloadContract' )->willReturn(
			$record['payload_contract']
		);
		$commitData->expects( $this->once() )->method( 'getMetaData' )->willReturn( $record['meta_data'] );
		$commitData->expects( $this->once() )->method( 'getMetaDataContract' )->willReturn(
			$record['meta_data_contract']
		);
		$commitData->expects( $this->once() )->method( 'getOccurredOn' )->willReturn( $record['occurred_on'] );
		$commitData->expects( $this->once() )->method( 'getCommittedOn' )->willReturn( $record['committed_on'] );

		/** @var ServesCommitData $commitData */
		$envelope = CommitEnvelope::fromCommitData( $commitData );

		$this->assertEquals( $record['id'], $envelope->getId() );
		$this->assertEquals( $record['commit_id'], $envelope->getCommitId() );
		$this->assertEquals( $record['stream_id'], $envelope->getStreamId() );
		$this->assertEquals( $record['stream_id_contract'], $envelope->getStreamIdContract() );
		$this->assertEquals( $record['payload'], $envelope->getPayload() );
		$this->assertEquals( $record['payload_contract'], $envelope->getPayloadContract() );
		$this->assertEquals( $record['meta_data'], $envelope->getMetaData() );
		$this->assertEquals( $record['meta_data_contract'], $envelope->getMetaDataContract() );
		$this->assertEquals( $record['occurred_on'], $envelope->getOccurredOn() );
		$this->assertEquals( $record['committed_on'], $envelope->getCommittedOn() );
	}

	public function commitDataProvider()
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
					'file'               => '/tmp/Unit_Test_123456',
					'occurred_on'  => new \DateTimeImmutable( '2014-11-17 14:01:02' ),
					'committed_on' => new \DateTimeImmutable( '2014-11-17 14:01:12' ),
				],
			]
		];
	}
}
