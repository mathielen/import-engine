php import-engine
=============

[![Build Status](https://travis-ci.org/mathielen/import-engine.png?branch=master)](https://travis-ci.org/mathielen/import-engine) 
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mathielen/import-engine/badges/quality-score.png?s=c0a55ac5381a3f8fdacd95eeacc5e5ad8926695f)](https://scrutinizer-ci.com/g/mathielen/import-engine/)
[![Code Coverage](https://scrutinizer-ci.com/g/mathielen/import-engine/badges/coverage.png?s=5f083d5500d3ec956d5fc86a8570a97e2bb9c6dd)](https://scrutinizer-ci.com/g/mathielen/import-engine/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/985f7541-2ef9-4b92-98d3-8cf7f4144e74/mini.png)](https://insight.sensiolabs.com/projects/985f7541-2ef9-4b92-98d3-8cf7f4144e74)
[![Latest Stable Version](https://poser.pugx.org/mathielen/import-engine/v/stable.png)](https://packagist.org/packages/mathielen/import-engine)

Full-blown importer stack for importing almost any data into your application. Can be used for exports, too.

Introduction
------------
This library implements some high-level functionality based on the great [Ddeboer Data Import library](https://github.com/ddeboer/data-import).
As the Data-Import library offers a great toolkit to implement a data import/export process in a quick and clean way, there is still a lot of work to do to have a full blown importer set up for your application. This library helps you with that.

Features
--------
* A Storage Abstraction-layer that supports nice features like [automatic delimiter-discovering](#automatic-csv-delimiter-discovery-for-filestorageproviders) or processing compressed files. Currently these storages are supported:
  * CSV files
  * Excel files
  * Doctrine2 queries
  * Compressed files support (with a single file inside)
* Storage Provisioning. Provide a list of possible storage-containers for your import. I.e. local files, remote files, uploaded files, database connections, service endpoints and more.
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
  * An [Eventsystem](#eventsystem) for implementing detailed [Logging](#eventsystem) or other interactions within the process.
* An [Import](#import) is a specific definition of the process. It uses the [Importer](#importer) and has the specific informations that is mandatory for processing the data. That is a SourceStorage, a TargetStorage and a [Mapping](#mapping).
* The [ImportRunner](#importrunner) is used to process the Import.
* Every run of an Import is represented by an [ImportRun](#importrun)


### StorageProvider 

#### FinderFileStorageProvider
Using the [Symfony Finder Component](http://symfony.com/doc/current/components/finder.html) as a collection of possible files that can be imported.

```php
use Symfony\Component\Finder\Finder;
use Mathielen\ImportEngine\Storage\Provider\FinderFileStorageProvider;

$finder = Finder::create()
  ->in('tests/metadata/testfiles')
  ->name('*.csv')
  ->name('*.tab')
  ->size('>0K')
;
  
$ffsp = new FinderFileStorageProvider($finder);
```

#### DoctrineQueryStorageProvider
You can use specific Doctrine Queries or only Entity-Classnames (the query will be SELECT * FROM <Entity> then) as possible Source-Storages.

```php
use Symfony\Component\Finder\Finder;
use Mathielen\ImportEngine\Storage\Provider\DoctrineQueryStorageProvider;

$em = ... //Doctrine2 EntityManager
$qb = $em->createQueryBuilder()
  ->select('a')
  ->from('MySystem\Entity\Address', 'a')
  ->andWhere('a.id > 10')
;

$queries = array(
  'MySystem/Entity/MyEntity',
  $qb
);

$desp = new DoctrineQueryStorageProvider($em, $queries);
```

#### UploadFileStorageProvider
You can use a Provider to facilitate a File-Upload.

```php
use Mathielen\ImportEngine\Storage\Provider\UploadFileStorageProvider;

$ufsp = new UploadFileStorageProvider('/tmp'); //path to where the uploaded files will be transferred to
```

#### Automatic CSV Delimiter Discovery for FileStorageProviders
FileStorageProviders may use StorageFactories for constructing Storage objects. By default the DefaultLocalFileStorageFactory is used. This StorageFactory uses a MimeTypeDiscoverStrategy to determine the mime-type of the selected file and use it to create the correct storage-handler. You can change this behavior or extend it. There is a CsvAutoDelimiterTypeFactory that you can use to automaticly guess the correct delimiter of a CSV file.

```php
use Mathielen\ImportEngine\Storage\Format\Factory\CsvAutoDelimiterFormatFactory;
use Mathielen\ImportEngine\Storage\Factory\DefaultLocalFileStorageFactory;
use Mathielen\ImportEngine\Storage\Format\Discovery\MimeTypeDiscoverStrategy;

$ffsp = ...
$ffsp->setStorageFactory(
  new DefaultLocalFileStorageFactory(
    new MimeTypeDiscoverStrategy(array(
      'text/plain' => new CsvAutoDelimiterFormatFactory()
))));
```
This way any file that has the text/plain mime-type will be passed to the CsvAutoDelimiterFormatFactory to determine the delimiter.

### Validation
You can get the source and target validation errors with:
```php
$import = ...
$import->validation()->getViolations();
```

#### Source data validation
```php
use Mathielen\ImportEngine\Validation\Validation;
use Mathielen\DataImport\Filter\ClassValidatorFilter;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

$validator = ... //Symfony Validator

$validation = Validation::build($validator)
  ->addSourceConstraint('salutation', new NotBlank()) //source field 'salutation' should not be empty
  ->addSourceConstraint('zipcode', new Regex("/[0-9]{5}/")) //source field 'zipcode' should be 5 digits
;
```

#### Target data Validation

##### ClassValidatorFilter
You can use the ClassValidatorFilter to map the data to an object-tree and validate the objects (using annotations, or [differently configurated validation rules](http://symfony.com/doc/current/book/validation.html#constraint-configuration)). Therefore you must provide an ObjectFactory. There is a JmsSerializerObjectFactory you may want to use.

```php
use Mathielen\ImportEngine\Validation\Validation;
use Mathielen\DataImport\Filter\ClassValidatorFilter;
use Mathielen\DataImport\Writer\ObjectWriter\JmsSerializerObjectFactory;

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
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Storage\ArrayStorage;
use Mathielen\ImportEngine\Storage\Provider\UploadFileStorageProvider;

$ffsp = ...
$validation = ...
$targetStorage = ...

$importer = Importer::build($targetStorage)
  ->addSourceStorageProvider('uploadedFile', new UploadFileStorageProvider('/tmp'))
  ->addSourceStorageProvider('myLocalFiles', $ffsp)
  ->setValidation($validation)  
;
```

### Import
```php
use Mathielen\ImportEngine\Import\Import;

$importer = ...

$import = Import::build($importer)
  ->setSourceStorageProviderId('myLocalFiles') //use this storageProvider
  ->setSourceStorageId('tests/metadata/testfiles/flatdata.csv') //use this storage-id
;
```

### Source Storage
You can either use a StorageProvider (see above) and set the selection-id or you can use a specific Storage-Handler directly:
```php
use Mathielen\ImportEngine\Storage\ArrayStorage;
use Mathielen\ImportEngine\Storage\LocalFileStorage;
use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Importer\Importer;
use Mathielen\ImportEngine\Storage\Format\CsvFormat;

$targetArray = ...
$importer = Importer::build($targetArray);
$import = Import::build($importer)
  ->setSourceStorage(new LocalFileStorage(new \SplFileObject(__DIR__ . '/../../../metadata/testfiles/flatdata.csv'), new CsvFormat()));

```

### Mapping
```php
$import = ... 

$import->mappings()
  ->add('SALUTATION_FIELD', 'salutation')
  ->add('FILE_FIELD0', 'first_name')
;
```

### Converting fields
There are a some field-level build-in converters available:
* upperCase
* lowerCase
* @TODO

```php
$import = ...

$import->mappings()
  ->add('SALUTATION_FIELD', 'salutation', 'upperCase')
;
```

### Custom fieldlevel-converting
You have to register more complex converters to the importer for selecting them in your import.
```php
use Mathielen\ImportEngine\Mapping\Converter\Provider\DefaultConverterProvider;
use Ddeboer\DataImport\ValueConverter\CallbackValueConverter;
use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Storage\ArrayStorage;
use Mathielen\ImportEngine\Importer\Importer;

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

$array = array();
$import = Import::build($importer)
  ->setSourceStorage(new ArrayStorage($array))
  ->mappings()
  ->add('salutation', 'gender', 'salutationToGender')
;
```

### Custom rowlevel-converting
Like the fieldlevel converters, you have to register your converters first.
```php
use Mathielen\ImportEngine\Mapping\Converter\Provider\DefaultConverterProvider;
use Ddeboer\DataImport\ItemConverter\CallbackItemConverter;
use Mathielen\ImportEngine\Import\Import;
use Mathielen\ImportEngine\Storage\ArrayStorage;
use Mathielen\ImportEngine\Importer\Importer;

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

$array = array();
$import = Import::build($importer)
  ->setSourceStorage(new ArrayStorage($array))
  ->mappings()
  ->add('fullname', null, 'splitNames')
;
```

### ImportRunner
For running a configured Import you need an ImportRunner. Internally the ImportRunner builds a workflow and runs it.
You can change the way how the workflow is built by supplying a different WorkflowFactory.
```php
use Symfony\Component\EventDispatcher\EventDispatcher;
use Mathielen\ImportEngine\Import\Run\ImportRunner;
use Mathielen\ImportEngine\Import\Workflow\DefaultWorkflowFactory;

$workflowFactory = new DefaultWorkflowFactory(new EventDispatcher());
$importRunner = new ImportRunner($workflowFactory);

$import = ...

//sneak peak a row
$previewData = $importRunner->preview($import);

//dont really write, just validate
$importRun = $importRunner->dryRun($import);

//do the import
$importRun = $importRunner->run($import);
```

### ImportRun statistics
If you use the DefaultWorkflowFactory with your ImportRunner you get basic statistics from dryRun() and run() invocations. 
```php
$import = ...
$importRunner = ...

$importRun = $importRunner->dryRun($import);
$stats = $importRun->getStatistics();

/*
Array
(
    [processed] => 1
    [written] => 1
    [skipped] => 0
    [invalid] => 0
)
*/
```

### Eventsystem
You can interact with the running import via the [Symfony Eventdispatcher](http://symfony.com/doc/current/components/event_dispatcher/introduction.html).

```php
use Symfony\Component\EventDispatcher\EventDispatcher;
use Mathielen\ImportEngine\Import\Run\ImportRunner;
use Mathielen\DataImport\Event\ImportProcessEvent;
use Mathielen\DataImport\Event\ImportItemEvent;
use Mathielen\ImportEngine\Import\Workflow\DefaultWorkflowFactory;

$myListener = function ($event) {
    if ($event instanceof ImportItemEvent) {
    	$currentResult = $event->getCurrentResult(); //readonly access to current result in the process (might be false)
    }
};

$eventDispatcher = new EventDispatcher();
$eventDispatcher->addListener(ImportProcessEvent::AFTER_PREPARE, $myListener);
$eventDispatcher->addListener(ImportItemEvent::AFTER_READ, $myListener);
$eventDispatcher->addListener(ImportItemEvent::AFTER_FILTER, $myListener);
$eventDispatcher->addListener(ImportItemEvent::AFTER_CONVERSION, $myListener);
$eventDispatcher->addListener(ImportItemEvent::AFTER_CONVERSIONFILTER, $myListener);
$eventDispatcher->addListener(ImportItemEvent::AFTER_VALIDATION, $myListener);
$eventDispatcher->addListener(ImportItemEvent::AFTER_WRITE, $myListener);
$eventDispatcher->addListener(ImportProcessEvent::AFTER_FINISH, $myListener);

$workflowFactory = new DefaultWorkflowFactory($eventDispatcher);
$importRunner = new ImportRunner($workflowFactory);

$import = ...
$importRun = $importRunner->run($import);
```

License
-------

Import-Engine is released under the MIT license. See the [LICENSE](LICENSE) file for details.
