<?php
namespace Mathielen\ImportEngine\ValueObject;

class ImportRunTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ImportRun
     */
    private $importRun;

    protected function setUp()
    {
        $this->importRun = new ImportRun(new ImportConfiguration(), 'createdBy');
    }

    public function testSetGetContext()
    {
        $context = 'IAmTheContext';

        $this->assertEquals($this->importRun, $this->importRun->setContext($context));
        $this->assertEquals($context, $this->importRun->getContext());
    }

    public function testSetGetStatistics()
    {
        $stats = array('IAmTheStats');

        $this->assertEquals($this->importRun, $this->importRun->setStatistics($stats));
        $this->assertEquals($stats, $this->importRun->getStatistics());
    }

    public function testSetGetInfo()
    {
        $info = array('IAmTheInfo');

        $this->assertEquals($this->importRun, $this->importRun->setInfo($info));
        $this->assertEquals($info, $this->importRun->getInfo());
    }

    public function testConstructor()
    {
        $this->assertNotEmpty($this->importRun->getId());
        $this->assertEquals('createdBy', $this->importRun->getCreatedBy());
        $this->assertTrue($this->importRun->isRunnable());
        $this->assertEquals(new ImportConfiguration(), $this->importRun->getConfiguration());
        $this->assertEquals(ImportRun::STATE_CREATED, $this->importRun->getState());
    }

    public function testRevoke()
    {
        $this->importRun->finish();

        $this->assertFalse($this->importRun->isRevoked());
        $this->assertEquals($this->importRun, $this->importRun->revoke('revokedBy'));
        $this->assertTrue($this->importRun->isRevoked());
        $this->assertFalse($this->importRun->isRunnable());
        $this->assertEquals(ImportRun::STATE_REVOKED, $this->importRun->getState());
    }

    public function testFinish()
    {
        $this->assertFalse($this->importRun->isFinished());
        $this->assertEquals($this->importRun, $this->importRun->finish());
        $this->assertTrue($this->importRun->isFinished());
        $this->assertFalse($this->importRun->isRunnable());
        $this->assertEquals(ImportRun::STATE_FINISHED, $this->importRun->getState());
    }

    public function testValidate()
    {
        $this->assertFalse($this->importRun->isValidated());
        $this->assertEquals($this->importRun, $this->importRun->validated(array('invalid')));
        $this->assertTrue($this->importRun->isValidated());
        $this->assertTrue($this->importRun->isRunnable());
        $this->assertEquals(ImportRun::STATE_VALIDATED, $this->importRun->getState());
        $this->assertEquals(array('invalid'), $this->importRun->getValidationMessages());
    }

    public function testToArray()
    {
        $result = $this->importRun->toArray();
        $this->assertEquals($this->importRun->getId(), $result['id']);
    }

}
