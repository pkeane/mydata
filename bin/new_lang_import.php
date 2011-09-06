<?php

include 'config.php';

$case_study_areas[0] = 'Unknown';
$case_study_areas[1] = 'Australia';
$case_study_areas[2] = 'North America';
$case_study_areas[3] = 'Amazonia';
$case_study_areas[4] = 'All';

class Language extends MyORM_DBO {
    protected static $table = 'Language_List';
}

$old['host'] = 'mysql.laits.utexas.edu';
$old['name'] = 'hunter-gatherer_languages';
$old['user'] = 'hgl_user';
$old['pass'] = "z<29NP4n";

$odb = new MyORM_DB($old);

/*
$test = new Dase_DBO_Language($db);
print_r($test->getFieldNames());
 */


$langs = new Language($odb);
foreach ($langs->findAll() as $lang) {
		$_lang = new Dase_DBO_Language($db);
		//$_lang->name = utf8_decode($lang->language_name);
		$_lang->name = $lang->language_name;
		print "working on $_lang->name\n";
		$_lang->notes = $lang->notes;
		$_lang->latitude = $lang->latitude;
		$_lang->longitude = $lang->longitude;
		$_lang->iso_code = $lang->iso_code;
		if ($lang->case_study_region_id && $lang->case_study_region_id < 4) {
				$_lang->case_study_area_id = $lang->case_study_region_id;
		}
		$_lang->created = date(DATE_ATOM);
		$_lang->created_by = 'pkeane';
		$_lang->insert();
		if ($lang->other_names) {
				$set = explode(',',$lang->other_names);
				$delta = 0;
				foreach ($set as $other_name) {
						//$other_name = utf8_decode($other_name);
						if (trim($other_name)) {
								$on = new Dase_DBO_LanguageOtherName($db);
								$on->name = $other_name;
								$on->language_id = $_lang->id;
								if (!$on->findOne()) {
										$on->insert();
								}
						}
				}
		}

		$updated = 0;
		if ($lang->subgroup) {
				$sg = new Dase_DBO_LanguageSubgroup($db);
				$sg->name = trim($lang->subgroup);
				if (!$sg->findOne()) {
						$sg->insert();
				}
				$_lang->language_subgroup_id = $sg->id;
				$updated++;
		}

		if ($lang->family) {
				$fam = new Dase_DBO_LanguageFamily($db);
				$fam->name = trim($lang->family);
				if (!$fam->findOne()) {
						$fam->insert();
				}
				$_lang->language_family_id = $fam->id;
				$updated++;
		}
		if ($updated) {
				$_lang->update();
		}

}


