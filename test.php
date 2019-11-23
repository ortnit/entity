<?php

use Ortnit\Entity\TestEntity;
use Ortnit\Entity\Validator\Validator;

require('vendor/autoload.php');

$entity = new TestEntity();
$entity->id = 3.0;

$i = null;
dump($entity->id, gettype($i), get_class($i));

$c = new Validator();
dump(gettype($c), get_class($c));