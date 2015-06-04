<?php
namespace Mathielen\ImportEngine\Importer;

use Mathielen\ImportEngine\Filter\Filters;
use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Transformation\Transformation;
use Mathielen\ImportEngine\Validation\ValidationInterface;
use Mathielen\ImportEngine\Validation\DummyValidation;

class Importer implements ImporterInterface
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
     * @var Filters
     */
    private $filters;

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

        $this->validation(new DummyValidation());
        $this->transformation(new Transformation());
        $this->filters(new Filters());
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
    public function validation(ValidationInterface $validation = null)
    {
        if ($validation) {
            $this->validation = $validation;
        }

        return $this->validation;
    }

    /**
     * @return \Mathielen\ImportEngine\Transformation\Transformation
     */
    public function transformation(Transformation $transformation = null)
    {
        if ($transformation) {
            $this->transformation = $transformation;
        }

        return $this->transformation;
    }

    /**
     * @return \Mathielen\ImportEngine\Filter\Filters
     */
    public function filters(Filters $filters = null)
    {
        if ($filters) {
            $this->filters = $filters;
        }

        return $this->filters;
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
