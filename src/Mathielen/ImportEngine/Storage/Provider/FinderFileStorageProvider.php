<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Symfony\Component\Finder\Finder;
use Mathielen\ImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use Mathielen\ImportEngine\Storage\Format\Discovery\MimeTypeDiscoverStrategy;
use Mathielen\ImportEngine\Storage\Provider\Selection\StorageSelection;

class FinderFileStorageProvider extends AbstractFileStorageProvider implements \IteratorAggregate
{

    /**
     * @var Finder
     */
    private $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
        $this->setStorageFactory(
            new DefaultLocalFileStorageFactory(
                new MimeTypeDiscoverStrategy()));
    }

    public function getIterator()
    {
        $files = array();
        foreach ($this->finder->files() as $file) {
            $item = new StorageSelection(new \SplFileObject($file), $file->getFilename(), $file->getFilename());
            $files[] = $item;
        }

        return new \ArrayIterator($files);
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Storage\Provider\StorageProviderInterface::select()
     */
    public function select($id = null)
    {
        $selection = $id;
        if (is_string($id)) {
            $selection = new StorageSelection(new \SplFileObject($id), $id, $id);
        } elseif ($id instanceof \SplFileObject) {
            $selection = new StorageSelection($id, $id->getFilename(), $id->getFilename());
        } elseif (!($id instanceof StorageSelection)) {
            throw new \InvalidArgumentException("id must be string, SplFileObject or instance of StorageSelection");
        }

        return $selection;
    }

}
