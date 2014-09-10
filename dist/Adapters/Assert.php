<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Adapters;

use Assert as BeberleiAssert;

/**
 * Class Assert
 * @package hollodotme\MilestonES\Adapters
 */
abstract class Assert
{
	/**
	 * @param mixed       $value
	 * @param null|string $default_message
	 * @param null|string $default_property_path
	 *
	 * @return \Assert\AssertionChain
	 */
	public static function that( $value, $default_message = null, $default_property_path = null )
	{
		return BeberleiAssert\that( $value, $default_message, $default_property_path );
	}

	/**
	 * @param mixed       $value
	 * @param null|string $default_message
	 * @param null|string $default_property_path
	 *
	 * @return \Assert\AssertionChain
	 */
	public static function thatNullOr( $value, $default_message = null, $default_property_path = null )
	{
		return BeberleiAssert\thatNullOr( $value, $default_message, $default_property_path );
	}

	/**
	 * @param array       $values
	 * @param null|string $default_message
	 * @param null|string $default_property_path
	 *
	 * @return \Assert\AssertionChain
	 */
	public static function thatAll( array $values, $default_message = null, $default_property_path = null )
	{
		return BeberleiAssert\thatAll( $values, $default_message, $default_property_path );
	}

	/**
	 * @return \Assert\LazyAssertion
	 */
	public static function lazy()
	{
		return BeberleiAssert\lazy();
	}
} 