<?php
namespace Mathielen\ImportEngine\Import;

use Mathielen\ImportEngine\Importer\ImporterRepository;
use Mathielen\ImportEngine\ValueObject\ImportConfiguration;
use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\ValueObject\StorageSelection;
use Mathielen\ImportEngine\Storage\StorageLocator;
use Mathielen\ImportEngine\Event\ImportConfigureEvent;
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
        EventDispatcherInterface $eventDispatcher)
    {
        $this->importerRepository = $importerRepository;
        $this->storageLocator = $storageLocator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return Id
     */
    public function findImporterForStorage(StorageInterface $storage)
    {
        return $this->importerRepository->find($storage);
    }

    /**
     * @return ImportConfiguration
     */
    public function configure($idImporter, StorageSelection $storageSelection=null)
    {
        if (empty($idImporter)) {
            throw new \InvalidArgumentException("Importer Id must be given!");
        }

        $importConfiguration = new ImportConfiguration($idImporter, $storageSelection);

        return $this->build($importConfiguration);
    }

    /**
     * @return ImportConfiguration
     */
    public function build(ImportConfiguration $importConfiguration)
    {
        $importer = $this->importerRepository->get($importConfiguration->getImporterId());

        $import = Import::build($importer);
        $importConfiguration->applyImport($import, $this->storageLocator);

        //notify system
        $this->eventDispatcher->dispatch(
            ImportConfigureEvent::AFTER_BUILD.'.'.$importConfiguration->getImporterId(),
            new ImportConfigureEvent($import));

        return $importConfiguration;
    }

}
