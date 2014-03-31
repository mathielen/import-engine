import-engine
=============

[![Build Status](https://travis-ci.org/mathielen/import-engine.png?branch=master)](https://travis-ci.org/mathielen/import-engine) 
[![Latest Stable Version](https://poser.pugx.org/mathielen/import-engine/v/stable.png)](https://packagist.org/packages/mathielen/import-engine)

Full-blown importer stack for importing almost any data into your application. Can be used for exports, too.

Introduction
------------
This library implements some high-level functionality based on the great [Ddeboer Data Import library](https://github.com/ddeboer/data-import).
As the Data-Import library offers a great toolkit to implement a data import/export process in a quick and clean way, there is still a lot of work to do to have a full blown importer set up for your application. This library helps you with that.

Features
--------
* Storage Abstraction for CSV files, Excel files, databases and more. Providing nice features like automatic delimiter- and mapping-discovering.
* Storage Provisioning (virtual file system) for local files, remote files, uploaded files, database connections and more.
* A mapping sub-system, for building various mappings for your import: field-field, field-converter-field, field-converter-object and more.
* Automatic mapping into object tree's using the [JMSSerializer](http://jmsyst.com/libs/serializer)
* Source (read) and Target (write) validation using [Symfony Validation](http://symfony.com/doc/current/book/validation.html). Annotations can be used.
* Integrated Eventsystem using [Symfony EventDispatcher](http://symfony.com/doc/current/components/event_dispatcher/introduction.html)
* Keeping almost every flexibility that is offered by the Ddeboer Data Import library.
* Well-tested code. (@TODO)

Installation
------------

This library is available on [Packagist](https://packagist.org/packages/mathielen/import-engine).
The recommended way to install it is through [Composer](http://getcomposer.org):

```bash
$ composer require mathielen/import-engine:dev-master
```

Then include Composer’s autoloader:

```php
require_once 'vendor/autoload.php';
```

Usage
-----

The general idea of this library is the following:
* An [Importer](#importer) is the basic definition of the whole process. It says _what_ may be imported and _where_ to. It consists of:
  * (optional) A [StorageProvider](#storageprovider), that represents a "virtual file system" for selecting a SourceStorage
  * A SourceStorage that may be a file, a database table, an array, an object-tree, etc 
  * A TargetStorage that may be a file, a database table, an array, an object-tree, etc
  * A [Mapping](#mapping), which may contain converters, field-mappings, etc
  * A [Validation](#validation), that may contain validation-rules for data read from the SourceStorage and/or validation-rules for data that will be written to the TargetStorage.
  * [Logging](#logging)
* An [Import](#import) is a specific definition of the process. It uses the Importer and has the specific informations that is mandatory for processing the data.
* The [ImportRunner](#importrunner) is used to process the Import.
* Every run of an Import is represented by an [ImportRun](#importrun)


### StorageProvider 

#### FinderFileStorageProvider
Using the [Symfony Finder Component](http://symfony.com/doc/current/components/finder.html) as a collection of possible files that can be imported.

```php
use Symfony\Component\Finder\Finder;
use DataImportEngine\Storage\Provider\FinderFileStorageProvider;

$finder = Finder::create()
  ->in('files/importable/current')
  ->in('files/importable/failed')
  ->name('*.csv')
  ->name('*.tab')
  ->size('>0K');
  
$ffsp = new FinderFileStorageProvider($finder);
```

#### Automatic CSV Delimiter Discovery
StorageProviders use StorageFactories for constructing Storage objects. By default the DefaultLocalFileStorageFactory is used. This StorageFactory uses a MimeTypeDiscoverStrategy to determine the mime-type of the selected file and use it to create the correct storage-handler. You can change this behavior or extend it. There is a CsvAutoDelimiterTypeFactory that you can use to automaticly guess the correct delimiter of a CSV file.

```php
use DataImportEngine\Storage\Type\Factory\CsvAutoDelimiterTypeFactory;

$ffsp->setStorageFactory(
  new DefaultLocalFileStorageFactory(
    new MimeTypeDiscoverStrategy(array(
      'text/plain' => new CsvAutoDelimiterTypeFactory()
))));
```
This way any file that has the text/plain mime-type will be passed to the CsvAutoDelimiterTypeFactory to determine the delimiter.

### Storage
@TODO

### Validation

#### Source data validation
```php
use DataImportEngine\Validation\Validation;
use DataImportEngine\Import\Filter\ClassValidatorFilter;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

$validator = ... //Symfony Validator

$validation = Validation::build($validator)
  ->addSourceConstraint('salutation', new NotBlank()) //source field 'salutation' should not be empty
  ->addSourceConstraint('zipcode', new Regex("/[0-9]{5}/")) //source field 'zipcode' should be 5 digits
```

#### Target data Validation

##### ClassValidatorFilter
You can use the ClassValidatorFilter to map the data to an object-tree and validate the objects (using annotations, or [differently configurated validation rules](http://symfony.com/doc/current/book/validation.html#constraint-configuration)). Therefore you must provide an ObjectFactory. There is a JmsSerializerObjectFactory you may want to use.

```php
use DataImportEngine\Validation\Validation;
use DataImportEngine\Import\Filter\ClassValidatorFilter;
use DataImportEngine\Writer\ObjectWriter\JmsSerializerObjectFactory;

$validator = ... //Symfony Validator

$jms_serializer = ... 
$objectFactory = new JmsSerializerObjectFactory(
  'Entity\Address',
  $jms_serializer);

$validation = Validation::build($validator)
  ->setTargetValidatorFilter(new ClassValidatorFilter($validator, $objectFactory));
```

### Importer
```php
use DataImportEngine\Importer\Importer;

$ffsp = ...
$validation = ...
$targetArray = array();

$importer = Importer::build(new ArrayStorage($targetArray))
  ->addSourceStorageProvider('uploadedFile', new UploadFileStorageProvider('/tmp'))
  ->addSourceStorageProvider('myLocalFiles', $ffsp)
  ->setValidation($validation)  
;
```

### Import
```php
use DataImportEngine\Import\Import;

$import = Import::build($importer)
  ->setSourceStorageProviderId('myLocalFiles')
  ->setSourceStorageId('files/importable/current/data.csv')
;
```

### Mapping
```php
$import->mappings()
  ->add('SALUTATION_FIELD', 'salutation')
  ->add('FILE_FIELD0', 'first_name');
```

### Converting fields
There are a some field-level build-in converters available:
* upperCase
* lowerCase
* @TODO

```php
$import->mappings()
  ->add('SALUTATION_FIELD', 'salutation', 'upperCase');
```

### Custom fieldlevel-converting
You have to register more complex converters to the importer for selecting them in your import.
```php
use DataImportEngine\Mapping\Converter\Provider\DefaultConverterProvider;
use Ddeboer\DataImport\ValueConverter\CallbackValueConverter;
use DataImportEngine\Import\Import;
use DataImportEngine\Importer\Importer;

$mappingConverterProvider = new DefaultConverterProvider();
$mappingConverterProvider
  ->add('salutationToGender', new CallbackValueConverter(function ($item) {
      switch ($item) {
        case 'Mr.': return 'male';
        case 'Miss':
        case 'Mrs.': return 'femaile';
      }
  }));

$targetStorage = ...
$importer = Importer::build($targetStorage)
  ->setMappingConverterProvider($mappingConverterProvider);

$import = Import::build($importer)
  ->mappings()
  ->add('salutation', 'gender', 'salutationToGender');
```

### Custom rowlevel-converting
Like the fieldlevel converters, you have to register your converters first.
```php
use DataImportEngine\Mapping\Converter\Provider\DefaultConverterProvider;
use Ddeboer\DataImport\ValueConverter\CallbackValueConverter;
use DataImportEngine\Import\Import;
use DataImportEngine\Importer\Importer;

$mappingConverterProvider = new DefaultConverterProvider();
$mappingConverterProvider
  ->add('splitNames', new CallbackItemConverter(function ($item) {
      list($firstname, $lastname) = explode(' ', $item['name']);

      $item['first_name'] = $firstname;
      $item['lastname'] = $lastname;

      return $item;
  }));

$targetStorage = ...
$importer = Importer::build($targetStorage)
  ->setMappingConverterProvider($mappingConverterProvider);

$import = Import::build($importer)
  ->mappings()
  ->add('fullname', null, 'splitNames');
```

### ImportRunner
```php
use Symfony\Component\EventDispatcher\EventDispatcher;
use DataImportEngine\Import\Run;

$importRunner = new ImportRunner(new EventDispatcher());

//sneak peak a row
$previewData = $importRunner->preview($import);

//dont really write, just validate
$importRun = $importRunner->dryRun($import);

//do everything
$importRun = $importRunner->run($import);
```

### ImportRun statistics
@TODO

### Logging
@TODO

License
-------

Import-Engine is released under the MIT license. See the [LICENSE](LICENSE) file for details.
