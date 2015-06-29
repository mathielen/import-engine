<?php
namespace Mathielen\ImportEngine\Storage\Provider;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFileStorageProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructor()
    {
        new UploadFileStorageProvider('xy');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSelectNotValid()
    {
        $ufsp = new UploadFileStorageProvider(sys_get_temp_dir());
        $uploadedFile = __DIR__.'/../../../../../metadata/testfiles/testSelectNotValid.csv';
        copy(__DIR__.'/../../../../../metadata/testfiles/100.csv', $uploadedFile);
        $ufsp->select(new UploadedFile($uploadedFile, 'original.csv'));
    }

    public function testSelect()
    {
        $ufsp = new UploadFileStorageProvider(sys_get_temp_dir());

        $uploadedFile = __DIR__.'/../../../../../metadata/testfiles/testSelectNotValid.csv';
        copy(__DIR__.'/../../../../../metadata/testfiles/100.csv', $uploadedFile);
        $selection = $ufsp->select(new UploadedFile($uploadedFile, 'original.csv', null, null, null, true));

        $this->assertInstanceOf('\SplFileInfo', $selection->getImpl());
        $this->assertFileExists($selection->getImpl()->getRealPath());
        $this->assertEquals('original.csv', $selection->getName());
    }

}
