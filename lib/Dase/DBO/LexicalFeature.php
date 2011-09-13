<?php

require_once 'Dase/DBO/Autogen/LexicalFeature.php';

class Dase_DBO_LexicalFeature extends Dase_DBO_Autogen_LexicalFeature 
{
		public $semantic_field;

		public function getName()
		{
				return $this->english_headword;
		}

}
