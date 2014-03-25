<?php
namespace DataImportEngine;

use DataImportEngine\Import\Import;
use DataImportEngine\Importer\ImporterFactory;

class ImportManager
{

    /**
     * @var ImporterFactory
     */
    private $importerFactory;

    public function __construct(ImporterFactory $importerFactory)
    {
        $this->importerFactory = $importerFactory;
    }

    /**
     * @return Import
     */
    public function startNewImport($importerId)
    {
        if (!$this->importerFactory->hasImporter($importerId)) {
            throw new \InvalidArgumentException("Unknown importer-id: $importerId");
        }

        $importer = $this->importerFactory->factor($importerId);

        //TODO factory?
        $import = new Import($importer);

        return $import;
    }

}
