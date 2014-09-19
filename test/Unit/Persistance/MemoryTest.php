<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Persistance;

use hollodotme\MilestonES\CommitEventEnvelope;
use hollodotme\MilestonES\EventStreamIdentifier;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Persistence\Memory;

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
		$stream_identifier = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );
		$envelope          = new CommitEventEnvelope();
		$envelope->setStreamId( $stream_identifier->getStreamId() );
		$envelope->setStreamIdContract( $stream_identifier->getStreamIdContract() );

		$this->persistence->beginTransaction();

		$this->persistence->persistEventEnvelope( $envelope );

		$this->persistence->commitTransaction();

		$commited_envelopes = $this->persistence->getEventEnvelopesWithId( $stream_identifier );

		$this->assertCount( 1, $commited_envelopes );
		$this->assertContainsOnlyInstancesOf(
			'\hollodotme\MilestonES\Interfaces\WrapsEventForCommit',
			$commited_envelopes
		);
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\PersistenceHasNoTransactionStarted
	 */
	public function testPersistEventEnvelopeFailsWhenNotInTransaction()
	{
		$stream_identifier = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );
		$envelope          = new CommitEventEnvelope();
		$envelope->setStreamId( $stream_identifier->getStreamId() );
		$envelope->setStreamIdContract( $stream_identifier->getStreamIdContract() );

		$this->persistence->persistEventEnvelope( $envelope );
	}

	public function testGetEventEnvelopesWithId()
	{
		$first_stream_identifier  = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID_1' ) );
		$second_stream_identifier = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID_X' ) );

		$envelope1 = new CommitEventEnvelope();
		$envelope1->setStreamId( $first_stream_identifier->getStreamId() );
		$envelope1->setStreamIdContract( $first_stream_identifier->getStreamIdContract() );

		$envelope2 = new CommitEventEnvelope();
		$envelope2->setStreamId( $second_stream_identifier->getStreamId() );
		$envelope2->setStreamIdContract( $second_stream_identifier->getStreamIdContract() );

		$envelope3 = new CommitEventEnvelope();
		$envelope3->setStreamId( $second_stream_identifier->getStreamId() );
		$envelope3->setStreamIdContract( $second_stream_identifier->getStreamIdContract() );

		$this->persistence->beginTransaction();

		$this->persistence->persistEventEnvelope( $envelope1 );
		$this->persistence->persistEventEnvelope( $envelope2 );
		$this->persistence->persistEventEnvelope( $envelope3 );

		$this->persistence->commitTransaction();

		$envelopes_first_stream  = $this->persistence->getEventEnvelopesWithId( $first_stream_identifier );
		$envelopes_second_stream = $this->persistence->getEventEnvelopesWithId( $second_stream_identifier );

		$this->assertCount( 1, $envelopes_first_stream );
		$this->assertCount( 2, $envelopes_second_stream );
		$this->assertSame( $envelope1, $envelopes_first_stream[0] );

		// check sort order
		$this->assertSame( $envelope2, $envelopes_second_stream[0] );
		$this->assertSame( $envelope3, $envelopes_second_stream[1] );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventStreamDoesNotExistForKey
	 */
	public function testGetEventEnvelopesWithIdFailsWhenStreamIdNotFound()
	{
		$stream_identifier = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );

		$this->persistence->getEventEnvelopesWithId( $stream_identifier );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventStreamDoesNotExistForKey
	 */
	public function testGetEventEnvelopesWithIdFailsWhenEnvelopesAreNotCommitted()
	{
		$stream_identifier = new EventStreamIdentifier( new Identifier( 'Unit\\Test\\ID' ) );
		$envelope          = new CommitEventEnvelope();
		$envelope->setStreamId( $stream_identifier->getStreamId() );
		$envelope->setStreamIdContract( $stream_identifier->getStreamIdContract() );

		$this->persistence->beginTransaction();

		$this->persistence->persistEventEnvelope( $envelope );

		$this->persistence->getEventEnvelopesWithId( $stream_identifier );
	}
}
