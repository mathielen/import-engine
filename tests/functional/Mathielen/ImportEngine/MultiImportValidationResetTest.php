<?php
namespace Mathielen\ImportEngine;

use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Import\Run\ImportRunner;
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Storage\ArrayStorage;
use Mathielen\ImportEngine\Validation\ValidatorValidation;
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

        $importer = Importer::build($targetStorage);
        $importer->validation($validation);

        $importRunner = ImportRunner::build();

        $import = Import::build($importer, new ArrayStorage($data1));
        $importRunner->dryRun($import);
        $this->assertEquals(1, count($importer->validation()->getViolations()['source']));

        $import = Import::build($importer, new ArrayStorage($data2));
        $importRunner->dryRun($import);
        $this->assertEquals(1, count($importer->validation()->getViolations()['source']));
    }

}
