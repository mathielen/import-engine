<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Symfony\Component\Finder\Finder;
use Mathielen\ImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use Mathielen\ImportEngine\Storage\Type\Discovery\MimeTypeDiscoverStrategy;
use MyProject\Proxies\__CG__\OtherProject\Proxies\__CG__\stdClass;

class FinderFileStorageProvider extends AbstractStorageProvider implements \IteratorAggregate
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
            $item = new \stdClass();
            $item->name = $file->getFilename();
            $item->impl = $file;
            $files[] = $item;
        }

        return new \ArrayIterator($files);
    }

}
