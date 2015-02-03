<?php
namespace Mathielen\ImportEngine;

use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Import\Run\ImportRunner;
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Storage\ArrayStorage;
use Mathielen\ImportEngine\Validation\ValidatorValidation;
use Mathielen\ImportEngine\ValueObject\ImportConfiguration;
use Mathielen\ImportEngine\ValueObject\ImportRun;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Validation;

class MultiImportValidationResetTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @medium
     */
    public function test()
    {
        $data1 = [['foo' => 'bar1']];
        $data2 = [['foo' => 'bar2']];

        $targetData = array();
        $targetStorage = new ArrayStorage($targetData);

        $validator = Validation::createValidator();
        $validation = new ValidatorValidation($validator);
        $validation->addSourceConstraint('foo', new Choice(['choices'=>['a']]));

        $importer = Importer::build($targetStorage)
            ->setValidation($validation);

        $import = Import::build($importer);
        $importRunner = ImportRunner::build();

        $import->setSourceStorage(new ArrayStorage($data1));
        $importRun1 = $this->createImportRun($data1, $import);
        $importRunner->dryRun($importRun1);
        $this->assertEquals(1, count($importer->validation()->getViolations()['source']));

        $import->setSourceStorage(new ArrayStorage($data2));
        $importRun2 = $this->createImportRun($data2, $import);
        $importRunner->dryRun($importRun2);
        $this->assertEquals(1, count($importer->validation()->getViolations()['source']));
    }

    private function createImportRun(array $data, Import $import)
    {
        $importConfiguration = new ImportConfiguration();
        $importConfiguration->applyImport($import);
        $importRun = $importConfiguration->toRun();

        return $importRun;
    }

}
