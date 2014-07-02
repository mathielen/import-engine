<?php
namespace Mathielen\ImportEngine\Importer;

use Mathielen\ImportEngine\Storage\StorageInterface;
use Mathielen\ImportEngine\Storage\Format\Format;
use Mathielen\ImportEngine\Exception\InvalidConfigurationException;
use Mathielen\ImportEngine\Storage\StorageFormatInterface;

class ImporterPrecondition
{

    private $filenames = array();
    private $formats = array();
    private $fieldcount = null;
    private $anyfields = array();
    private $fieldset = null;

    /**
     * @return \Mathielen\ImportEngine\Importer\Discovery\Strategy\DefaultDiscoverStrategy
     */
    public function filename($pattern)
    {
        $this->filenames[] = $pattern;

        return $this;
    }

    /**
     * @return \Mathielen\ImportEngine\Importer\Discovery\Strategy\DefaultDiscoverStrategy
     */
    public function format($id)
    {
        $this->formats[] = $id;

        return $this;
    }

    /**
     * Fieldset must have this number of fields
     *
     * @return \Mathielen\ImportEngine\Importer\Discovery\Strategy\DefaultDiscoverStrategy
     */
    public function fieldcount($count)
    {
        $this->fieldcount = $count;

        return $this;
    }

    /**
     * Fieldest must have field with this name, anywhere in fieldset
     *
     * @return \Mathielen\ImportEngine\Importer\Discovery\Strategy\DefaultDiscoverStrategy
     */
    public function field($fieldname)
    {
        $this->anyfields[] = strtolower($fieldname);

        return $this;
    }

    /**
     * Add required fields, must exist in the given order
     *
     * @return \Mathielen\ImportEngine\Importer\Discovery\Strategy\DefaultDiscoverStrategy
     */
    public function fieldset(array $fieldset)
    {
        $this->fieldset = array_map('strtolower', $fieldset);

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Mathielen\ImportEngine\Importer\Discovery\Strategy\DiscoverStrategyInterface::discover()
     */
    public function isSatisfiedBy(StorageInterface $storage)
    {
        if (!($storage instanceof StorageFormatInterface) && !empty($this->formats)) {
            throw new InvalidConfigurationException("Cannot check format when storage does not implement StorageFormatInterface");
        }

        if (!$this->isSatisfiedFilename($storage->info()['name'])) {
            return false;
        }

        if (!$this->isSatisfiedFormat($storage->info()['format'])) {
            return false;
        }

        if (!$this->isSatisfiedFieldcount(count($storage->getFields()))) {
            return false;
        }

        if (!$this->isSatisfiedAnyFields($storage->getFields())) {
            return false;
        }

        if (!$this->isSatisfiedFieldset($storage->getFields())) {
            return false;
        }

        return true;
    }

    private function isSatisfiedFilename($filename)
    {
        if (empty($this->filenames)) {
            return true;
        }

        foreach ($this->filenames as $pattern) {
            if (preg_match($pattern, $filename)) {
                return true;
            }
        }

        return false;
    }

    private function isSatisfiedFormat(Format $format)
    {
        if (empty($this->formats)) {
            return true;
        }

        foreach ($this->formats as $formatId) {
            if ($formatId == $format->getId()) {
                return true;
            }
        }

        return false;
    }

    private function isSatisfiedFieldcount($fieldCount)
    {
        if (is_null($this->fieldcount)) {
            return true;
        }

        return $this->fieldcount == $fieldCount;
    }

    private function isSatisfiedAnyFields(array $fields)
    {
        if (empty($this->anyfields)) {
            return true;
        }

        $fields = array_map('strtolower', $fields);

        foreach ($this->anyfields as $anyField) {
           if (!in_array($anyField, $fields)) {
               return false;
           }
        }

        return true;
    }

    private function isSatisfiedFieldset(array $fieldset)
    {
        if (empty($this->fieldset)) {
            return true;
        }

        return array_map('strtolower', $fieldset) == $this->fieldset;
    }

}
