<?php
namespace Mathielen\ImportEngine\Import;

use Mathielen\ImportEngine\Exception\InvalidConfigurationException;
use Mathielen\ImportEngine\Importer\ImporterRepository;
use Mathielen\ImportEngine\ValueObject\ImportConfiguration;
use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Storage\StorageLocator;
use Mathielen\ImportEngine\Event\ImportConfigureEvent;
use Mathielen\ImportEngine\ValueObject\ImportRequest;
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
     * @return Import
     */
    public function build(ImportRequest $importRequest)
    {
        $importerId = $importRequest->getImporterId();
        $storageSelection = null;
        $sourceStorage = null;

        if ($importRequest->hasSource()) {
            $storageSelection = $this->storageLocator->selectStorage(
                $importRequest->getSourceProviderId(),
                $importRequest->getSourceId()
            );

            $sourceStorage = $this->storageLocator->getStorage($storageSelection);

            if (!$importRequest->hasImporterId()) {
                $importerId = $this->findImporterForStorage($sourceStorage);

                if (!$importerId) {
                    throw new InvalidConfigurationException("No importerId was given and there is not importer that could automatically match the storage.");
                }
            }
        }

        $importer = $this->importerRepository->get($importerId);
        if ($importer->getSourceStorage()) {
            $sourceStorage = $importer->getSourceStorage();
        } elseif (!$sourceStorage) {
            throw new InvalidConfigurationException("Either the importRequest or the importer '$importerId' must have a source storage set.");
        }

        $importConfiguration = new ImportConfiguration($storageSelection, $importerId);
        $importRun = $importConfiguration->toRun($importRequest->getCreatedBy());
        $importRun->setInfo((array) $sourceStorage->info());

        $import = Import::build($importer, $sourceStorage, $importRun);

        //notify system
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(
                ImportConfigureEvent::AFTER_BUILD.'.'.$importConfiguration->getImporterId(),
                new ImportConfigureEvent($import));
        }

        return $import;
    }

}
