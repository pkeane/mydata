<?php

require_once 'Dase/DBO.php';

/*
 * DO NOT EDIT THIS FILE
 * it is auto-generated by the
 * script 'bin/class_gen.php
 * 
 */

class Dase_DBO_Autogen_LanguageToGrammaticalFeature extends Dase_DBO 
{
	public function __construct($db,$assoc = false) 
	{
		parent::__construct($db,'language_to_grammatical_feature', array('language_id','grammatical_feature_id','bib_source','bib_source_pages','answer','original_form','phonemicized_form','etymology_note','phonology_note','grammatical_note','general_note'));
		if ($assoc) {
			foreach ( $assoc as $key => $value) {
				$this->$key = $value;
			}
		}
	}
}