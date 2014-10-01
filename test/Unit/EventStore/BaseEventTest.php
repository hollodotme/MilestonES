<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit\EventStore;

require_once __DIR__ . '/../_test_classes/TestBaseEvent.php';

use hollodotme\MilestonES\Identifier;
use hollodotme\MilestonES\Interfaces\ActsAsRole;
use hollodotme\MilestonES\Test\Unit\TestBaseEvent;

class BaseEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanKeepARole()
	{
		$role = $this->getMock( ActsAsRole::class );
		$role->expects( $this->any() )->method( 'getIdentifier' )->willReturn( new Identifier( 'Unit-Test-Role-ID' ) );

		$event = new TestBaseEvent( new Identifier( 'Unit-Test-ID' ) );

		$event->setRole( $role );

		$this->assertSame( $role, $event->getRole() );
		$this->assertTrue( $event->getRole()->getIdentifier()->equals( new Identifier( 'Unit-Test-Role-ID' ) ) );
		$this->assertTrue( $event->hasRole() );
		$this->assertTrue( $event->hasRoleWithId( new Identifier( 'Unit-Test-Role-ID' ) ) );
	}

	public function testCanBeRoleless()
	{
		$event = new TestBaseEvent( new Identifier( 'Unit-Test-ID' ) );

		$this->assertFalse( $event->hasRole() );
		$this->assertFalse( $event->hasRoleWithId( new Identifier( 'Unit-Test-Role-ID' ) ) );
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventHasNoRole
	 */
	public function testGetRoleFailsWhenNoRoleAdded()
	{
		$event = new TestBaseEvent( new Identifier( 'Unit-Test-ID' ) );

		$event->getRole();
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventHasNoRole
	 */
	public function testRemoveRoleFailsWhenNoRoleAdded()
	{
		$event = new TestBaseEvent( new Identifier( 'Unit-Test-ID' ) );

		$event->removeRole();
	}

	/**
	 * @expectedException \hollodotme\MilestonES\Exceptions\EventHasARoleAlready
	 */
	public function testSetRoleFailsWhenEventHasARoleAlready()
	{
		$role = $this->getMock( ActsAsRole::class );
		$role->expects( $this->any() )->method( 'getIdentifier' )->willReturn( new Identifier( 'Unit-Test-Role-ID' ) );

		$override_role = $this->getMock( ActsAsRole::class );
		$override_role->expects( $this->any() )->method( 'getIdentifier' )->willReturn( new Identifier( 'Unit-Test-Override-Role-ID' ) );

		$event = new TestBaseEvent( new Identifier( 'Unit-Test-ID' ) );

		$event->setRole( $role );
		$event->setRole( $override_role );
	}

	public function testCanRemoveRole()
	{
		$role = $this->getMock( ActsAsRole::class );
		$role->expects( $this->any() )->method( 'getIdentifier' )->willReturn( new Identifier( 'Unit-Test-Role-ID' ) );

		$event = new TestBaseEvent( new Identifier( 'Unit-Test-ID' ) );

		$event->setRole( $role );

		$this->assertTrue( $event->hasRole() );

		$event->removeRole();

		$this->assertFalse( $event->hasRole() );
	}

	public function testCanCheckForRoleId()
	{
		$role = $this->getMock( ActsAsRole::class );
		$role->expects( $this->any() )->method( 'getIdentifier' )->willReturn( new Identifier( 'Unit-Test-Role-ID' ) );

		$event = new TestBaseEvent( new Identifier( 'Unit-Test-ID' ) );

		$event->setRole( $role );

		$this->assertTrue( $event->hasRoleWithId( new Identifier( 'Unit-Test-Role-ID' ) ) );
		$this->assertFalse( $event->hasRoleWithId( new Identifier( 'Unit-Test-Unadded-Role-ID' ) ) );
	}
}
 