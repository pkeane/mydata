<?php

include 'config.php';

class Source extends MyORM_DBO {
    protected static $table = 'Grammatical_Features';
}

class Cat extends MyORM_DBO {
    protected static $table = 'Grammatical_Features_Category';
}

$old['host'] = 'mysql.laits.utexas.edu';
$old['name'] = 'hunter-gatherer_languages';
$old['user'] = 'hgl_user';
$old['pass'] = "z<29NP4n";

$odb = new MyORM_DB($old);
$sources = new Source($odb);

foreach ($sources->findAll() as $source) {
		$feat = new Dase_DBO_GrammaticalFeature($db);
		$source = clone($source);
		$cat = new Cat($odb);
		$cat->category_id = $source->category;
		$cattext = $cat->findOne()->category;
		$ncat = new Dase_DBO_GrammaticalFeatureCategory($db);
		$ncat->name = trim($cattext);
		$ncat->findOne();
		$feat->grammatical_feature_category_id = $ncat->id;
		$feat->text = $source->feature;
		$feat->note = $source->note;
		print $feat;
		$feat->insert();
}


