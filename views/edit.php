<h1>Edit page: <?=h($page_name)?></h1>
<form action="<?=url_for($page_name)?>" method="post">
  <input type="hidden" name="_method" value="PUT" id="_method">
  <input type="hidden" name="page_name" value="<?=page_name?>" id="page_name">
  <textarea name="page_content" id="page_content" rows="8" cols="40"><?=$page_content;?></textarea>
  <p><input type="submit" value="Update &rarr;"></p>
</form>