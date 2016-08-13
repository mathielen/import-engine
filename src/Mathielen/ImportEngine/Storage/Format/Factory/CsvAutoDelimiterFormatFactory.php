<?php

namespace Mathielen\ImportEngine\Storage\Format\Factory;

use Mathielen\ImportEngine\Storage\Format\CsvFormat;

class CsvAutoDelimiterFormatFactory implements FormatFactoryInterface
{
    /**
     * @return \Mathielen\ImportEngine\Storage\Format\Format
     */
    public function factor($uri)
    {
        $file = new \SplFileObject($uri);
        $delimiter = $this->guessDelimiter(utf8_encode($file->getCurrentLine()));

        return new CsvFormat($delimiter);
    }

    public function guessDelimiter($line)
    {
        $specialCharString = preg_replace('/[a-z0-9éâëïüÿçêîôûéäöüß\n\r "]/iu', '', $line);

        $charStats = count_chars($specialCharString, 1);

        if (empty($charStats)) {
            throw new \LogicException('Could not discover CSV-delimiter!');
        }

        arsort($charStats);
        $delimiter = chr(key($charStats));

        return $delimiter;
    }
}
