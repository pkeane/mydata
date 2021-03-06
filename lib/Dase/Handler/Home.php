<?php

class Dase_Handler_Home extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'home',
	);

	protected function setup($r)
	{
		$this->user = $r->getUser();
	}

	public function getHome($r) 
	{
		$t = new Dase_Template($r);
		$r->renderResponse($t->fetch('home.tpl'));
	}
}

