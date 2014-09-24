<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

/**
 * Class TestEnum
 * @package hollodotme\MilestonES\Test\Unit
 */
class TestEnum extends Enum
{
	const STRING_TEST = 'Unit.Test';

	const INT_TEST = 12345;

	const FLOAT_TEST = 123.45;

	const FALSE_TEST = false;

	const TRUE_TEST = true;

	const NULL_TEST = null;

	public function getDefaultValue()
	{
		return self::NULL_TEST;
	}
}