<?php
namespace Mathielen\ImportEngine\Importer\Discovery\Strategy;

use Mathielen\ImportEngine\Storage\StorageInterface;
interface DiscoverStrategyInterface
{
	
	/**
	 * @return importerId
	 */
	public function discover(StorageInterface $storage);
	
}