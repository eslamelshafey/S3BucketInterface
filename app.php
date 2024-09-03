<?php

use Aws\S3\S3Client;

session_start();

require 'vendor/autoload.php';

$config = require('config.php');

$s3 = S3Client::factory([
    'credentials'=>$config['s3']['credentials'],
    'bucket'=>$config['s3']['bucket'],
    'version'=>$config['s3']['version'],
    'region'=>$config['s3']['region'],
]);