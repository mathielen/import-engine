<?php
/**
 * Created by PhpStorm.
 * User: Markus
 * Date: 03.06.2015
 * Time: 18:46.
 */
namespace Mathielen\ImportEngine\Importer;

use Mathielen\ImportEngine\Filter\Filters;
use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Transformation\Transformation;
use Mathielen\ImportEngine\Validation\ValidationInterface;

interface ImporterInterface
{
    /**
     * @return \Mathielen\ImportEngine\Importer\Importer
     */
    public static function build(StorageInterface $targetStorage);

    /**
     * @return StorageInterface
     */
    public function targetStorage();

    /**
     * @return ValidationInterface
     */
    public function validation(ValidationInterface $validation = null);

    /**
     * @return \Mathielen\ImportEngine\Transformation\Transformation
     */
    public function transformation(Transformation $transformation = null);

    /**
     * @return \Mathielen\ImportEngine\Filter\Filters
     */
    public function filters(Filters $filters = null);

    /**
     * @return StorageInterface
     */
    public function getSourceStorage();

    /**
     * @return \Mathielen\ImportEngine\Importer\Importer
     */
    public function setSourceStorage(StorageInterface $sourceStorage);
}
