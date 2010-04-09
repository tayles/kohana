<?php defined('SYSPATH') OR die('No direct access allowed.');

class DBNav_Admin_Table extends Table {

	// set all properties in the constructor
		public function __construct($data) {
			$this->set_body_data($data);
			$this->set_column_titles(Table::AUTO);
			#$this->set_row_titles(array_shift(array_keys($this->body_data[0])));
		}

		protected function _generate_body_cell($row, $column_name) {
			
			$val = $this->body_data[$row][$column_name];
			
			if( in_array( $column_name, array('auto_incr', 'nullable', 'unsigned', 'zerofill', 'unique') ) ) {
				$val_mod = '<span class="boolean">' . ($val ? '&#10003;' : '&#10007;') . '</span>';
			}
			else if( in_array($column_name, array('auto_incr_id', 'num_rows', 'length', 'numeric_precision', 'numeric_scale', 'seq_in_index', 'cardinality') ) ) {
				$val_mod = number_format($val);
				//$val_mod = Num::format($val, 0);
			}
			else if( in_array($column_name, array('data_size', 'index_size', 'total_size') ) ) {
				$val_mod = Text::bytes($val, NULL, '%01.0f <em>%s</em>');
			}
			else if( $column_name == 'comment' ) {
				$val_mod = '<em>' . $val . '</em>';
			}
			else if( Text::contains($column_name, 'date') ) {
				$val_mod = '<span class="date time">' . date( Kohana::config('dbnav.format.date') . ' ' . Kohana::config('dbnav.format.time'), $val) . '</span>';
			}
			
			
			if( $column_name == 'name' ) {
				$val_mod = Html::anchor(array('dbnav', $val), $val);
			}
			
			if( is_null($val) ) {
				$val_mod = '<em>NULL</em>';
			}
			
			return '<td>' . (isset($val_mod) ? $val_mod : $val) . '</td>';
		}
}