<?php
namespace Mathielen\ImportEngine\Import;

use Mathielen\ImportEngine\Importer\ImporterRepository;
use Mathielen\ImportEngine\ValueObject\ImportConfiguration;
class ImportBuilder
{

    /**
     * @var ImporterRepository
     */
    private $importerRepository;

    public function __construct(
        ImporterRepository $importerRepository)
    {
        $this->importerRepository = $importerRepository;
    }

    /**
     * @return ImportConfiguration
     */
    public function build(ImportConfiguration $importConfiguration)
    {
        $importer = $this->importerRepository->get($importConfiguration->importerId);
        if (!isset($importConfiguration->sourceStorageProviderId) && count($importer->getSourceStorageProviders()) == 1) {
            $importConfiguration->sourceStorageProviderId = key($importer->getSourceStorageProviders());
        }

        $importConfiguration->applyImporter($importer);
        return $importConfiguration;
    }

}
