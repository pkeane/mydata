<?php
require_once 'Dase/DB.php';

class Dase_DBO_Exception extends Exception {}

class Dase_DBO implements IteratorAggregate
{
	private $fields = array(); 
	private $table;
	protected $limit;
	public $sort_key; //for runtime foreign obj sort
	protected $order_by;
	protected $qualifiers = array();
	public $attributes = array();
	public $bind = array();
	public $config;
	public $db;
	public $id = 0;
	public $sql;

	function __construct($db, $table, $fields )
	{
		$this->db = $db;
		//so any DBO has a copy of config
		$this->config = $db->config;
		$this->table = $db->table_prefix.$table;
		foreach( $fields as $key ) {
			$this->fields[ $key ] = null;
		}
	}

	public static function getDBOClass($table)
	{
			return 'Dase_DBO_'.Dase_Util::camelize($table);
	}


	/* This is the static comparing function: */
	static function compare($a, $b)
	{
			$al = strtolower($a->sort_key);
			$bl = strtolower($b->sort_key);
			if ($al == $bl) {
					return 0;
			}
			return ($al > $bl) ? +1 : -1;
	}

	public function getName()
	{
			if ($this->title) {
					return $this->title;
			}
			if ($this->text) {
					return $this->text;
			}
			if ($this->id) {
					return $this->id;
			}
	}

	public function getNameField()
	{
			if ($this->hasMember('name')) {
					return 'name';
			}
			if ($this->hasMember('title')) {
					return 'title';
			}
			if ($this->hasMember('text')) {
					return 'text';
			}
	}

	public function setName($val)
	{
			if ($this->hasMember('name')) {
					return $this->name = $val;
			}
			if ($this->hasMember('title')) {
					return $this->title = $val;
			}
			if ($this->hasMember('text')) {
					return $this->text = $val;
			}
	}

	public function inflate($foreign_sort_key='') 
	{
			foreach ($this->getFieldNames() as $field) {
					if ('_id' != substr($field,-3)) {
							$this->attributes[$field] = 'simple';
					}
					if ('created' == $field) {
							$this->attributes[$field] = 'meta';
					}
					if ('created_by' == $field) {
							$this->attributes[$field] = 'meta';
					}
			}
			foreach ($this->getMembers()  as $k => $mem) {
					if (is_array($mem)) {
							//allows us to specify att type in class
							if (!isset($this->attributes[$k]) || !$this->attributes[$k]) {
									$this->attributes[$k] = 'many';
							}
							$table = rtrim($k,'s');
							$class = Dase_DBO::getDBOClass($table);
							$obj = new $class($this->db);
							$id_field = $this->getTable().'_id';
							$obj->$id_field = $this->id;
							$this->$k = $obj->findAll(1);
					} else {
							$this->attributes[$k] = 'one';
							$id_field = $k.'_id';
							if ($this->$id_field) {
									$class = Dase_DBO::getDBOClass($k);
									$obj = new $class($this->db);
									$obj->load($this->$id_field);
									$this->$k = $obj;
									if ($foreign_sort_key == $k) {
											$this->sort_key = $obj->name;
									}
							}
					}
			}
	}

	public function getMembers()
	{
			$pmembers = get_class_vars(get_parent_class($this));
			$members = get_class_vars(get_class($this));
			foreach ($pmembers as $k => $v) {
					unset($members[$k]);
			}
			return $members;
	}

	public function getTable($include_prefix = true)
	{
		if ($include_prefix) {
		return $this->table;
		} else {
			$prefix = $this->db->table_prefix;
			return substr_replace($this->table,'',0,strlen($prefix));
		}
	}

	public function __get( $key )
	{
		if ( array_key_exists( $key, $this->fields ) ) {
			return $this->fields[ $key ];
		}
		//automatically call accessor method if it exists
		$classname = get_class($this);
		$method = 'get'.Dase_Util::camelize($key);
		if (method_exists($classname,$method)) {
			return $this->{$method}();
		}	
	}

	public function __set( $key, $value )
	{
		if ( array_key_exists( $key, $this->fields ) ) {
			$this->fields[ $key ] = $value;
			return true;
		}
		$classname = get_class($this);
		$method = 'set'.Dase_Util::camelize($key);
		if (method_exists($classname,$method)) {
			return $this->{$method}($value);
		}	
		return false;
	}

	//magic __set does not seem to work w/ variable variables, thus this
	public function set( $key, $value )
	{
		if ( array_key_exists( $key, $this->fields ) ) {
			$this->fields[ $key ] = $value;
			return true;
		}
		$classname = get_class($this);
		$method = 'set'.Dase_Util::camelize($key);
		if (method_exists($classname,$method)) {
			return $this->{$method}($value);
		}	
		return false;
	}

	private function _dbGet() {
		try {
			return $this->db->getDbh();
		} catch (PDOException $e) {
			throw new PDOException($e->getMessage());
		}
	}

	function getFieldNames() {
		return array_keys($this->fields);
	}

	function hasMember($key)
	{
		if ( array_key_exists( $key, $this->fields ) ) {
			return true;
		} else {
			return false;
		}
	}

	function setLimit($limit)
	{
		$this->limit = $limit;
	}

	function orderBy($ob)
	{
		$this->order_by = $ob;
	}

	function addWhere($field,$value,$operator)
	{
		if ( 
			array_key_exists( $field, $this->fields) &&
			in_array(strtolower($operator),array('is not','is','ilike','like','not ilike','not like','=','!=','<','>','<=','>='))
		) {
			$this->qualifiers[] = array(
				'field' => $field,
				'value' => $value,
				'operator' => $operator
			);
		} else {
			throw new Dase_DBO_Exception('addWhere problem');
		}
	}

	function __toString()
	{
		$members = '';
		$table = $this->table;
		$id = $this->id;
		foreach ($this->fields as $key => $value) {
			$members .= "$key: $value\n";
		}
		$out = "--$table ($id)--\n$members\n";
		return $out;
	}

	function load( $id )
	{
		if (!$id) {return false;}
		$this->id = $id;
		$db = $this->_dbGet();
		$table = $this->table;
		$sql = "SELECT * FROM $table WHERE id=:id";
		$sth = $db->prepare($sql);
		if (! $sth) {
			$errs = $db->errorInfo();
			if (isset($errs[2])) {
				throw new Dase_DBO_Exception($errs[2]);
			}
		}
		Dase_Log::debug(LOG_FILE,$sql . ' /// '.$id);
		$sth->setFetchMode(PDO::FETCH_INTO, $this);
		$sth->execute(array( ':id' => $this->id));
		if ($sth->fetch()) {
			return $this;
		} else {
			return false;
		}
	}

	function insert($seq = '')
	{ //postgres needs id specified
		if ('pgsql' == $this->db->getDbType()) {
			if (!$seq) {
				//beware!!! fix this after no longer using DB_DataObject
				//$seq = $this->table . '_id_seq';
				$seq = $this->table . '_seq';
			}
			//$id = "nextval('$seq'::text)";
			$id = "nextval(('public.$seq'::text)::regclass)"; 	
		} elseif ('sqlite' == $this->db->getDbType()) {
			$id = 'null';
		} else {
			$id = 0;
		}
		$dbh = $this->db->getDbh();
		$fields = array('id');
		$inserts = array($id);
		foreach( array_keys( $this->fields ) as $field )
		{
			$fields []= $field;
			$inserts []= ":$field";
			$bind[":$field"] = $this->fields[ $field ];
		}
		$field_set = join( ", ", $fields );
		$insert = join( ", ", $inserts );
		//$this->table string is NOT tainted
		$sql = "INSERT INTO ".$this->table. 
			" ( $field_set ) VALUES ( $insert )";
		$sth = $dbh->prepare( $sql );
		if (! $sth) {
			$error = $db->errorInfo();
			throw new Exception("problem on insert: " . $error[2]);
			exit;
		}
		if ($sth->execute($bind)) {
			$last_id = $dbh->lastInsertId($seq);
			$this->id = $last_id;
			Dase_Log::debug(LOG_FILE,$sql." /// last insert id = $last_id");
			return $last_id;
		} else { 
			$error = $sth->errorInfo();
			throw new Exception("could not insert: " . $error[2]);
		}
	}

	function getMethods()
	{
		$class = new ReflectionClass(get_class($this));
		return $class->getMethods();
	}

	function findOne()
	{
		$this->setLimit(1);
		$set = $this->find()->fetchAll();
		if (count($set)) {
			return $set[0];
		}
		return false;
	}

	function findAll($return_empty_array=false)
	{
		$set = array();
		$iter = $this->find();
		foreach ($iter as $it) {
			$set[$it->id] = clone($it);
		}
		if (count($set)) {
			return $set;
		} else {
			if ($return_empty_array) {
				return $set;
			}
			return false;
		}
	}

	function find()
	{
		//finds matches based on set fields (omitting 'id')
		//returns an iterator
		$dbh = $this->db->getDbh();
		$sets = array();
		$bind = array();
		$limit = '';
		foreach( array_keys( $this->fields ) as $field ) {
			if (isset($this->fields[ $field ]) 
				&& ('id' != $field)) {
					$sets []= "$field = :$field";
					$bind[":$field"] = $this->fields[ $field ];
				}
		}
		if (isset($this->qualifiers)) {
			//work on this
			foreach ($this->qualifiers as $qual) {
				$f = $qual['field'];
				$op = $qual['operator'];
				//allows is to add 'is null' qualifier
				if ('null' == $qual['value']) {
					$v = $qual['value'];
				} else {
					$v = $dbh->quote($qual['value']);
				}
				$sets[] = "$f $op $v";
			}
		}
		$where = join( " AND ", $sets );
		if ($where) {
			$sql = "SELECT * FROM ".$this->table. " WHERE ".$where;
		} else {
			$sql = "SELECT * FROM ".$this->table;
		}
		if (isset($this->order_by)) {
			$sql .= " ORDER BY $this->order_by";
		}
		if (isset($this->limit)) {
			$sql .= " LIMIT $this->limit";
		}
		$sth = $dbh->prepare( $sql );
		if (!$sth) {
			throw new PDOException('cannot create statement handle');
		}

		//pretty logging
		$log_sql = $sql;
		foreach ($bind as $k => $v) {
			$log_sql = preg_replace("/$k/","'$v'",$log_sql,1);
		}
		Dase_Log::debug(LOG_FILE,'[DBO find] '.$log_sql);

		$sth->setFetchMode(PDO::FETCH_INTO,$this);
		$sth->execute($bind);
		//NOTE: PDOStatement implements Traversable. 
		//That means you can use it in foreach loops 
		//to iterate over rows:
		// foreach ($thing->find() as $one) {
		//     print_r($one);
		// }
		return $sth;
	}

	function findCount()
	{
		$dbh = $this->db->getDbh();
		$sets = array();
		$bind = array();
		foreach( array_keys( $this->fields ) as $field ) {
			if (isset($this->fields[ $field ]) 
				&& ('id' != $field)) {
					$sets []= "$field = :$field";
					$bind[":$field"] = $this->fields[ $field ];
				}
		}
		if (isset($this->qualifiers)) {
			//work on this
			foreach ($this->qualifiers as $qual) {
				$f = $qual['field'];
				$op = $qual['operator'];
				//allows is to add 'is null' qualifier
				if ('null' == $qual['value']) {
					$v = $qual['value'];
				} else {
					$v = $dbh->quote($qual['value']);
				}
				$sets[] = "$f $op $v";
			}
		}
		$where = join( " AND ", $sets );
		if ($where) {
			$sql = "SELECT count(*) FROM ".$this->table. " WHERE ".$where;
		} else {
			$sql = "SELECT count(*) FROM ".$this->table;
		}
		$sth = $dbh->prepare( $sql );
		if (!$sth) {
			throw new PDOException('cannot create statement handle');
		}
		$log_sql = $sql;
		foreach ($bind as $k => $v) {
			$log_sql = preg_replace("/$k/","'$v'",$log_sql,1);
		}
		Dase_Log::debug(LOG_FILE,'[DBO findCount] '.$log_sql);
		$sth->execute($bind);
		//Dase_Log::debug(LOG_FILE,'DB ERROR: '.print_r($sth->errorInfo(),true));
		return $sth->fetchColumn();
	}

	function update()
	{
		$dbh = $this->db->getDbh();
		foreach( $this->fields as $key => $val) {
			if ('timestamp' != $key || !is_null($val)) { //prevents null timestamp as update
				$fields[]= $key." = ?";
				$values[]= $val;
			}
		}
		$set = join( ",", $fields );
		$sql = "UPDATE {$this->{'table'}} SET $set WHERE id=?";
		$values[] = $this->id;
		$sth = $dbh->prepare( $sql );
		Dase_Log::debug(LOG_FILE,$sql . ' /// ' . join(',',$values));
		if (!$sth->execute($values)) {
			$errs = $sth->errorInfo();
			if (isset($errs[2])) {
				Dase_Log::debug(LOG_FILE,"updating error: ".$errs[2]);
				//throw new Dase_DBO_Exception('could not update '. $errs[2]);
			}
		} else {
			return true;
		}
	}

	function delete()
	{
		$dbh = $this->db->getDbh();
		$sth = $dbh->prepare(
			'DELETE FROM '.$this->table.' WHERE id=:id'
		);
		Dase_Log::debug(LOG_FILE,"deleting id $this->id from $this->table table");
		return $sth->execute(array( ':id' => $this->id));
		//probably need to destroy $this here
	}

	//implement SPL IteratorAggregate:
	//now simply use 'foreach' to iterate 
	//over object properties
	public function getIterator()
	{
		return new ArrayObject($this->fields);
	}

	public function asArray()
	{
		foreach ($this as $k => $v) {
			$my_array[$k] = $v;
		}
		return $my_array;
	}

	public function asJson()
	{
		Dase_Json::get($this->asArray());
	}
}
