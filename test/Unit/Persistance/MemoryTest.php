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
		$eventStreamId = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );
		$envelope      = new CommitEnvelope();
		$envelope->setStreamId( $eventStreamId->getStreamId() );
		$envelope->setStreamIdContract( $eventStreamId->getStreamIdContract() );

		$this->persistence->beginTransaction();

		$this->persistence->persistCommitEnvelope( $envelope );

		$this->persistence->commitTransaction();

		$commitedEnvelopes = $this->persistence->getEventStreamWithId( $eventStreamId, 0 );

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
		$firstEventStreamId  = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID_1' ) );
		$secondEventStreamId = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID_X' ) );

		$envelope1 = new CommitEnvelope();
		$envelope1->setStreamId( $firstEventStreamId->getStreamId() );
		$envelope1->setStreamIdContract( $firstEventStreamId->getStreamIdContract() );

		$envelope2 = new CommitEnvelope();
		$envelope2->setStreamId( $secondEventStreamId->getStreamId() );
		$envelope2->setStreamIdContract( $secondEventStreamId->getStreamIdContract() );

		$envelope3 = new CommitEnvelope();
		$envelope3->setStreamId( $secondEventStreamId->getStreamId() );
		$envelope3->setStreamIdContract( $secondEventStreamId->getStreamIdContract() );

		$this->persistence->beginTransaction();

		$this->persistence->persistCommitEnvelope( $envelope1 );
		$this->persistence->persistCommitEnvelope( $envelope2 );
		$this->persistence->persistCommitEnvelope( $envelope3 );

		$this->persistence->commitTransaction();

		$envelopesFirstStream  = $this->persistence->getEventStreamWithId( $firstEventStreamId, 0 );
		$envelopesSecondStream = $this->persistence->getEventStreamWithId( $secondEventStreamId, 0 );

		$this->assertCount( 1, $envelopesFirstStream );
		$this->assertCount( 2, $envelopesSecondStream );
		$this->assertEquals( $envelope1, $envelopesFirstStream[0] );

		// check sort order
		$this->assertEquals( $envelope2, $envelopesSecondStream[0] );
		$this->assertEquals( $envelope3, $envelopesSecondStream[1] );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventStreamDoesNotExistForKey
	 */
	public function testGetEventEnvelopesWithIdFailsWhenStreamIdNotFound()
	{
		$eventStreamId = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );

		$this->persistence->getEventStreamWithId( $eventStreamId, 0 );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventStreamDoesNotExistForKey
	 */
	public function testGetEventEnvelopesWithIdFailsWhenEnvelopesAreNotCommitted()
	{
		$eventStreamId = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );
		$envelope      = new CommitEnvelope();
		$envelope->setStreamId( $eventStreamId->getStreamId() );
		$envelope->setStreamIdContract( $eventStreamId->getStreamIdContract() );

		$this->persistence->beginTransaction();

		$this->persistence->persistCommitEnvelope( $envelope );

		$this->persistence->getEventStreamWithId( $eventStreamId, 0 );
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

		$commited_envelopes = $this->persistence->getEventStreamWithId( $stream_identifier, 0 );

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
		$eventStreamId = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );
		$envelope      = new CommitEnvelope();
		$envelope->setStreamId( $eventStreamId->getStreamId() );
		$envelope->setStreamIdContract( $eventStreamId->getStreamIdContract() );
		$envelope->setFile( __DIR__ . '/../Fixtures/eventFileTest' );

		$persistence = new TestMemoryPersistenceWithInvalidRestoreFileDir();

		$persistence->beginTransaction();

		$persistence->persistCommitEnvelope( $envelope );

		$persistence->commitTransaction();

		$persistence->getEventStreamWithId( $eventStreamId, 0 );
	}
}
