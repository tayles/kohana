<? if( count($rows) > 0 ) : ?>

<div id="search">
<form>
<input type="text" id="" name="" />
<button type="submit">Search</button>
<br />
<small><em>Separate multiple terms with a comma, no need to quote your search term. Possible operators include =, <, >, like, regexp (leave blank to do an equality)... e.g. "username tayles" or "id = 53534, town like readi%"</em></small>
</form>
</div>


<?=$pagination->render();?>
<div class="tbl">
<?=$tbl_view->render();?>
</div>
<?=$pagination->render();?>
<? else : ?>
<p><em>No rows found</em></p>
<? endif; ?>