<?php
namespace Mathielen\ImportEngine\Importer\Discovery\Strategy;

class StorageInfoDiscoverStrategy implements DiscoverStrategyInterface
{

	/**
	 * @return \Mathielen\ImportEngine\Importer\Discovery\Strategy\DefaultDiscoverStrategy
	 */
	public function filename($pattern)
	{
		return $this;
	}
	
	/**
	 * @return \Mathielen\ImportEngine\Importer\Discovery\Strategy\DefaultDiscoverStrategy
	 */
	public function format($id)
	{
		return $this;
	}
	
	/**
	 * Fieldset must have this number of fields
	 * 
	 * @return \Mathielen\ImportEngine\Importer\Discovery\Strategy\DefaultDiscoverStrategy
	 */
	public function fieldcount($count)
	{
		return $this;
	}
	
	/**
	 * Fieldest must have field with this name, anywhere in fieldset
	 * 
	 * @return \Mathielen\ImportEngine\Importer\Discovery\Strategy\DefaultDiscoverStrategy
	 */
	public function field($fieldname)
	{
		return $this;
	}
	
	/**
	 * Add required fields, must exist in the given order
	 * 
	 * @return \Mathielen\ImportEngine\Importer\Discovery\Strategy\DefaultDiscoverStrategy
	 */
	public function fieldset(array $fields)
	{
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mathielen\ImportEngine\Importer\Discovery\Strategy\DiscoverStrategyInterface::discover()
	 */
	public function discover(StorageInterface $storage)
	{
		
	}
	
}