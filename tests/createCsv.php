<?php
use Faker\Factory;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Writer\CsvWriter;
require 'bootstrap.php';

$rows = array();

$faker = Factory::create();
for ($i=0; $i < 100; $i++) {
    $row = array(
        'prefix' => $faker->prefix,
        'name' => $faker->name,
        'street' => $faker->streetAddress,
        'zip' => $faker->postcode,
        'city' => $faker->city,
        'phone' => $faker->phoneNumber,
        'email' => $faker->email
    );

    if (count($rows) == 0) {
        $rows[] = array_keys($row);
    }

    $rows[] = $row;
}

$reader = new ArrayReader($rows);
$workflow = new Workflow($reader);
$workflow
    ->addWriter(new CsvWriter(new \SplFileObject(__DIR__ . '/metadata/testfiles/100.csv', 'w+')))
    ->process();
