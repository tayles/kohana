<?php defined('SYSPATH') or die('No direct script access.');

class Text extends Kohana_Text {

	/**
	 * Searches a string for a word or words (using word boundaries)
	 *
	 **/
	 public static function ends_with($str, $endings) {
		$endings = (array)$endings;
		
		foreach( $endings as $ending ) {
			if( $str === $ending ) return $str;
			
			$substr_pos = -1 - strlen($ending);
			if( substr( $str, $substr_pos ) === '_' . $ending ) {
				// return the prefix (i.e. str - ending)
				return substr($str, 0, $substr_pos);
			}
		}
		return FALSE;
	}
	
	/**
	 * Searches a string for a word or words (using word boundaries)
	 *
	 **/
	public static function contains($str, $endings) {
		$endings = (array)$endings;
		return preg_match('/(?:^|\b|_)' . implode('|', $endings) . '(?:$|\b|_)/i', $str);
	}

}
