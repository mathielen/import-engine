<?php
namespace Mathielen\ImportEngine\Import;

use Mathielen\ImportEngine\Importer\ImporterRepository;
class ImportManager
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
    public function create($importerId)
    {
        $importer = $this->importerRepository->get($importerId);
        $import = new Import($importer);

        return $import;
    }

}
