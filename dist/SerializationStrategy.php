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
	private $defaultContract;

	/**
	 * @param SerializerRegistry $registry
	 * @param Contract           $defaultContract
	 *
	 * @throws DefaultSerializationContractIsNotRegistered
	 */
	public function __construct( SerializerRegistry $registry, Contract $defaultContract )
	{
		$this->registry        = $registry;
		$this->defaultContract = $defaultContract;

		$this->guardDefaultContractIsRegistered();
	}

	/**
	 * @throws Exceptions\SerializationContractIsNotRegistered
	 * @return Interfaces\SerializesData
	 */
	public function getDefaultSerializer()
	{
		return $this->registry->getSerializerForContract( $this->defaultContract );
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
		return $this->defaultContract;
	}

	/**
	 * @throws DefaultSerializationContractIsNotRegistered
	 */
	private function guardDefaultContractIsRegistered()
	{
		if ( !$this->registry->isContractRegistered( $this->defaultContract ) )
		{
			throw new DefaultSerializationContractIsNotRegistered( $this->defaultContract->toString() );
		}
	}
}
