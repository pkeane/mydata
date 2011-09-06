<?php

include '../inc/config.php';
include '../lib/MyORM/DB.php';
include '../lib/MyORM/DBO.php';
include '../lib/Util.php';
include 'db_helpers.php';


$drupal  = new MyORM_DB($conf);
$olddb = new MyORM_DB($old);


$list = new MyORM_DBO($olddb,'Language_List');

$now = time();

$user = new MyORM_DBO($drupal,'users');
$user->name = 'pkeane';
$uid = $user->findOne()->uid;

$case_study_areas[0] = 'Unknown';
$case_study_areas[1] = 'Australia';
$case_study_areas[2] = 'North America';
$case_study_areas[3] = 'Amazonia';
$case_study_areas[4] = 'All';


$i = 0;
foreach ($list->findAll() as $lang) {

		//print Util::json_format(json_encode($lang->asArray()));
		//print_r($lang->asArray());
		$char = mb_detect_encoding($lang->language_name);
		
		$i++;
		$node = new MyORM_DBO($drupal,'node');
		$node->type = 'language';
		$node->title = utf8_decode($lang->language_name);
		$node->uid = $uid;
		$node->created = $now;
		$node->changed = $now;
		$node->insert();

		$node_rev = new MyORM_DBO($drupal,'node_revisions');
		$node_rev->nid= $node->nid;
		$node_rev->uid= $node->uid;
		$node_rev->title = utf8_decode($lang->language_name);
		$node_rev->body = '-';
		$node_rev->teaser = '-';
		$node_rev->log = 'pk';
		$node_rev->timestamp = $now;
		$node_rev->insert();

		$node->vid = $node_rev->vid;
		$node->update();

		print "\nWorking on $node->title\n";
		$ctl = new MyORM_DBO($drupal,'content_type_language');
		$ctl->nid = $node->nid;
		$ctl->vid = $node_rev->vid;

		if ($lang->subgroup) {
				$ctl->field_subgroup_value =  getTaxonomyTermId($drupal,'Language Subgroup',utf8_decode($lang->subgroup));
		}

		if ($lang->family) {
				$ctl->field_family_value =  getTaxonomyTermId($drupal,'Language Family',utf8_decode($lang->family));
		}

		$ctl->field_notes_value = $lang->notes;
		$ctl->field_iso_code_value = $lang->iso_code;
		$ctl->field_lat_value = $lang->latitude;
		$ctl->field_lon_value = $lang->longitude;

		if (0 == $lang->case_study_region_id) {
				$csa = new MyORM_DBO($drupal,'content_field_case_study_area');
				$csa->field_case_study_area_value = 1298;
				$csa->nid = $node->nid;
				$csa->vid = $node_rev->vid;
				$csa->insert();
		}

		if (1 == $lang->case_study_region_id) {
				$csa = new MyORM_DBO($drupal,'content_field_case_study_area');
				$csa->field_case_study_area_value = 1295;
				$csa->nid = $node->nid;
				$csa->vid = $node_rev->vid;
				$csa->insert();
		}

		if (2 == $lang->case_study_region_id) {
				$csa = new MyORM_DBO($drupal,'content_field_case_study_area');
				$csa->field_case_study_area_value = 1297;
				$csa->nid = $node->nid;
				$csa->vid = $node_rev->vid;
				$csa->insert();
		}

		if (3 == $lang->case_study_region_id) {
				$csa = new MyORM_DBO($drupal,'content_field_case_study_area');
				$csa->field_case_study_area_value = 1296;
				$csa->nid = $node->nid;
				$csa->vid = $node_rev->vid;
				$csa->insert();
		}

		$ctl->insert();

		if ($lang->other_names) {
				$set = explode(',',$lang->other_names);
				$delta = 0;
				foreach ($set as $other_name) {
						$other_name = utf8_decode($other_name);
						if (trim($other_name)) {
						$cf = new MyORM_DBO($drupal,'content_field_other_name');
						$cf->nid = $node->nid;
						$cf->vid = $node_rev->vid;
						$cf->delta = $delta;
						$cf->field_other_name_value = trim($other_name);
						$cf->insert();
						$delta++;
						}
				}
		}
		if ($i > 20) {
				//exit;
		}
}
