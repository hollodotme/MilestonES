<?php
/**
 *
 * @author hwoltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\SerializationContractIsNotRegistered;
use hollodotme\MilestonES\Interfaces\Identifies;
use hollodotme\MilestonES\Interfaces\SerializesData;

/**
 * Class SerializerRegistry
 * @package hollodotme\MilestonES
 */
class SerializerRegistry
{

	/** @var SerializesData[] */
	private $serializer_map = [];

	/**
	 * @param Identifies     $contract
	 * @param SerializesData $serializer
	 */
	public function registerSerializerForContract( Identifies $contract, SerializesData $serializer )
	{
		$this->serializer_map[$contract->toString()] = $serializer;
	}

	/**
	 * @param Identifies $contract
	 *
	 * @throws SerializationContractIsNotRegistered
	 * @return SerializesData
	 */
	public function getSerializerForContract( Identifies $contract )
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
	 * @param Identifies $contract
	 *
	 * @return bool
	 */
	public function isContractRegistered( Identifies $contract )
	{
		return array_key_exists( $contract->toString(), $this->serializer_map );
	}

	/**
	 * @param Identifies $contract
	 *
	 * @return SerializesData
	 */
	private function getSerializerForRegisteredContract( Identifies $contract )
	{
		return $this->serializer_map[$contract->toString()];
	}
} 