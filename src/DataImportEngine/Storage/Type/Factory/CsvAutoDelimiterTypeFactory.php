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
        $delimiter = $this->guessDelimiter(utf8_encode($file->getCurrentLine()));

        return new CsvType($delimiter);
    }

    public function guessDelimiter($line)
    {
        $specialCharString = preg_replace('/[a-z0-9éâëïüÿçêîôûéäöüß "]/iu', '', $line);
        $charStats = count_chars($specialCharString, 1);
        arsort($charStats);
        $delimiter = chr(key($charStats));

        return $delimiter;
    }

}
