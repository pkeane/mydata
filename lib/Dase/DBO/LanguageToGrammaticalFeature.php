<?php

require_once 'Dase/DBO/Autogen/LanguageToGrammaticalFeature.php';

class Dase_DBO_LanguageToGrammaticalFeature extends Dase_DBO_Autogen_LanguageToGrammaticalFeature 
{
		public $language;
		public $grammatical_feature;
		public $attributes = array(
				'language' => '',
				'grammatical_feature' => '',
				'answer' => '',
		);

}
