<?php
namespace Mathielen\ImportEngine\Import;

use Mathielen\ImportEngine\Importer\ImporterRepository;
use Mathielen\ImportEngine\ValueObject\ImportConfiguration;
use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Storage\StorageLocator;
use Mathielen\ImportEngine\Event\ImportConfigureEvent;
use Mathielen\ImportEngine\ValueObject\ImportRun;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ImportBuilder
{

    /**
     * @var ImporterRepository
     */
    private $importerRepository;

    /**
     * @var StorageLocator
     */
    private $storageLocator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        ImporterRepository $importerRepository,
        StorageLocator $storageLocator,
        EventDispatcherInterface $eventDispatcher=null)
    {
        $this->importerRepository = $importerRepository;
        $this->storageLocator = $storageLocator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return null|string
     */
    public function findImporterForStorage(StorageInterface $storage)
    {
        return $this->importerRepository->find($storage);
    }

    /**
     * @return ImportRun
     */
    public function build(ImportConfiguration $importConfiguration, $createdBy=null)
    {
        $importer = $this->importerRepository->get($importConfiguration->getImporterId());

        $import = Import::build($importer);
        $importConfiguration->applyImport($import, $this->storageLocator);

        //notify system
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(
                ImportConfigureEvent::AFTER_BUILD.'.'.$importConfiguration->getImporterId(),
                new ImportConfigureEvent($import));
        }

        return $importConfiguration->toRun($createdBy);
    }

}
