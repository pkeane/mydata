<?php

require_once 'Dase/DBO/Autogen/Language.php';

class Dase_DBO_Language extends Dase_DBO_Autogen_Language 
{
		public $case_study_area;
		public $language_family;
		public $language_subgroup;
		public $language_other_names = array();
		public $order_by = 'name';
		//allow us to create an attribute order
		public $attributes = array(
				'name' => '',
				'language_other_names' => '',
				'language_family' => '',
				'language_subgroup' => '',
		);	
		
}
