<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\EventStore;

use hollodotme\MilestonES\EventCollection;
use hollodotme\MilestonES\EventStore;
use hollodotme\MilestonES\EventStoreConfigDelegate;
use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Test\Unit\TestAggregateWasDescribed;

require_once __DIR__ . '/../_test_classes/TestEventObserver.php';
require_once __DIR__ . '/../_test_classes/TestAggregateWasDescribed.php';
require_once __DIR__ . '/../_test_classes/TestAggregateWasDeleted.php';

class EventStoreTest extends \PHPUnit_Framework_TestCase
{

	private $config_delegate;

	public function setUp()
	{
		$this->config_delegate = new EventStoreConfigDelegate();
	}

	public function testCanCommitEventsAndPublishThemToObservers()
	{
		$event_store = new EventStore( $this->config_delegate );

		$identifier = new Identifier( 'Unit-Test-ID' );
		$event      = new TestAggregateWasDescribed( $identifier );
		$event->setDescription( 'Unit-Test' );

		$collection   = new EventCollection();
		$collection[] = $event;

		/** @var EventCollection $collection */
		$event_store->commitEvents( $collection );
	}
}
 