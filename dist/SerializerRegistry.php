<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\SerializationContractIsNotRegistered;
use hollodotme\MilestonES\Interfaces\SerializesData;

/**
 * Class SerializerRegistry
 *
 * @package hollodotme\MilestonES
 */
class SerializerRegistry
{

	/** @var SerializesData[] */
	private $serializer_map = [ ];

	/**
	 * @param Contract $contract
	 * @param SerializesData $serializer
	 */
	public function registerSerializerForContract( Contract $contract, SerializesData $serializer )
	{
		$this->serializer_map[ $contract->toString() ] = $serializer;
	}

	/**
	 * @param Contract $contract
	 *
	 * @throws SerializationContractIsNotRegistered
	 * @return SerializesData
	 */
	public function getSerializerForContract( Contract $contract )
	{
		if ( $this->isContractRegistered( $contract ) )
		{
			return $this->getSerializerForRegisteredContract( $contract );
		}
		else
		{
			throw new SerializationContractIsNotRegistered( $contract->toString() );
		}
	}

	/**
	 * @param Contract $contract
	 *
	 * @return bool
	 */
	public function isContractRegistered( Contract $contract )
	{
		return array_key_exists( $contract->toString(), $this->serializer_map );
	}

	/**
	 * @param Contract $contract
	 *
	 * @return SerializesData
	 */
	private function getSerializerForRegisteredContract( Contract $contract )
	{
		return $this->serializer_map[ $contract->toString() ];
	}
} 