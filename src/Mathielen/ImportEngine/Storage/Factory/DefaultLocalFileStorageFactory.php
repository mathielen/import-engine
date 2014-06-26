<?php
namespace Mathielen\ImportEngine\Storage\Factory;

use Mathielen\ImportEngine\Storage\LocalFileStorage;
use Mathielen\ImportEngine\Storage\Format\Discovery\FormatDiscoverStrategyInterface;
use Mathielen\ImportEngine\ValueObject\StorageSelection;

class DefaultLocalFileStorageFactory implements StorageFactoryInterface
{

    /**
     * @var FormatDiscoverStrategyInterface
     */
    private $formatDiscoverStrategyInterface;

    public function __construct(FormatDiscoverStrategyInterface $formatDiscoverStrategyInterface)
    {
        $this->formatDiscoverStrategyInterface = $formatDiscoverStrategyInterface;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Factory\StorageFactoryInterface::factor()
     */
    public function factor(StorageSelection $selection)
    {
        $file = $selection->getImpl();

        if (!($file instanceof \SplFileObject)) {
            throw new \InvalidArgumentException("StorageSelection does not contain a SplFileObject as impl property");
        }

        $format = $this->formatDiscoverStrategyInterface->getFormat($file->getRealPath());
        if (!$format) {
            throw new \LogicException("Could not discover format!");
        }

        $localFile = new LocalFileStorage($file, $format);

        return $localFile;
    }
}
