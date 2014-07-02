<?php
namespace Mathielen\ImportEngine\Importer;

use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Transformation\Transformation;
use Mathielen\ImportEngine\Validation\ValidationInterface;
use Mathielen\ImportEngine\Validation\DummyValidation;

class Importer
{

    /**
     * @var StorageInterface
     */
    private $sourceStorage;

    /**
     * @var StorageInterface
     */
    private $targetStorage;

    /**
     * @var ValidationInterface
     */
    private $validation;

    /**
     * @var Transformation
     */
    private $transformation;

    /**
     * @return \Mathielen\ImportEngine\Importer\Importer
     */
    public static function build(StorageInterface $targetStorage)
    {
        return new self($targetStorage);
    }

    public function __construct(StorageInterface $targetStorage)
    {
        $this->targetStorage = $targetStorage;

        $this->validation = new DummyValidation();
        $this->transformation = new Transformation();
    }

    /**
     * @return \Mathielen\ImportEngine\Importer\Importer
     */
    public function setValidation(ValidationInterface $validation)
    {
        $this->validation = $validation;

        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function targetStorage()
    {
        return $this->targetStorage;
    }

    /**
     * @return ValidationInterface
     */
    public function validation()
    {
        return $this->validation;
    }

    /**
     * @return \Mathielen\ImportEngine\Transformation\Transformation
     */
    public function transformation()
    {
        return $this->transformation;
    }

    /**
     * @return StorageInterface
     */
    public function getSourceStorage()
    {
        return $this->sourceStorage;
    }

    /**
     * @return \Mathielen\ImportEngine\Importer\Importer
     */
    public function setSourceStorage(StorageInterface $sourceStorage)
    {
        $this->sourceStorage = $sourceStorage;

        return $this;
    }

}
