<?php

include 'config.php';

class Source extends MyORM_DBO {
    protected static $table = 'Grammatical_Features_Category';
}

$old['host'] = 'mysql.laits.utexas.edu';
$old['name'] = 'hunter-gatherer_languages';
$old['user'] = 'hgl_user';
$old['pass'] = "z<29NP4n";

$odb = new MyORM_DB($old);
$sources = new Source($odb);

foreach ($sources->findAll() as $source) {
		$sink = new Dase_DBO_GrammaticalFeatureCategory($db);
		$sink->name = $source->category;
		$sink->insert();
}


