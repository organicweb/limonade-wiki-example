<h1>Create page: <?=h($page_name)?></h1>
<form action="<?=url_for('create')?>" method="post">
  <input type="hidden" name="page_name" value="<?=$page_name;?>" id="page_name">
  <textarea name="page_content" id="page_content" rows="8" cols="40"></textarea>
  <p><input type="submit" value="Create &rarr;"></p>
</form>