<?php

require_once 'Dase/DBO/Autogen/CaseStudyArea.php';

class Dase_DBO_CaseStudyArea extends Dase_DBO_Autogen_CaseStudyArea 
{
		public $languages = array();
		public $language_familys = array();
		public $attributes = array(
				'name' => '',
				'languages' => 'view_only',
				'language_familys' => 'view_only',
		);	


}
