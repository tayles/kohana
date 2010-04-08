<? if( $schemata->count() > 0 ) : ?>
<? foreach( $schemata as $schema ) : ?>
<?=Html::anchor('dbnav/' . $schema->name, $schema->name);?>
<br />
<? endforeach; ?>
<? else : ?>
<p><em>No schemata found</em></p>
<? endif; ?>