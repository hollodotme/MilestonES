<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\DefaultSerializationContractIsNotRegistered;
use hollodotme\MilestonES\Interfaces\Identifies;

/**
 * Class SerializationConfig
 * @package hollodotme\MilestonES
 */
class SerializationConfig
{

	/** @var SerializerRegistry */
	private $registry;

	/** @var Identifies */
	private $default_contract;

	/**
	 * @param SerializerRegistry $registry
	 * @param Identifies         $default_contract
	 *
	 * @throws DefaultSerializationContractIsNotRegistered
	 */
	public function __construct( SerializerRegistry $registry, Identifies $default_contract )
	{
		$this->registry         = $registry;
		$this->default_contract = $default_contract;

		$this->guardDefaultContractIsRegistered();
	}

	/**
	 * @throws Exceptions\SerializationContractIsNotRegistered
	 * @return Interfaces\SerializesData
	 */
	public function getDefaultSerializer()
	{
		return $this->registry->getSerializerForContract( $this->default_contract );
	}

	/**
	 * @param Identifies $contract
	 *
	 * @throws Exceptions\SerializationContractIsNotRegistered
	 * @return Interfaces\SerializesData
	 */
	public function getSerializerForContract( Identifies $contract )
	{
		return $this->registry->getSerializerForContract( $contract );
	}

	/**
	 * @return Identifies
	 */
	public function getDefaultContract()
	{
		return $this->default_contract;
	}

	/**
	 * @throws DefaultSerializationContractIsNotRegistered
	 */
	private function guardDefaultContractIsRegistered()
	{
		if ( !$this->registry->isContractRegistered( $this->default_contract ) )
		{
			throw new DefaultSerializationContractIsNotRegistered( $this->default_contract->toString() );
		}
	}
}