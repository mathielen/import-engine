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
* Storage Provisioning for local files, remote files, uploaded files, database connections and more.
* A mapping sub-system, for building various mappings for your import: field-field, field-converter-field, field-converter-object and more.
* Source (read) and Target (write) validation using [Symfony Validation](http://symfony.com/doc/current/book/validation.html)
* Integrated Eventsystem using [Symfony EventDispatcher](http://symfony.com/doc/current/components/event_dispatcher/introduction.html)
* Automatic mapping discovery
* Keeping almost every flexibility that is offered by the Ddeboer Data Import library.
* Well-tested code. (@TODO)

Todos
------------
dry-run (like preview)
console demo
faker
bundle

License
-------

Import-Engine is released under the MIT license. See the [LICENSE](LICENSE) file for details.
