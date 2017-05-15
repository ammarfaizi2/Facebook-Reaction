<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

define('fb_data', __DIR__ . '/fb_data');
is_dir(fb_data) or mkdir(fb_data);

use System\Facebook;

$fb = new Facebook($config['email'],$config['pass'],$config['user'],$config['token']);
#print $fb->login();