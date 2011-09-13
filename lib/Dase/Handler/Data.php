<?php

class Dase_Handler_Data extends Dase_Handler
{
		public $resource_map = array(
				'{table}/list' => 'list',
				'{table}/{id}' => 'item',
				'{table}/{id}/rowdata' => 'rowdata',
				'{table}/{id}/{att}' => 'value',
				'{table}/{id}/link/{att}/{att_id}' => 'link',
				'{table}/{id}/edit/{att}/simple_form' => 'simple_form',
				'{table}/{id}/edit/{att}/one_form' => 'one_form',
				'{table}/{id}/edit/{att}/many_form' => 'many_form',
		);

		protected function setup($r)
		{
				$this->user = $r->getUser();
		}

		public function deleteLink($r)
		{
				$class = 'Dase_DBO_'.Dase_Util::camelize($r->get('table'));
				if (!class_exists($class)) {
						$r->renderError(404);
				}
				$attclass = 'Dase_DBO_'.Dase_Util::camelize(rtrim($r->get('att'),'s'));
				if (!class_exists($attclass)) {
						$r->renderError(404);
				}
				$attobj = new $attclass($this->db);
				$attobj->load($r->get('att_id'));
				$attobj->delete();
				$r->renderResponse('deleted');
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
				//att to be set
				$att = $r->get('att');

				//this class
				$class = 'Dase_DBO_'.Dase_Util::camelize($r->get('table'));
				if (!class_exists($class)) {
						$r->renderError(404);
				}
				$obj = new $class($this->db);
				$obj->load($r->get('id'));

				//if simple, it'll be a value
				//if one, it'll be a foreign key
				$obj->set($att,$r->get('value'));
				if (!$obj->$att) {
						$r->renderResponse('no change');
				}
				$obj->update();
				$r->renderResponse('updated');
		}

		public function deleteValue($r)
		{
				$att = $r->get('att');
				$class = 'Dase_DBO_'.Dase_Util::camelize($r->get('table'));
				if (!class_exists($class)) {
						$r->renderError(404);
				}
				$obj = new $class($this->db);
				$obj->load($r->get('id'));

				$obj->$att = '';
				$obj->update();
				$r->renderResponse('deleted');
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
				if ($attobj) {
						$t->assign('value',$attobj->id);
				} else {
						$t->assign('value','');
				}
				$t->assign('id',$r->get('id'));
				$t->assign('att',$att);
				$t->assign('table',$r->get('table'));
				$attclass = Dase_DBO::getDBOClass($att);
				$attobjs = new $attclass($this->db);
				$t->assign('attobjs',$attobjs->findAll());
				$r->renderResponse($t->fetch('one_form.tpl'));
		}

		public function getManyForm($r)
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
				$t->assign('item',$obj);
				$t->assign('id',$r->get('id'));
				$t->assign('att',$att);
				$t->assign('table',$r->get('table'));
				$r->renderResponse($t->fetch('many_form.tpl'));
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

				$attributes = array();
				$check_obj = new $class($this->db);
				$check_obj->inflate();
				$attributes = $check_obj->attributes;
				$filters = array();
				foreach ($attributes as $k => $v) {
						if ('one' == $v) {
								$attclass = 'Dase_DBO_'.Dase_Util::camelize(rtrim($k,'s'));
								if (class_exists($attclass)) {
										$attobj = new $attclass($this->db);
										$filters[rtrim($k,'s')] = $attobj->findAll(1);
								}
						}
				}

				$t = new Dase_Template($r);
				$objs = new $class($this->db);

				$_filters = array();
				foreach ($filters as $k => $v) {
						if ($r->get($k)) {
								$att_id = $k.'_id';
								$objs->$att_id = $r->get($k);
								$_filters[$k] = $r->get($k);
						}
				}
				$t->assign('_filters',$_filters);

				if ($r->get('sort')) {
						$objs->orderBy($r->get('sort'));
						$t->assign('sort',$r->get('sort'));
				}

				$fsort_map = array();
				if ($r->get('fsort')) {
						$fsort_class = 'Dase_DBO_'.Dase_Util::camelize($r->get('fsort'));
						if (class_exists($fsort_class)) {
								$fsort_objs = new $fsort_class($this->db);
								$fsort_objs->orderBy($fsort_objs->getNameField());
								$i = 0;
								foreach ($fsort_objs->find() as $fsort_obj) {
										$i++;
										$fsort_map[$fsort_obj->id] = $i;
								}
						}
				}

				$fullset = array();
				foreach ($objs->find() as $obj) {
						$obj = clone($obj);
						if ($r->get('fsort')) {
								$fid_field = $r->get('fsort').'_id';
								$fkey = $obj->$fid_field;
								if ($fkey) {
										$obj->sort_key = $fsort_map[$fkey];
								}
						}
						$fullset[] = $obj;
				}

				if ($r->get('fsort')) {
						usort($fullset, array($class, "compare"));
						$t->assign('fsort',$r->get('fsort'));
				}

				$set = array();
				$i = 0;
				$skips = 0;

				foreach ($fullset as $obj) {
						$i++;
						if ($start && $i < $start) {
								$skips++;
								continue;
						}
						if ($max && $i > $max+$skips) {
								continue;
						}
						$obj->inflate();
						$set[] = $obj;
				}

				$t->assign('attributes',$attributes);
				$t->assign('filters',$filters);
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

		public function getItem($r) 
		{
				$table = rtrim($r->get('table'),'s');
				$class = 'Dase_DBO_'.Dase_Util::camelize($table);
				if (!class_exists($class)) {
						$r->renderError(404);
				}
				$item = new $class($this->db);
				$item->load($r->get('id'));
				$item->inflate();
				$t = new Dase_Template($r);
				$t->assign('table',$table);
				$t->assign('title',ucwords(str_replace('_',' ',$table).' '.$item->name));
				$t->assign('item',$item);
				$template_file = $table.'.tpl';
				if ($t->template_exists($template_file)) {
						$r->renderResponse($t->fetch($template_file));
				} else {
						$r->renderResponse($t->fetch('item.tpl'));
				}
		}
}

