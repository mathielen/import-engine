<?php
namespace Mathielen\ImportEngine\Storage\Type\Factory;

use Mathielen\ImportEngine\Storage\Type\CsvType;

class CsvAutoDelimiterTypeFactory implements TypeFactory
{

    /**
     * @return \Mathielen\ImportEngine\Storage\Type\Type
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
