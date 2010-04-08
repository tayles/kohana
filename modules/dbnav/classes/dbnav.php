<?php defined('SYSPATH') or die('No direct script access.');

class DBNav {

	public $schemas;
	
	public $per_page = 10;
	
	public static function instance() {
		static $db = null;
		if ( $db == null )
		  $db = new DBNav();
		return $db;
	  }

  private function __construct() {
	
  }
  
	public function schemata() {
		$schemata = DB::query(Database::SELECT, 'SELECT SCHEMA_NAME as name, DEFAULT_CHARACTER_SET_NAME as charset, DEFAULT_COLLATION_NAME as collation FROM information_schema.schemata s')
					->as_object('DBNav_Schema')
					->execute();
		return $schemata;
	}
	
	public function tables($schema_name) {
		$tables = DB::query(Database::SELECT, 'SELECT TABLE_NAME as name, ENGINE as engine, TABLE_ROWS as num_rows, AVG_ROW_LENGTH as avg_row_length, DATA_LENGTH as data_length, INDEX_LENGTH as index_length, AUTO_INCREMENT as auto_incr, UNIX_TIMESTAMP(CREATE_TIME) as created, UNIX_TIMESTAMP(UPDATE_TIME) as updated, UNIX_TIMESTAMP(CHECK_TIME) as checked, TABLE_COLLATION as collation, TABLE_COMMENT as comment FROM information_schema.tables t WHERE t.table_schema = :schema')
					->param(':schema', $schema_name)
					->as_object('DBNav_Table')
					->execute();
		return $tables;
	}

	public function schema($schema_name) {
		$schema = DB::query(Database::SELECT, 'SELECT SCHEMA_NAME as name, DEFAULT_CHARACTER_SET_NAME as charset, DEFAULT_COLLATION_NAME as collation FROM information_schema.schemata s WHERE SCHEMA_NAME = :schema')
					->param(':schema', $schema_name)
					->as_object('DBNav_Schema')
					->execute()->current();
		return $schema;
	}
	
	public function table($table_name) {
		$tables = DB::query(Database::SELECT, 'SELECT TABLE_NAME as name, ENGINE as engine, TABLE_ROWS as num_rows, AVG_ROW_LENGTH as avg_row_length, DATA_LENGTH as data_length, INDEX_LENGTH as index_length, AUTO_INCREMENT as auto_incr, UNIX_TIMESTAMP(CREATE_TIME) as created, UNIX_TIMESTAMP(UPDATE_TIME) as updated, UNIX_TIMESTAMP(CHECK_TIME) as checked, TABLE_COLLATION as collation, TABLE_COMMENT as comment FROM information_schema.tables t WHERE t.table_schema = :schema AND t.table_name = :table')
					->param(':schema', 'pubjury')
					->param(':table', $table_name)
					->as_object('DBNav_Table')
					->execute()->current();
		return $tables;
	}
	
	public function rows($table_name, $page = 1, $per_page = 10) {
	
		$offset = ($page - 1) * $per_page;
	
		$total_count = DB::select(DB::expr('COUNT(*) AS mycount'))->from($table_name)->execute()->get('mycount');

	
		$rows = DB::select()
					->from($table_name)
					->limit($per_page)
					->offset($offset)
					->as_object('DBNav_Record')
					->execute();
					
		return array( $total_count, $rows );
	}
  

}