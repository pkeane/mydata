<?php

//since loan_source_language points to language
class Dase_DBO_LoanSourceLanguage extends Dase_DBO_Language {}

require_once 'Dase/DBO/Autogen/LexicalData.php';

class Dase_DBO_LexicalData extends Dase_DBO_Autogen_LexicalData 
{
		public $language;
		public $lexical_feature;
		public $relationship_type;
		public $loan_source_language;
		public $attributes = array(
				'language' => '',
				'lexical_feature' => '',
				'original_form' => '',
				'phonemicized_form' => '',
				'original_form' => '',
				'loan_source_language' => '',
		);

}
