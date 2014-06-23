<?php
namespace Mathielen\ImportEngine\Import;

use Mathielen\ImportEngine\Importer\ImporterRepository;
use Mathielen\ImportEngine\Configuration\ImportConfiguration;
class ImportBuilder
{

    /**
     * @var ImporterRepository
     */
    private $importerRepository;

    public function __construct(ImporterRepository $importerRepository)
    {
        $this->importerRepository = $importerRepository;
    }

    /**
     * @return Import
     */
    public function build(ImportConfiguration $importConfiguration)
    {
        return $importConfiguration->buildImport();
    }

    /**
     * @return \Mathielen\ImportEngine\Configuration\ImportConfiguration
     */
    public function configuration($importId)
    {
        $importer = $this->importerRepository->get($importId);
        $importConfiguration = new ImportConfiguration($importer);

        if (count($importer->getSourceStorageProviders()) == 1) {
            $importConfiguration->sourceStorageProviderId = key($importer->getSourceStorageProviders());
        }

        return $importConfiguration;
    }

}
