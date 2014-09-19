<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\DefaultSerializationContractIsNotRegistered;

/**
 * Class SerializationStrategy
 *
 * @package hollodotme\MilestonES
 */
class SerializationStrategy
{

	/** @var SerializerRegistry */
	private $registry;

	/** @var Contract */
	private $default_contract;

	/**
	 * @param SerializerRegistry $registry
	 * @param Contract           $default_contract
	 *
	 * @throws DefaultSerializationContractIsNotRegistered
	 */
	public function __construct( SerializerRegistry $registry, Contract $default_contract )
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
	 * @param Contract $contract
	 *
	 * @throws Exceptions\SerializationContractIsNotRegistered
	 * @return Interfaces\SerializesData
	 */
	public function getSerializerForContract( Contract $contract )
	{
		return $this->registry->getSerializerForContract( $contract );
	}

	/**
	 * @return Contract
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
