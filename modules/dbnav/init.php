<?php

Route::set('dbnav_table', 'dbnav/<schema>/<table>(/<action>)(/page/<page>)')
	->defaults(array(
		'controller'	=> 'dbnav',
		'action'		=> 'table',
		'page'			=> 1,
	));

Route::set('dbnav_schema', 'dbnav(/<schema>(/<action>))')
	->defaults(array(
		'controller'	=> 'dbnav',
		'action'		=> 'index',
	));