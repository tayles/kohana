<?php defined('SYSPATH') or die('No direct script access.');

class DBNav {
	
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
		$schemata = DB::select(
							array('SCHEMA_NAME',					'name'),
							array('DEFAULT_CHARACTER_SET_NAME',		'charset'),
							array('DEFAULT_COLLATION_NAME',			'collation')
						)
						->from('information_schema.schemata')
						->as_object('DBNav_Schema')
						->execute();
		return $schemata;
	}
	
	public function tables($schema_name) {
		$tables = DB::select(
							array('TABLE_NAME',						'name'),
							array('ENGINE',							'engine'),
							array('TABLE_ROWS',						'num_rows'),
							array('AVG_ROW_LENGTH',					'avg_row_size'),
							array('DATA_LENGTH',					'data_size'),
							array('INDEX_LENGTH',					'index_size'),
							array('AUTO_INCREMENT',					'auto_incr_id'),
							array('UNIX_TIMESTAMP("CREATE_TIME")',	'date_created'),
							array('UNIX_TIMESTAMP("UPDATE_TIME")',	'date_updated'),
							array('UNIX_TIMESTAMP("CHECK_TIME")',	'date_checked'),
							array('TABLE_COLLATION',				'collation'),
							array('TABLE_COMMENT',					'comment')
						)
						->from('information_schema.tables')
						->where('table_schema', '=', $schema_name)
						->as_object('DBNav_Table')
						->execute();
						
		$table_arr = array();
		foreach( $tables as $table ) {
			$table->pre_process();
			$table_arr[$table->name] = $table;
		}
		
		return $table_arr;
	}
	
	public function columns($schema_name, $table_name) {
		$columns = DB::select(
							array('COLUMN_NAME',					'name'),
							array('COLUMN_DEFAULT',					'default'),
							array('IF(STRCMP("IS_NULLABLE",\'YES\'), 0, 1)',	'nullable'),
							array('DATA_TYPE',						'type'),
							array('COLUMN_TYPE',					'raw_type'),
							array('CHARACTER_MAXIMUM_LENGTH',		'length'),
							array('NUMERIC_PRECISION',				'numeric_precision'),
							array('NUMERIC_SCALE',					'numeric_scale'),
							array('CHARACTER_SET_NAME',				'charset'),
							array('COLLATION_NAME',					'collation'),
							array('COLUMN_KEY',						'keys'),
							array('EXTRA',							'extra'),
							array('COLUMN_COMMENT',					'comment')
						)
						->from('information_schema.columns')
						->where('table_schema', '=', $schema_name)
						->where('table_name', '=', $table_name)
						->as_object('DBNav_Column')
						->execute();
					
		// store in an assoc array so we can reference by column name
		$column_arr = array();
		foreach( $columns as $column ) {
			$column->pre_process();
			$column_arr[$column->name] = $column;
		}
		return $column_arr;
	}
	
	
	

	public function schema($schema_name) {
		$schema = DB::select(
							array('SCHEMA_NAME',					'name'),
							array('DEFAULT_CHARACTER_SET_NAME',		'charset'),
							array('DEFAULT_COLLATION_NAME',			'collation')
						)
						->from('information_schema.schemata')
						->where('SCHEMA_NAME', '=', $schema_name)
						->as_object('DBNav_Schema')
						->execute()
						->current();
		return $schema;
	}
	
	public function table($schema_name, $table_name) {
		$table = DB::select(
							array('TABLE_NAME',						'name'),
							array('ENGINE',							'engine'),
							array('TABLE_ROWS',						'num_rows'),
							array('AVG_ROW_LENGTH',					'avg_row_size'),
							array('DATA_LENGTH',					'data_size'),
							array('INDEX_LENGTH',					'index_size'),
							array('AUTO_INCREMENT',					'auto_incr_id'),
							array('UNIX_TIMESTAMP("CREATE_TIME")',	'date_created'),
							array('UNIX_TIMESTAMP("UPDATE_TIME")',	'date_updated'),
							array('UNIX_TIMESTAMP("CHECK_TIME")',	'date_checked'),
							array('TABLE_COLLATION',				'collation'),
							array('TABLE_COMMENT',					'comment')
						)
						->from('information_schema.tables')
						->where('table_schema', '=', $schema_name)
						->where('table_name', '=', $table_name)
						->as_object('DBNav_Table')
						->execute()
						->current();
		$table->pre_process();
		return $table;
	}
	
	public function rows($schema_name, $table_name, $page = 1, $per_page = 10) {
	
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
	
	
	public function indices($schema_name, $table_name) {
		$indices = DB::select(
							array('IF("NON_UNIQUE" = 0, 1, 0 )',	'unique'),
							array('INDEX_SCHEMA',					'schema'),
							array('INDEX_NAME',						'name'),
							array('SEQ_IN_INDEX',					'seq_in_index'),
							array('COLUMN_NAME',					'column_name'),
							array('COLLATION',						'collation'),
							array('CARDINALITY',					'cardinality'),
							array('SUB_PART',						'sub_part'),
							array('PACKED',							'packed'),
							array('IF(STRCMP("NULLABLE",\'YES\'), 0, 1)',	'nullable'),
							array('INDEX_TYPE',						'type'),
							array('COMMENT',						'comment')
						)
						->from('information_schema.statistics')
						->where('table_schema', '=', $schema_name)
						->where('table_name', '=', $table_name)
						->as_object('DBNav_Index')
						->execute();
		return $indices;
	}
  

}