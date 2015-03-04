<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Mathielen\ImportEngine\Storage\Factory\StorageFactoryInterface;
use Symfony\Component\Finder\Finder;
use Mathielen\ImportEngine\ValueObject\StorageSelection;

class FinderFileStorageProvider extends FileStorageProvider implements \IteratorAggregate
{

    /**
     * @var Finder
     */
    private $finder;

    public function __construct(Finder $finder, StorageFactoryInterface $storageFactory=null)
    {
        parent::__construct($storageFactory);

        $this->finder = $finder;
    }

    public function getIterator()
    {
        $files = array();
        foreach ($this->finder->files() as $file) {
            $item = new StorageSelection(new \SplFileInfo($file), $file->getFilename(), $file->getFilename());
            $files[] = $item;
        }

        return new \ArrayIterator($files);
    }

}
