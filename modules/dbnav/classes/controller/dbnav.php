<?php defined('SYSPATH') or die('No direct script access.');

class Controller_DBNav extends Controller_Template {
	
	public $template = 'dbnav/template';
	
	public function before() {
		parent::before();
		
		$this->dbnav = DBNav::instance();
		
		$this->schema = $this->request->param('schema');
		$this->table = $this->request->param('table');
		
		$this->page = $this->request->param('page');
		$this->per_page = 10;
		
		echo Kohana::debug($this->schema);
		echo Kohana::debug($this->table);
	}
	
	public function action_index() {
	
		if( $this->schema != NULL ) {
			$this->action_schema();
		}
		else {
	
			$this->template->content = View::factory('dbnav/schema/list')
						->bind('schemata', $schemata);
			
			//$schemata = $this->dbnav->schemas();
			
			$schemata = $this->dbnav->schemata();
		}
	}
	
	public function action_schema() {
		$this->template->content = View::factory('dbnav/table/list')
					->bind('schema', $schema)
					->bind('tables', $tables)
					->bind('tbl_view', $tbl_view);

		$schema = $this->dbnav->schema($this->schema);
		$tables = $this->dbnav->tables($this->schema);
		
		echo Kohana::debug($schema);
		
		$tbl_view = Table::factory($tables)
					->set_footer('footerrrr')
					->set_column_titles(Table::AUTO)
					#->set_column_titles(array('id' => 'ID', 'select' => Form::checkbox('row_selectall', 1, false), 'lat' => 'Lat', 'lng' => 'Lng'))
					#->add_column('select', 'id')
					#->set_callback(array($this,'add_checkbox'), 'column', 'select')
					#->add_column('opts');
					#->set_callback(array($this,'add_opts'), 'column', 'opts');
					#->set_column_filter(array('select', 'lat', 'lng'));
					;
		
	}
	
	public function action_table() {
		$this->template->content = View::factory('dbnav/table/browse')
					->bind('schema', $schema)
					->bind('table', $table)
					->bind('rows', $rows)
					->bind('pagination', $pagination)
					->bind('tbl_view', $tbl_view);

		$schema = $this->dbnav->schema($this->schema);
					
		$table = $this->dbnav->table($this->table);
					
		list( $total_count, $rows ) = $this->dbnav->rows($this->table, $this->page, $this->per_page);
		
		echo Kohana::debug(Database::instance());
		
		$tbl_view = Table::factory($rows)
					->set_footer('footerrrr')
					->set_column_titles(Table::AUTO)
					#->set_column_titles(array('id' => 'ID', 'select' => Form::checkbox('row_selectall', 1, false), 'lat' => 'Lat', 'lng' => 'Lng'))
					#->add_column('select', 'id')
					#->set_callback(array($this,'add_checkbox'), 'column', 'select')
					#->add_column('opts');
					#->set_callback(array($this,'add_opts'), 'column', 'opts');
					#->set_column_filter(array('select', 'lat', 'lng'));
					;
					
		$pagination = Pagination::factory(array(
						'total_items'    => $total_count,
						'items_per_page' => $this->per_page,
					));

		
	}

}
