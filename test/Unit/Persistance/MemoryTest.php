<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Persistance;

use hollodotme\MilestonES\CommitEnvelope;
use hollodotme\MilestonES\EventStreamIdentifier;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Persistence\Memory;
use hollodotme\MilestonES\Test\Unit\Fixtures\TestMemoryPersistenceWithInvalidRestoreFileDir;

class MemoryTest extends \PHPUnit_Framework_TestCase
{

	/** @var Memory */
	private $persistence;

	protected function setUp()
	{
		parent::setUp();

		$this->persistence = new Memory();
	}

	public function testIsNotInTransactionAfterConstruction()
	{
		$persistence = new Memory();

		$this->assertFalse( $persistence->isInTransaction() );
	}

	public function testBeginTransaction()
	{
		$this->persistence->beginTransaction();

		$this->assertTrue( $this->persistence->isInTransaction() );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\PersistenceHasStartedTransactionAlready
	 */
	public function testBeginTransactionFailsWhenTransacionIsAlreadyStarted()
	{
		$this->persistence->beginTransaction();
		$this->persistence->beginTransaction();
	}

	public function testCommitTransaction()
	{
		$this->persistence->beginTransaction();
		$this->persistence->commitTransaction();

		$this->assertFalse( $this->persistence->isInTransaction() );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\PersistenceHasNoTransactionStarted
	 */
	public function testCommitTransactionFailsWhenTransactionIsNotStarted()
	{
		$this->persistence->commitTransaction();
	}

	public function testRollbackTransaction()
	{
		$this->persistence->beginTransaction();
		$this->persistence->rollbackTransaction();

		$this->assertFalse( $this->persistence->isInTransaction() );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\PersistenceHasNoTransactionStarted
	 */
	public function testRollbackTransactionFailsWhenNoTransactionIsStarted()
	{
		$this->persistence->rollbackTransaction();
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\PersistenceHasNoTransactionStarted
	 */
	public function testCommitTransactionFailsWhenTransactionIsRolledBack()
	{
		$this->persistence->beginTransaction();
		$this->persistence->rollbackTransaction();
		$this->persistence->commitTransaction();
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\PersistenceHasNoTransactionStarted
	 */
	public function testRollbackTransactionFailsWhenTransactionIsCommitted()
	{
		$this->persistence->beginTransaction();
		$this->persistence->commitTransaction();
		$this->persistence->rollbackTransaction();
	}

	public function testPersistEventEnvelope()
	{
		$streamId = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );
		$envelope = new CommitEnvelope();
		$envelope->setStreamId( $streamId->getStreamId() );
		$envelope->setStreamIdContract( $streamId->getStreamIdContract() );

		$this->persistence->beginTransaction();

		$this->persistence->persistCommitEnvelope( $envelope );

		$this->persistence->commitTransaction();

		$commitedEnvelopes = $this->persistence->getEventStreamWithId( $streamId );

		$this->assertCount( 1, $commitedEnvelopes );
		$this->assertContainsOnlyInstancesOf(
			'\hollodotme\MilestonES\Interfaces\CarriesCommitData',
			$commitedEnvelopes
		);
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\PersistenceHasNoTransactionStarted
	 */
	public function testPersistEventEnvelopeFailsWhenNotInTransaction()
	{
		$stream_identifier = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );
		$envelope = new CommitEnvelope();
		$envelope->setStreamId( $stream_identifier->getStreamId() );
		$envelope->setStreamIdContract( $stream_identifier->getStreamIdContract() );

		$this->persistence->persistCommitEnvelope( $envelope );
	}

	public function testGetEventEnvelopesWithId()
	{
		$first_stream_identifier  = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID_1' ) );
		$second_stream_identifier = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID_X' ) );

		$envelope1 = new CommitEnvelope();
		$envelope1->setStreamId( $first_stream_identifier->getStreamId() );
		$envelope1->setStreamIdContract( $first_stream_identifier->getStreamIdContract() );

		$envelope2 = new CommitEnvelope();
		$envelope2->setStreamId( $second_stream_identifier->getStreamId() );
		$envelope2->setStreamIdContract( $second_stream_identifier->getStreamIdContract() );

		$envelope3 = new CommitEnvelope();
		$envelope3->setStreamId( $second_stream_identifier->getStreamId() );
		$envelope3->setStreamIdContract( $second_stream_identifier->getStreamIdContract() );

		$this->persistence->beginTransaction();

		$this->persistence->persistCommitEnvelope( $envelope1 );
		$this->persistence->persistCommitEnvelope( $envelope2 );
		$this->persistence->persistCommitEnvelope( $envelope3 );

		$this->persistence->commitTransaction();

		$envelopes_first_stream  = $this->persistence->getEventStreamWithId( $first_stream_identifier );
		$envelopes_second_stream = $this->persistence->getEventStreamWithId( $second_stream_identifier );

		$this->assertCount( 1, $envelopes_first_stream );
		$this->assertCount( 2, $envelopes_second_stream );
		$this->assertEquals( $envelope1, $envelopes_first_stream[0] );

		// check sort order
		$this->assertEquals( $envelope2, $envelopes_second_stream[0] );
		$this->assertEquals( $envelope3, $envelopes_second_stream[1] );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventStreamDoesNotExistForKey
	 */
	public function testGetEventEnvelopesWithIdFailsWhenStreamIdNotFound()
	{
		$stream_identifier = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );

		$this->persistence->getEventStreamWithId( $stream_identifier );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventStreamDoesNotExistForKey
	 */
	public function testGetEventEnvelopesWithIdFailsWhenEnvelopesAreNotCommitted()
	{
		$stream_identifier = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );
		$envelope = new CommitEnvelope();
		$envelope->setStreamId( $stream_identifier->getStreamId() );
		$envelope->setStreamIdContract( $stream_identifier->getStreamIdContract() );

		$this->persistence->beginTransaction();

		$this->persistence->persistCommitEnvelope( $envelope );

		$this->persistence->getEventStreamWithId( $stream_identifier );
	}

	public function testFilesWillBeRestoredOnReconstitution()
	{
		$stream_identifier = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );
		$envelope = new CommitEnvelope();
		$envelope->setStreamId( $stream_identifier->getStreamId() );
		$envelope->setStreamIdContract( $stream_identifier->getStreamIdContract() );
		$envelope->setFile( __DIR__ . '/../Fixtures/eventFileTest' );

		$this->persistence->beginTransaction();

		$this->persistence->persistCommitEnvelope( $envelope );

		$this->persistence->commitTransaction();

		$commited_envelopes = $this->persistence->getEventStreamWithId( $stream_identifier );

		$this->assertCount( 1, $commited_envelopes );

		/** @var CommitEnvelope $restored_envelope */
		$restored_envelope = $commited_envelopes[0];

		$this->assertNotEquals( $envelope->getFile(), $restored_envelope->getFile() );
		$this->assertEquals(
			file_get_contents( $envelope->getFile() ),
			file_get_contents( $restored_envelope->getFile() )
		);
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\RestoringFileWithContentFailed
	 */
	public function testRestoringFilesOnReconstituionFailsWhenRestoreDirDoesNotExist()
	{
		$stream_identifier = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );
		$envelope = new CommitEnvelope();
		$envelope->setStreamId( $stream_identifier->getStreamId() );
		$envelope->setStreamIdContract( $stream_identifier->getStreamIdContract() );
		$envelope->setFile( __DIR__ . '/../Fixtures/eventFileTest' );

		$persistence = new TestMemoryPersistenceWithInvalidRestoreFileDir();

		$persistence->beginTransaction();

		$persistence->persistCommitEnvelope( $envelope );

		$persistence->commitTransaction();

		$persistence->getEventStreamWithId( $stream_identifier );
	}
}
