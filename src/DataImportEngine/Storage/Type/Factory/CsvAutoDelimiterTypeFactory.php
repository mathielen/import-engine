<?php
namespace DataImportEngine\Storage\Type\Factory;

use DataImportEngine\Storage\Type\CsvType;

class CsvAutoDelimiterTypeFactory implements TypeFactory
{

    /**
     * @return \DataImportEngine\Storage\Type\Type
     */
    public function factor($uri)
    {
        $options = array();

        $file = new \SplFileObject($uri);
        $specialCharString = preg_replace('/[a-z0-9éâëïüÿçêîôûéäöüß]/iu', '', utf8_encode($file->getCurrentLine()));
        $charStats = count_chars($specialCharString, 1);
        arsort($charStats);
        $delimiter = chr(key($charStats));

        return new CsvType($delimiter);
    }

}
