<?php defined('SYSPATH') or die('No direct script access.');

class Controller_DBNav extends Controller_Template {
	
	public $template = 'dbnav/template';
	
	
	private $schema, $table;
	
	public function before() {
		parent::before();
		
		$this->dbnav = DBNav::instance();
		
		$this->schema_name = $this->request->param('schema');
		$this->table_name = $this->request->param('table');
		
		
		
		if( $this->schema_name ) {
			$this->schema = $this->dbnav->schema($this->schema_name);
			echo Kohana::debug($this->schema->name);
		}
		
		if( $this->table_name ) {
			$this->table = $this->dbnav->table($this->schema->name, $this->table_name);
			echo Kohana::debug($this->table->name);
		}
		
		$this->page = $this->request->param('page');
		$this->per_page = 20;
	}
	
	public function action_index() {
	
		if( $this->schema_name != NULL ) {
			$this->action_schema();
		}
		else {
	
			$this->template->content = View::factory('dbnav/schema/list')
						->bind('schemata', $schemata)
						->bind('tbl_view', $tbl_view);
			
			//$schemata = $this->dbnav->schemas();
			
			$schemata = $this->dbnav->schemata();
			
			$tbl_view = Table::factory('DBNav_Admin', $schemata)
							->set_column_filter(array('name', 'charset', 'collation'));
		}
	}
	
	public function action_schema() {
		$this->template->content = View::factory('dbnav/table/list')
					#->bind('schema', $this->schema)
					->bind('tables', $tables)
					->bind('tbl_view', $tbl_view);

		$tables = $this->dbnav->tables($this->schema->name);
		
		$tbl_view = Table::factory('DBNav_Admin', $tables)
							->set_column_filter(array('name', 'num_rows', 'auto_incr_id', 'data_size', 'index_size', 'total_size', 'date_created', 'date_updated', 'date_checked', 'engine', 'collation', 'comment'));
	}
	
	public function action_table() {
		$this->template->content = View::factory('dbnav/table/browse')
					#->bind('schema', $this->schema)
					#->bind('table', $this->table)
					->bind('columns', $columns)
					->bind('rows', $rows)
					->bind('pagination', $pagination)
					->bind('tbl_view', $tbl_view);
					
		list( $total_count, $rows ) = $this->dbnav->rows($this->schema->name, $this->table->name, $this->page, $this->per_page);
		
		$columns = $this->dbnav->columns($this->schema->name, $this->table->name);
		
		$render_heading = function($column_name) {
			return Html::anchor('?sort=' . $column_name, ucwords(Inflector::humanize($column_name)));
		};
		
		$tbl_view = Table::factory('DBNav_Decorated', $rows)
							->set_user_data('sort', array('id', 'asc'))
							->set_user_data('schema', $this->schema)
							->set_user_data('table', $this->table)
							->set_user_data('columns', $columns)
							->set_column_titles(array_merge(array('<input type="checkbox" id="select_all_ids_head" class="select_all_ids" />', ''), array_map($render_heading, array_keys($columns))))
							->set_column_filter(array_merge(array('dbnav_select', 'dbnav_options'), array_keys($columns)));
					
		$pagination = Pagination::factory(array(
						'total_items'    => $total_count,
						'items_per_page' => $this->per_page,
					));
	}
	
	public function action_columns() {
		$this->template->content = View::factory('dbnav/column/list')
					#->bind('schema', $this->schema)
					#->bind('table', $this->table)
					->bind('columns', $columns)
					->bind('tbl_view', $tbl_view);

		$columns = $this->dbnav->columns($this->schema->name, $this->table->name);
		
		//echo Kohana::debug(Database::instance());
		
		$tbl_view = Table::factory('DBNav_Admin', $columns)
							->set_column_filter(array('name', 'keys', 'type', 'length', 'auto_incr', 'nullable', 'default', 'unsigned', 'zerofill', 'numeric_precision', 'numeric_scale', 'extra', 'collation', 'charset', 'comment'));
	}
	
	public function action_indices() {
		$this->template->content = View::factory('dbnav/index/list')
					->bind('indices', $indices)
					->bind('tbl_view', $tbl_view);

		$indices = $this->dbnav->indices($this->schema->name, $this->table->name);
		
		$tbl_view = Table::factory('DBNav_Admin', $indices)
							->set_column_filter(array('name', 'unique', 'nullable', 'column_name', 'seq_in_index', 'collation', 'cardinality', 'sub_part', 'packed', 'type', 'comment'));
	}
	
	public function action_edit() {
		$this->template->content = View::factory('dbnav/table/edit')
					#->bind('schema', $this->schema)
					#->bind('table', $this->table)
					->bind('columns', $columns)
					->bind('tbl_view', $tbl_view);

		$columns = $this->dbnav->columns($this->schema->name, $this->table->name);
		
		//echo Kohana::debug(Database::instance());
		
		$tbl_view = Table::factory('DBNav_Admin', $columns);
	}
	
	public function after() {
		$this->template->schema = $this->schema;
		$this->template->content->schema = $this->schema;
		$this->template->table = $this->table;
		$this->template->content->table = $this->table;
		
		parent::after();
	}

}
