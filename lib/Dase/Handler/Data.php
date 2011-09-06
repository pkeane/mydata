<?php

class Dase_Handler_Data extends Dase_Handler
{
		public $resource_map = array(
				'{table}/list' => 'list',
				'{table}/{id}/rowdata' => 'rowdata',
				'{table}/{id}/{att}' => 'value',
				'{table}/{id}/edit/{att}/simple_form' => 'simple_form',
				'{table}/{id}/edit/{att}/one_form' => 'one_form',
		);

		protected function setup($r)
		{
				$this->user = $r->getUser();
		}

		public function getRowdata($r)
		{
				$t = new Dase_Template($r);
				$class = 'Dase_DBO_'.Dase_Util::camelize($r->get('table'));
				if (!class_exists($class)) {
						$r->renderError(404);
				}
				$obj = new $class($this->db);
				$obj->load($r->get('id'));
				$obj->inflate();
				$t->assign('item',$obj);
				$t->assign('table',$r->get('table'));
				$r->renderResponse($t->fetch('rowdata.tpl'));

		}

		public function postToValue($r)
		{
				$att = $r->get('att');
				$class = 'Dase_DBO_'.Dase_Util::camelize($r->get('table'));
				if (!class_exists($class)) {
						$r->renderError(404);
				}
				$obj = new $class($this->db);
				$obj->load($r->get('id'));
				$obj->$att = $r->get('value');
				$obj->update();
				$r->renderResponse('updated');
		}

		public function getSimpleForm($r)
		{
				$t = new Dase_Template($r);
				$att = $r->get('att');
				$class = 'Dase_DBO_'.Dase_Util::camelize($r->get('table'));
				if (!class_exists($class)) {
						$r->renderError(404);
				}
				$obj = new $class($this->db);
				$obj->load($r->get('id'));
				$value = $obj->$att;
				$t->assign('id',$r->get('id'));
				$t->assign('att',$att);
				$t->assign('table',$r->get('table'));
				$t->assign('value',$value);
				$r->renderResponse($t->fetch('simple_form.tpl'));

		}

		public function getOneForm($r)
		{
				$t = new Dase_Template($r);
				$att = $r->get('att');
				$class = 'Dase_DBO_'.Dase_Util::camelize($r->get('table'));
				if (!class_exists($class)) {
						$r->renderError(404);
				}
				$obj = new $class($this->db);
				$obj->load($r->get('id'));
				$obj->inflate();
				$attobj = $obj->$att;
				$attclass = Dase_DBO::getDBOClass($att);
				$attobjs = new $attclass($this->db);
				$t->assign('attobj',$attobj);
				$t->assign('attobjs',$attobjs->findAll());
				$r->renderResponse($t->fetch('one_form.tpl'));

		}

		public function getList($r) 
		{
				$start = 1;
				$max = 0;
				if ($r->get('max')) { 
						$max = $r->get('max');
				}
				if ($r->get('start')) {
						$start = $r->get('start');
				}

				$class = 'Dase_DBO_'.Dase_Util::camelize($r->get('table'));
				if (!class_exists($class)) {
						$r->renderError(404);
				}
				$t = new Dase_Template($r);
				$objs = new $class($this->db);
				if ($r->get('sort')) {
						$objs->orderBy($r->get('sort'));
						$t->assign('sort',$r->get('sort'));
				}
				$set = array();
				$i = 0;
				$skips = 0;

				$attributes = array();
				foreach ($objs->find() as $obj) {
						$i++;
						if ($start && $i < $start) {
								$skips++;
								continue;
						}
						if ($max && $i > $max+$skips) {
								continue;
						}
						$obj = clone($obj);
						$obj->inflate();
						//use one and only one inflated object to get class atts
						if (count($obj->attributes) && !count($attributes)) {
								$attributes = $obj->attributes;
						}
						$set[] = $obj;
				}
				$t->assign('attributes',$attributes);
				$pages = array();
				if ($max) {
						$num_pages = floor($i/$max);
				} else {
						$num_pages = 0;
				}
				if ($max && $i%$max) {
						$num_pages++;
						$t->assign('page_num',floor($start/$max)+1);
				}
				foreach (range(1,$num_pages) as $pnum) {
						$pages[$pnum]['num'] = $pnum;
						$pages[$pnum]['start'] = (($pnum-1)*$max) + 1;
						$pages[$pnum]['max'] = $max;
				}

				$end = $start + $max - 1;
				if ($end > $i || $max == 0) {
						$end = $i;
				}

				$t->assign('table',$r->get('table'));
				$t->assign('title',ucwords(str_replace('_',' ',$r->get('table'))));
				$t->assign('num_pages',$num_pages+1);
				$t->assign('pages',$pages);
				$t->assign('max',$max);
				$t->assign('start',$start);
				$t->assign('end',$end);
				$t->assign('count_all',$i);
				$t->assign('set',$set);
				$template_file = $r->get('table').'_list.tpl';
				if ($t->template_exists($template_file)) {
						$r->renderResponse($t->fetch($template_file));
				} else {
						$r->renderResponse($t->fetch('list.tpl'));
				}
		}
}

