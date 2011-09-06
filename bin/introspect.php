<?php

include 'config.php';

$myobj = new Dase_DBO_Language($db);
$myobj->load(52);
//$myobj->findOne();

$mytable = $myobj->getTable();


foreach ($myobj->getMembers()  as $k => $mem) {
		if (is_array($mem)) {
				$table = rtrim($k,'s');
				$class = Dase_DBO::getDBOClass($table);
				$obj = new $class($myobj->db);
				$id_field = $mytable.'_id';
				$obj->$id_field = $myobj->id;
				$myobj->$mem = $obj->findAll();
		}
}
