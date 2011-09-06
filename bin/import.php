<?php

include 'config.php';

class Source extends MyORM_DBO {
    protected static $table = 'Grammatical_Data';
}

class Feat extends MyORM_DBO {
    protected static $table = 'Grammatical_Features';
}

class Lang extends MyORM_DBO {
    protected static $table = 'Language_List';
}

$old['host'] = 'mysql.laits.utexas.edu';
$old['name'] = 'hunter-gatherer_languages';
$old['user'] = 'hgl_user';
$old['pass'] = "z<29NP4n";

$odb = new MyORM_DB($old);
$sources = new Source($odb);

foreach ($sources->findAll() as $source) {
		$source = clone($source);
		$sink = new Dase_DBO_LanguageToGrammaticalFeature($db);
		$sink->bib_source = $source->source;
		$sink->answer = $source->answer;
		$sink->original_form = $source->original_form;
		$sink->phonemicized_form = $source->phonemicized_form;
		$sink->etymology_note = $source->etymology_note;
		$sink->phonology_note = $source->phonology_note;
		$sink->grammatical_note = $source->grammatical_note;
		$sink->general_note = $source->general_note;

		$feat = new Feat($odb);
		$feat->feature_id = $source->feature;
		$feattext = $feat->findOne()->feature;
		$n = new Dase_DBO_GrammaticalFeature($db);
		$n->text = trim($feattext);
		$n->findOne();
		$sink->grammatical_feature_id = $n->id;

		$lang = new Lang($odb);
		$lang->language_id = $source->language;
		$langtext = $lang->findOne()->language_name;
		$n = new Dase_DBO_Language($db);
		$n->name = trim($langtext);
		$n->findOne();
		$sink->language_id = $n->id;

		//print $sink;

		$sink->insert();
}
