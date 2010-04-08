<?php defined('SYSPATH') or die('No direct script access.');

class DBNav_Table extends Model {

	public $name, $engine, $num_rows, $avg_row_length, $data_length, $index_length, $auto_incr, $created, $updated, $checked, $collation, $comment;
	
	public $columns;

}
