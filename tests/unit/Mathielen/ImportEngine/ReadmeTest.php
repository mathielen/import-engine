<?php
namespace Mathielen\ImportEngine;

class ReadmeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider parseReadme
     */
    public function testPhpCode($startLine, $code)
    {
        if (strpos($code, '...')) {
            $this->markTestSkipped("Cannot test with insufficient info.");
        }

        ob_start();
        eval($code);
        $output = ob_get_contents();
        ob_end_clean();

        if (!empty($output)) {
            $this->fail("Error in README.me at line $startLine:\n".$output);
        }
    }

    public function parseReadme()
    {
        $readme = file(__DIR__ . '/../../../../README.md');

        $startLine = $endLine = null;
        $i = 0;
        foreach ($readme as $line) {
            if ('```php' == trim($line)) {
                $startLine = $i;
                $endLine = null;
            } elseif ('```' == trim($line)) {
                $endLine = $i;
            }

            if ($startLine && $endLine) {
                $startLine++;
                $code = join('', array_slice($readme, $startLine, $endLine-$startLine));

                $code = str_replace('$ffsp = ...', $this->getBuildFFSP(), $code);
                $code = str_replace('$targetStorage = ...', $this->getBuildTargetStorage(), $code);
                $code = str_replace('$importer = ...', $this->getBuildImporterCode(), $code);
                $code = str_replace('$import = ...', $this->getBuildImportCode(), $code);

                $chunks[] = array($startLine, $code);
                $startLine = $endLine = null;
            }

            ++$i;
        }

        return $chunks;
    }

    private function getBuildImportCode()
    {
        return $this->getBuildImporterCode() . '
            $a = array(array("field1"=>"data1"));
            $import = Mathielen\ImportEngine\Import\Import::build($importer);
            $import->setSourceStorage(new \Mathielen\ImportEngine\Storage\ArrayStorage($a));
        ';
    }

    private function getBuildImporterCode()
    {
        return $this->getBuildFFSP() .
            $this->getBuildTargetStorage() . '
            $importer = \Mathielen\ImportEngine\Importer\Importer::build($targetStorage)
               ->addSourceStorageProvider(\'myLocalFiles\', $ffsp);
        ';
    }

    private function getBuildTargetStorage()
    {
        return '
            $a = array();
            $targetStorage = new \Mathielen\ImportEngine\Storage\ArrayStorage($a);
        ';
    }

    private function getBuildFFSP()
    {
        return '
            $ffsp = new Mathielen\ImportEngine\Storage\Provider\FinderFileStorageProvider(Symfony\Component\Finder\Finder::create());
        ';
    }

}
