<? if( count($tables) > 0 ) : ?>
<p>
<? foreach( $tables as $table ) : ?>
<?=Html::anchor(array('dbnav', $schema->name, $table->name), $table->name);?> 
<? endforeach; ?>
</p>
<?=$tbl_view->render();?>
<? else : ?>
<p><em>No tables found</em></p>
<? endif; ?>