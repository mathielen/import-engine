<?php
namespace DataImportEngine\Importer;

use DataImportEngine\Storage\Provider\LocalFileStorageProvider;
use DataImportEngine\Storage\Provider\UploadFileStorageProvider;
use Symfony\Component\Finder\Finder;
use DataImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use DataImportEngine\Storage\Type\Discovery\MimeTypeDiscoverStrategy;
use DataImportEngine\Storage\Type\Factory\CsvAutoDelimiterTypeFactory;
use DataImportEngine\Storage\ObjectStorage;

class ImporterFactory
{

    public function hasImporter($importId)
    {
        return true;
    }

    /**
     * return Importer
     */
    public function factor($importerId)
    {
        $finder = Finder::create()
           ->in(__DIR__ . '/../../../tests/metadata/testfiles')
           ->name('*');

        $lfsp = new LocalFileStorageProvider($finder);
        $lfsp->setStorageFactory(
            new DefaultLocalFileStorageFactory(
                new MimeTypeDiscoverStrategy(array(
                    'text/plain' => new CsvAutoDelimiterTypeFactory()
                ))));

        $targetStorage = new ObjectStorage();

        return Importer::build($targetStorage)
            ->addSourceStorageProvider('myLocalFiles', $lfsp)
            ->addSourceStorageProvider('uploadFile', new UploadFileStorageProvider(__DIR__ . '/../../../tmp'))
            ;
    }

}
