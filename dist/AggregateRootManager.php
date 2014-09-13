<?php
/**
 *
 * @author h.woltersdorf
 */

namespace hollodotme\MilestonES;

use hollodotme\MilestonES\Exceptions\RepositoryWithNameDoesNotExist;

/**
 * Class AggregateRootManager
 *
 * @package hollodotme\MilestonES
 */
class AggregateRootManager
{

	/** @var UnitOfWork */
	protected $unit_of_work;

	/** @var array|AggregateRootRepository[] */
	protected $repositories = [ ];

	/**
	 * @param UnitOfWork $unit_of_work
	 */
	public function __construct( UnitOfWork $unit_of_work )
	{
		$this->unit_of_work = $unit_of_work;
	}

	/**
	 * @param string $aggregate_root_fqcn
	 *
	 * @throws RepositoryWithNameDoesNotExist
	 * @return AggregateRootRepository|null
	 */
	public function getRepository( $aggregate_root_fqcn )
	{
		$class_name = $aggregate_root_fqcn . 'Repository';
		if ( class_exists( $class_name, true ) )
		{
			$repository = $this->createRepositoryInstanceWithName( $class_name );
		}
		else
		{
			$repository = null;
			throw new RepositoryWithNameDoesNotExist( $class_name );
		}

		return $repository;
	}

	/**
	 * @param string $class_name
	 *
	 * @return AggregateRootRepository
	 */
	protected function createRepositoryInstanceWithName( $class_name )
	{
		return new $class_name( $this->unit_of_work );
	}

	private function __clone()
	{
	}

	/**
	 * @return static
	 */
	public static function shared()
	{
		static $instance = null;

		if ( is_null( $instance ) )
		{
			$instance = new static( new UnitOfWork() );
		}

		return $instance;
	}
}
