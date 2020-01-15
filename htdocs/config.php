<?php

$file = file(basedir.'/conf/database');
foreach ($file as $line){
	list($param, $value) = explode('=', $line);
	$param = trim($param);
	$value = trim($value);

	if ($param && $value){
		define($param, $value);
	}
}