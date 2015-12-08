<?php
namespace Mathielen\ImportEngine\Import;

use Mathielen\ImportEngine\Event\ImportRequestEvent;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Importer\ImporterRepository;
use Mathielen\ImportEngine\ValueObject\ImportConfiguration;
use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Storage\StorageLocator;
use Mathielen\ImportEngine\Event\ImportConfigureEvent;
use Mathielen\ImportEngine\ValueObject\ImportRequest;
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
     * @return Import
     */
    public function rebuild(ImportRun $importRun)
    {
        $importerId = $importRun->getConfiguration()->getImporterId();

        $importer = $this->importerRepository->get($importerId);
        if ($importer->getSourceStorage()) {
            $sourceStorage = $importer->getSourceStorage();
        } else {
            $sourceStorage = $this->storageLocator->getStorage($importRun->getConfiguration()->getSourceStorageSelection());
        }

        return $this->factorImport($importer, $sourceStorage, $importRun);
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
                    throw new InvalidConfigurationException("No importerId was given and there is no importer that matches the storage.");
                }

                $importRequest->setImporterId($importerId);
                $this->eventDispatcher->dispatch(
                    ImportRequestEvent::DISCOVERED,
                    new ImportRequestEvent($importRequest));
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

        //apply static context from importer
        if (!is_null($importer->getContext())) {
            $importRun->setContext($importer->getContext());
        }

        return $this->factorImport($importer, $sourceStorage, $importRun);
    }

    /**
     * @return Import
     */
    private function factorImport(Importer $importer, StorageInterface $sourceStorage, ImportRun $importRun)
    {
        $import = Import::build($importer, $sourceStorage, $importRun);

        //notify system
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(
                ImportConfigureEvent::AFTER_BUILD,
                new ImportConfigureEvent($import));

            $this->eventDispatcher->dispatch(
                ImportConfigureEvent::AFTER_BUILD.'.'.$importRun->getConfiguration()->getImporterId(),
                new ImportConfigureEvent($import));
        }

        return $import;
    }

}
