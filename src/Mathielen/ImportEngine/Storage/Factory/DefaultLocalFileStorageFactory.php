<?php
namespace Mathielen\ImportEngine\Storage\Factory;

use Mathielen\ImportEngine\Storage\LocalFileStorage;
use Mathielen\ImportEngine\Storage\Format\Discovery\FormatDiscoverStrategyInterface;
use Mathielen\ImportEngine\ValueObject\StorageSelection;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;

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

        if (!($file instanceof \SplFileInfo)) {
            throw new InvalidConfigurationException("StorageSelection does not contain a SplFileInfo as impl property but this is mandatory for a LocalFileStorage.");
        }

        $format = $selection->getMetadata('format');
        if (!$format) {
            $format = $this->formatDiscoverStrategyInterface->getFormat($file->getRealPath());
            if (!$format) {
                throw new InvalidConfigurationException("Could not discover format!");
            }
        }

        $localFile = new LocalFileStorage($file, $format);

        return $localFile;
    }
}
