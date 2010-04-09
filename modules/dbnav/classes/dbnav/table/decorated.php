<?php defined('SYSPATH') OR die('No direct access allowed.');

class DBNav_Decorated_Table extends DBNav_Admin_Table {



	// set all properties in the constructor
		public function __construct($data) {
			$this->set_body_data($data)
					->add_column('dbnav_select')
					->set_footer('&#8598; with selected: ' . Html::anchor('#', 'Edit') . ' ' . Html::anchor('#', 'Delete'));
			#$this->set_row_titles(array_shift(array_keys($this->body_data[0])));
		}
		
		protected function _generate_body_cell($row, $column_name) {
			
			if( $column_name == 'dbnav_select' ) {
				return '<td><input type="checkbox" name="ids[]" id="idval_' . $row . '" value="idval" /></td>';
			}
			else if( $column_name == 'dbnav_options' ) {
				return '<td>' . Html::anchor('#', 'Edit') . ' ' . Html::anchor('#', 'Delete') . '</td>';
			}
			
			$val = $this->body_data[$row][$column_name];
			
			$val_trimmed = Text::limit_chars($val, 50);
			
			$column = $this->user_data['columns'][$column_name];

			
			// do foreign key links
			
			
			// do column type based alternations
			if( $column->keys == 'PRI' ) {
				$val_mod = Html::anchor(array('dbnav', $this->user_data['schema']->name, $this->user_data['table']->name, 'row', $val), $val);
			}			
			else if( in_array( $column->type, array('datetime', 'date', 'time', 'timestamp') ) ) {
				// pretty print the date/time
				$timestamp = ( is_int($val) ? $val : strtotime($val) );
				
				$date_fmt = Kohana::config('dbnav.format.date');
				$time_fmt = Kohana::config('dbnav.format.time');
				
				switch($column->type) {
					case 'time':
						$fmt_str = $time_fmt;
						break;
					case 'date':
						$fmt_str = $date_fmt;
						break;
					case 'datetime':
					case 'timestamp':
						$fmt_str = $date_fmt . ' ' . $time_fmt;
						break;
				}
				
				$val_mod = '<span class="date time">' . date($fmt_str, $timestamp) . '</span>';
			}
			else if( $column->type == 'tinyint' && $column->length == 1 ) {
				// assume tinyint(1) is a boolean value
				//$val_mod = '<input type="checkbox" disabled="disabled" value="1" ' . ($val ? ' checked="checked"' : '') . '/>';
				$val_mod = '<span class="boolean">' . ($val ? '&#10003;' : '&#10007;') . '</span>';
			}
			else if( Text::contains($column->type, array('int', 'integer') ) && !Text::contains($column_name, 'id') ) {
				$val_mod = number_format($val);
			}
			
			
				// lat/lng/coords - show map popup
				
				// do number format
				
				
				
			
			// do column name based alterations
			if( Text::contains($column_name, array('dob', 'date_of_birth')) ) {
				// append their age
				$val_mod .= ' <small>(' . date::span($timestamp, time(), 'years') . ')</small>';
			}
			else if( Text::contains($column_name, 'email') && Validate::email($val) ) {
				// format as an email
				$val_mod = Html::mailto($val, $val_trimmed);
			}
			else if( Text::contains($column_name, array('url', 'website')) && Validate::url($val) ) {
				// format as a url
				$val_mod = Html::anchor($val, $val_trimmed);
			}
			else if( Text::contains($column_name, array('ip', 'ip_address', 'ipaddress', 'ipaddr', 'ip_addr')) && Validate::ip($val) ) {
				// format as a url
				$val_mod = Html::anchor('#', Html::image('http://static.pubjury.local:8088/images/icons/help.png'), array('title' => 'Information about this ip address')) . ' <code>' . $val . '</code>';
			}
			else if( Text::contains($column_name, 'country') && preg_match('/^[a-z]{2}$/i', $val) ) {
				// assume we have an iso country code
				$val_mod = Html::image('http://static.pubjury.local:8088/images/flags/' . strtolower($val) . '.png') . ' ' . $val;
			}
						
			// do cell value based alterations
			if( is_null($val) ) {
				$val_mod = '<em>NULL</em>';
			}
			else if( strlen($val_trimmed) < strlen($val) && !isset($val_mod) ) {
				$val_mod = $val_trimmed . ' ' . Html::anchor(array('dbnav',$row), '<small>more</small>');
			}
			
			return '<td>' . (isset($val_mod) ? $val_mod : $val) . '</td>';
		}
}