<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\Aggregates;

use hollodotme\MilestonES\Events\AggregateRootWasAllocated;
use hollodotme\MilestonES\EventStream;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Test\Unit\TestAggregateRoot;
use hollodotme\MilestonES\Test\Unit\TestAggregateWasDescribed;

require_once __DIR__ . '/../_test_classes/TestAggregateRoot.php';

class AggregateRootTest extends \PHPUnit_Framework_TestCase
{
	public function testAllocationWithIdTriggersAndAppliesChange()
	{
		$identifier     = new Identifier( 'Unit-Test-ID' );
		$aggregate_root = TestAggregateRoot::allocateWithId( $identifier );

		$this->assertInstanceOf( TestAggregateRoot::class, $aggregate_root );
		$this->assertTrue( $aggregate_root->hasChanges() );
		$this->assertCount( 1, $aggregate_root->getChanges() );
		$this->assertSame( $identifier, $aggregate_root->getIdentifier() );
	}

	public function testEventsIncreaseVersionOfAggregateRoot()
	{
		$identifier     = new Identifier( 'Unit-Test-ID' );
		$aggregate_root = TestAggregateRoot::allocateWithId( $identifier );

		$this->assertEquals( 1, $aggregate_root->getVersion() );

		$aggregate_root->describe();

		$this->assertEquals( 2, $aggregate_root->getVersion() );
		$this->assertEquals( 'Unit-Test', $aggregate_root->getDescription() );
	}

	public function testCanBeAllocatedWithEventStream()
	{
		$identifier  = new Identifier( 'Unit-Test-ID' );
		$alloc_event = new AggregateRootWasAllocated( $identifier );
		$alloc_event->setVersion( 1 );

		$test_event = new TestAggregateWasDescribed( $identifier );
		$test_event->setVersion( 2 );
		$test_event->setDescription( 'Unit-Test' );

		$stream = new EventStream( [$alloc_event, $test_event] );

		$aggregate_root = TestAggregateRoot::allocateWithEventStream( $stream );

		$this->assertSame( $identifier, $aggregate_root->getIdentifier() );
		$this->assertEquals( 2, $aggregate_root->getVersion() );
		$this->assertEquals( 'Unit-Test', $aggregate_root->getDescription() );
		$this->assertFalse( $aggregate_root->hasChanges() );
	}

	public function testChangesCanBeCleared()
	{
		$identifier     = new Identifier( 'Unit-Test-ID' );
		$aggregate_root = TestAggregateRoot::allocateWithId( $identifier );

		$this->assertCount( 1, $aggregate_root->getChanges() );
		$this->assertTrue( $aggregate_root->hasChanges() );

		$aggregate_root->clearChanges();

		$this->assertCount( 0, $aggregate_root->getChanges() );
		$this->assertFalse( $aggregate_root->hasChanges() );
	}
}
