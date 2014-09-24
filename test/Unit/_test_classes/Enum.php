<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES\Test\Unit;

/**
 * Class Enum
 * @package hollodotme\MilestonES\Test\Unit
 */
abstract class Enum
{

	private $value;

	final public function __construct( $value = null )
	{
		$value = $this->getDefaultValueIfNeccessary( $value );

		$this->guardValidValue( $value );

		$this->value = $value;
	}

	private function getDefaultValueIfNeccessary( $value )
	{
		if ( is_null( $value ) )
		{
			$value = $this->getDefaultValue();
		}

		return $value;
	}

	private function guardValidValue( $value )
	{
		$this->guardValueIsScalar( $value );
		$this->guardValueIsInConstants( $value );
	}

	private function guardValueIsScalar( $value )
	{
		if ( !is_scalar( $value ) && !is_null( $value ) )
		{
			throw new \InvalidArgumentException( 'Value is not scalar or null: ' . gettype( $value ) );
		}
	}

	private function guardValueIsInConstants( $value )
	{
		if ( !in_array( $value, $this->getValues(), true ) )
		{
			throw new \InvalidArgumentException( $value . ' is not defined as class constant of enum ' . get_class( $this ) );
		}
	}

	abstract public function getDefaultValue();

	/**
	 * @return array
	 */
	final public function getValues()
	{
		$ref_class     = new \ReflectionClass( $this );
		$ref_constants = $ref_class->getConstants();

		return array_values( $ref_constants );
	}

	final public function __toString()
	{
		return strval( $this->value );
	}

	final public function equals( $other_value )
	{
		return ($this->value === $other_value);
	}
}