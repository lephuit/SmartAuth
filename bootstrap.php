<?php
Autoloader::add_namespace('SmartAuth', __DIR__.'classes');
Autoloader::add_core_namespace('SmartAuth', true);

Autoloader::add_classes(array(
	'SmartAuth\\SmartUser'  => __DIR__.'/classes/smartuser.php',
	'SmartAuth\\SmartGroup' => __DIR__.'/classes/smartgroup.php',
	'SmartAuth\\SmartRole'  => __DIR__.'/classes/smartrole.php',
));

/* End of file bootstrap.php */