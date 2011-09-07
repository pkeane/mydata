<?php

require_once 'Dase/DBO/Autogen/GrammaticalFeatureToCaseStudyArea.php';

class Dase_DBO_GrammaticalFeatureToCaseStudyArea extends Dase_DBO_Autogen_GrammaticalFeatureToCaseStudyArea 
{
		public $grammatical_feature;
		public $case_study_area;

		public function getName()
		{
				$this->case_study_area = new Dase_DBO_CaseStudyArea($this->db);
				$this->case_study_area->load($this->case_study_area_id);
				return $this->case_study_area->name;
		}
}
