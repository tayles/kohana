<ul class="inline">

<li><?=Html::anchor('dbnav/engines', 'List engines');?></li>
<li><?=Html::anchor('dbnav/variables', 'Database status');?></li>
<li><?=Html::anchor('dbnav/variables', 'Current queries');?></li>
<li><?=Html::anchor('dbnav/variables', 'List variables');?></li>
<li><?=Html::anchor('dbnav/execsql', 'Execute some SQL');?></li>
<li><?=Html::anchor('dbnav/execsql', 'Users / Privaledges');?></li>
<li><?=Html::anchor('dbnav/execsql', 'Triggers');?></li>
<li><?=Html::anchor('dbnav/execsql', 'Stored Procedures / Functions');?></li>

</ul>

<? if( $schema ) : ?>

<ul class="inline">

<li><?=Html::anchor('dbnav', '&laquo; list databases');?></li>

<? if( $table ) : ?>

<li><?=Html::anchor(array('dbnav', $schema->name), '&laquo; Lists tables in ' . $schema->name);?></li>

</ul>

<ul class="inline">

<li><?=Html::anchor(array('dbnav', $schema->name, $table->name), 'Browse table');?></li>
<li><?=Html::anchor(array('dbnav', $schema->name, $table->name, 'columns'), 'Edit columns');?></li>
<li><?=Html::anchor(array('dbnav', $schema->name, $table->name, 'edit'), 'Edit table');?></li>
<li><?=Html::anchor(array('dbnav', $schema->name, $table->name, 'indices'), 'View indices');?></li>

</ul>

<? endif; ?>

</ul>

<? endif; ?>