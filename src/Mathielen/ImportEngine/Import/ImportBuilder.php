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
        EventDispatcherInterface $eventDispatcher = null)
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
     *
     * @throws InvalidConfigurationException
     */
    public function buildFromRequest(ImportRequest $importRequest)
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
                    throw new InvalidConfigurationException('No importerId was given and there is no importer that matches the storage.');
                }

                $importRequest->setImporterId($importerId);
                $this->eventDispatcher->dispatch(
                    ImportRequestEvent::DISCOVERED,
                    new ImportRequestEvent($importRequest));
            }
        }

        $importConfiguration = new ImportConfiguration($storageSelection, $importerId);

        return $this->build($importConfiguration, $sourceStorage, $importRequest->getCreatedBy(), $importRequest->getContext());
    }

    /**
     * @return Import
     *
     * @throws InvalidConfigurationException
     */
    public function build(ImportConfiguration $importConfiguration, StorageInterface $sourceStorage = null, $createdBy = null, $requestContext = null)
    {
        $importer = $this->importerRepository->get($importConfiguration->getImporterId());
        if ($importer->getSourceStorage()) {
            $sourceStorage = $importer->getSourceStorage();
        } elseif (!$sourceStorage) {
            throw new InvalidConfigurationException("Either the importRequest or the importer '".$importConfiguration->getImporterId()."' must have a source storage set.");
        }

        $importRun = $importConfiguration->toRun($createdBy);

        //apply static context from importer & request
        $context = $requestContext;
        if (!is_null($importer->getContext()) && !empty($importer->getContext())) {
            $importerContext = $importer->getContext();
            if ($importerContext && $requestContext) {
                $context = array_merge($importerContext, $requestContext);
            } else {
                $context = $importerContext;
            }
        }
        $importRun->setContext($context);

        $import = $this->factorImport($importer, $sourceStorage, $importRun);

        //after everthing was build, apply softdata from sourcestorage to importrun
        //dont do this any earlier, as there might be AFTER_BUILD hooks, that may change
        //the sourcestorage configuration
        $importRun->setInfo((array) $sourceStorage->info());

        return $import;
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
