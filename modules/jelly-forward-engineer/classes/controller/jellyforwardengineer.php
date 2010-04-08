<?php defined('SYSPATH') or die('No direct script access.');

class Controller_JellyForwardEngineer extends Controller {
	
	
	private $datatype_map = array(
			'bit'						=>'String',
			'bit varying'				=>'String',
			'char'						=>'String',
			'char varying'				=>'String',
			'character'					=>'String',
			'character varying'			=>'String',
			'date'						=>'Timestamp',
			'dec'						=>'Float',
			'decimal'					=>'Float',
			'double precision'			=>'Float',
			'float'						=>'Float',
			'int'						=>'Integer',
			'integer'					=>'Integer',
			'interval'					=>'String',	// ?
			'national char'				=>'String',
			'national char varying'		=>'String',
			'national character'		=>'String',
			'national character varying'=>'String',
			'nchar'						=>'String',
			'nchar varying'				=>'String',
			'numeric'					=>'Float',
			'real'						=>'Float',
			'smallint'					=>'Integer',
			'time'						=>'Timestamp',
			'time with time zone'		=>'Timestamp',
			'timestamp'					=>'Timestamp',
			'timestamp with time zone'	=>'Timestamp',
			'varchar'					=>'String',

			// SQL:1999
			'binary large object'		=>'Text',
			'blob'						=>'Text',
			'boolean'					=>'Boolean',
			'char large object'			=>'Text',
			'character large object'	=>'Text',
			'clob'						=>'Text',
			'national character large object'	=>'Text',
			'nchar large object'		=>'Text',
			'nclob'						=>'Text',
			'time without time zone'	=>'Timestamp',
			'timestamp without time zone'	=>'Timestamp',

			// SQL:2003
			'bigint'					=>'Integer',

			// SQL:2008
			'binary'					=>'Text',
			'binary varying'			=>'Text',
			'varbinary'					=>'Text',
			
			
			'blob'						=>'Text',
			'bool'						=>'Boolean',
			'bigint unsigned'			=>'Integer',
			'datetime'					=>'Timestamp',
			'decimal unsigned'			=>'Float',
			'double'					=>'Float',
			'double precision unsigned'	=>'Float',
			'double unsigned'			=>'Float',
			'enum'						=>'Enum',
			'fixed'						=>'Float',
			'fixed unsigned'			=>'Float',
			'float unsigned'			=>'Float',
			'int unsigned'				=>'Integer',
			'integer unsigned'			=>'Integer',
			'longblob'					=>'Text',
			'longtext'					=>'Text',
			'mediumblob'				=>'Text',
			'mediumint'					=>'Integer',
			'mediumint unsigned'		=>'Integer',
			'mediumtext'				=>'Text',
			'national varchar'			=>'String',
			'numeric unsigned'			=>'Float',
			'nvarchar'					=>'String',
			'real unsigned'				=>'Float',
			'set'						=>'Enum',
			'smallint unsigned'			=>'Integer',
			'text'						=>'Text',
			'tinyblob'					=>'String',
			'tinyint'					=>'Integer',
			'tinyint unsigned'			=>'Integer',
			'tinytext'					=>'String',
			'year'						=>'Timestamp',
		);
		
	public function action_index() {
	
		$models = $this->_generateModels();
		
		echo View::factory('jelly_model_list')
					->set('models', $models)
					->render();
					
	}
	
	public function action_downloadModels() {
	
		$models = $this->_generateModels();
		
		
		$tmp_dir = sys_get_temp_dir();
		if( !$tmp_dir ) exit('could not find tmp directory');
		
		$tmp_dir .= '/jelly_models/';
		
		if(!is_dir($tmp_dir)) {
			mkdir($tmp_dir);
		}
		
		$archive = Archive::factory('zip');
		
		$files_to_cleanup = array();
		
		foreach( $models as $model ) {		
			$tmp_filename = $tmp_dir . $model->filename();
			$file_contents = View::factory('jelly_model_skeleton')->set('model', $model)->render();
			
			file_put_contents($tmp_filename, $file_contents);
			
			$files_to_cleanup[] = $tmp_filename;
			
			$archive->add($tmp_filename, $model->filename());
		}
		
		
		// output archive data
		$this->request->response = $archive->save();
		
		// offer archive as a download
		$this->request->send_file(NULL);
	}
	
	private function _generateModels() {					
					
		$tables = Database::instance()->list_tables();
		
		$models = array();
		
	/*
		

		foreach( $tables as $table ) {
			$columns = 
		}
		*/
		
		foreach( $tables as $table ) {
		
	//	$table = 'pages';
		
		
		
		
		
		$model = new Model_DBModel();
		$model->table = $table;
		
		$columns = Database::instance()->list_columns($model->table);
		
		
		
		// if the table is innodb, determine the foreign key references, otherwise guess them
		if( FALSE ) $foreign_key_check_mode = 'reference';
		else $foreign_key_check_mode = 'guess';
		
		
		foreach( $columns as $column ) {
			//echo Kohana::debug($column);
			$field = new Model_DBField();
			$field->raw_data = $column;
			
			$field->name = $column['column_name'];
			
			$field->type = $this->datatype_map[$column['data_type']];
			
			if( $field->name == 'id' ) {
				$field->type = 'Primary';
				$model->fields[$field->name] = $field;
				continue;
			}
			else {
				// check for foreign keys
				if( $foreign_key_check_mode == 'guess' ) {
					if( $prefix = StringManip::is_or_ends_with($field->name, 'id') ) {
						$matched_table = null;
						if( in_array( $prefix, $tables ) ) $matched_table = $prefix;
						else if( in_array( Inflector::plural($prefix), $tables ) ) $matched_table = Inflector::plural($prefix);
						
						if( $matched_table ) {
							// we have a possible foreign key here
							$field->type = 'BelongsTo';
							$field->name = $prefix;
							
							// add it to the table now and don't run any further rules
							$model->fields[$field->name] = $field;
							continue;
						}
					}
				}
				else {
					// use foreign key lookups
				}
			}
			
			
			
			if( isset($column['values']) ) $field->enum_choices = $column['values'];
						
			if( strpos($column['data_type'], 'tinyint') !== FALSE && isset($column['character_maximum_length']) && $column['character_maximum_length'] == 1 ) {
				// assume tinyint(1) fields are boolean
				$field->type = 'Boolean';
				unset($column['character_maximum_length']);
				unset($column['is_nullable']);
			}
			
			if( isset($column['column_default']) && $column['column_default'] ) {
				$field->options['default'] = $column['column_default'];
			}
			
			if( isset($column['character_maximum_length']) && ( $column['type'] != 'string' || $column['character_maximum_length'] < 255 ) ) {
				$field->rules['max_length'] = $column['character_maximum_length'];
			}
			
			if( isset($column['is_nullable']) && $column['is_nullable'] === FALSE ) {
				$field->rules['not_empty'] = NULL;
			}
			
			
			if( StringManip::is_or_ends_with($field->name, 'email') ) {
				$field->type = 'Email';
			}
			
			if( StringManip::is_or_ends_with($field->name, array('url','website') ) ) {
				$field->rules['url'] = NULL;
			}
			
			if( StringManip::is_or_ends_with($field->name, array('ip','ipaddress','ip_address') ) ) {
				$field->rules['ip'] = NULL;
			}
			
			if( StringManip::is_or_ends_with($field->name, array('telephone', 'tel', 'phone') ) ) {
				$field->rules['phone'] = NULL;
			}
			
			if( $field->type == 'Timestamp' && StringManip::contains($field->name, array('creation', 'created', 'added') ) ) {
				$field->options['auto_now_create'] = TRUE;
			}
			
			if( $field->type == 'Timestamp' && StringManip::contains($field->name, array('edited', 'modified', 'updated') ) ) {
				$field->options['auto_now_update'] = TRUE;
			}
			
			
			$model->fields[$field->name] = $field;
		}
		
		$models[] = $model;
		
		}
		
		return $models;
		
	}
	
}