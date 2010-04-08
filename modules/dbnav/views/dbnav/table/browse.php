<p><?=Html::anchor('dbnav', '&laquo; list databases');?></p>
<p><?=Html::anchor(array('dbnav', $schema->name), '&laquo; back to ' . $schema->name);?></p>
<p><?=Html::anchor(array('dbnav', $schema->name, $table->name), '&laquo; back to ' . $table->name);?></p>

<? if( count($rows) > 0 ) : ?>
<?=$pagination->render();?>
<?=$tbl_view->render();?>
<?=$pagination->render();?>
<? else : ?>
<p><em>No rows found</em></p>
<? endif; ?>