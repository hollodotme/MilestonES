<?php
/**
 *
 * @author hollodotme
 */

namespace hollodotme\MilestonES\Test\Unit\Identifiers;

use hollodotme\MilestonES\Contract;

class ContractTest extends \PHPUnit_Framework_TestCase
{
	public function testObjectsCanBeUsedAsId()
	{
		$contract = new Contract( new \stdClass() );

		$this->assertEquals( 'stdClass', $contract->toString() );
		$this->assertEquals( 'stdClass', (string)$contract );
	}
}
 