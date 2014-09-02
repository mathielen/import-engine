<?php
require_once __DIR__.'/../vendor/autoload.php';

// Bootstrap the JMS custom annotations for Object to Json mapping
\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
    'JMS\Serializer\Annotation',
    dirname(__DIR__).'/vendor/jms/serializer/src'
);
