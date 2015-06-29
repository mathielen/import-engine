<?php
namespace Mathielen\ImportEngine\Importer;

use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Storage\Format\Format;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;
use Mathielen\ImportEngine\Storage\StorageFormatInterface;
use Psr\Log\LoggerInterface;

class ImporterPrecondition
{

    private $filenames = array();
    private $formats = array();
    private $fieldcount = null;
    private $anyfields = array();
    private $fieldset = null;

    /**
     * @return ImporterPrecondition
     */
    public function filename($pattern)
    {
        $this->filenames[] = $pattern;

        return $this;
    }

    /**
     * @return ImporterPrecondition
     */
    public function format($id)
    {
        $this->formats[] = $id;

        return $this;
    }

    /**
     * Fieldset must have this number of fields
     *
     * @return ImporterPrecondition
     */
    public function fieldcount($count)
    {
        $this->fieldcount = $count;

        return $this;
    }

    /**
     * Fieldset must have field with this name, anywhere in fieldset
     *
     * @return ImporterPrecondition
     */
    public function field($fieldname)
    {
        $this->anyfields[] = strtolower($fieldname);

        return $this;
    }

    /**
     * Add required fields, must exist in the given order
     *
     * @return ImporterPrecondition
     */
    public function fieldset(array $fieldset)
    {
        $this->fieldset = array_map('strtolower', $fieldset);

        return $this;
    }

    public function isSatisfiedBy(StorageInterface $storage, LoggerInterface $logger = null)
    {
        if (!($storage instanceof StorageFormatInterface) && !empty($this->formats)) {
            throw new InvalidConfigurationException("Cannot check format when storage does not implement StorageFormatInterface");
        }

        if (!$this->isSatisfiedFilename($storage->info()['name'], $logger)) {
            return false;
        }

        if (!$this->isSatisfiedFormat($storage->info()['format'], $logger)) {
            return false;
        }

        if (!$this->isSatisfiedFieldcount(count($storage->getFields()), $logger)) {
            return false;
        }

        if (!$this->isSatisfiedAnyFields($storage->getFields(), $logger)) {
            return false;
        }

        if (!$this->isSatisfiedFieldset($storage->getFields(), $logger)) {
            return false;
        }

        return true;
    }

    private function isSatisfiedFilename($filename, LoggerInterface $logger = null)
    {
        if (empty($this->filenames)) {
            return true;
        }

        foreach ($this->filenames as $pattern) {
            if (preg_match("/$pattern/i", $filename)) {
                return true;
            }
        }

        if ($logger) {
            $logger->debug("Storage does not meet Preconditions due to filename restriction. Was $filename, should be one of " . join(',', $this->filenames));
        }

        return false;
    }

    private function isSatisfiedFormat(Format $format, LoggerInterface $logger = null)
    {
        if (empty($this->formats)) {
            return true;
        }

        foreach ($this->formats as $formatId) {
            if ($formatId == $format->getId()) {
                return true;
            }
        }

        if ($logger) {
            $logger->debug("Storage does not meet Preconditions due to format restriction. Was $format, should be one of " . join(',', $this->formats));
        }

        return false;
    }

    private function isSatisfiedFieldcount($fieldCount, LoggerInterface $logger = null)
    {
        if (is_null($this->fieldcount)) {
            return true;
        }

        if ($this->fieldcount == $fieldCount) {
            return true;
        }

        if ($logger) {
            $logger->debug("Storage does not meet Preconditions due to fieldcount restriction. Was $fieldCount, should be " . $this->fieldcount);
        }

        return false;
    }

    private function isSatisfiedAnyFields(array $fields, LoggerInterface $logger = null)
    {
        if (empty($this->anyfields)) {
            return true;
        }

        $fields = array_map('strtolower', $fields);
        $fields = array_map('trim', $fields);

        foreach ($this->anyfields as $anyField) {
           if (!in_array($anyField, $fields)) {
               if ($logger) {
                   $logger->debug("Storage does not meet Preconditions due to fields restriction. Missing field: '$anyField'");
               }

               return false;
           }
        }

        return true;
    }

    private function isSatisfiedFieldset(array $fieldset, LoggerInterface $logger = null)
    {
        if (empty($this->fieldset)) {
            return true;
        }

        if (array_map('strtolower', $fieldset) == $this->fieldset) {
            return true;
        }

        if ($logger) {
            $logger->debug("Storage does not meet Preconditions due to fieldset restriction. Was ".join(',', $fieldset).", should be " . join(',', $this->fieldset));
        }

        return false;
    }

}
