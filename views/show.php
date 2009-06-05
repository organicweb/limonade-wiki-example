<h1><?=h($page_name);?></h1>
<div id="page_content">
  <?=wikir_render($page_content);?>
</div>
<div id="buttons">
  <p>
    <a href="<?=url_for('Home');?>">Home</a>
    | <a href="<?=url_for($page_name, 'edit');?>">Edit</a>
    <? if($page_name != 'Home'): ?>
    | <a href="<?=url_for($page_name);?>" onclick="if (confirm('Are you sure?')) { var f = document.createElement('form'); f.style.display = 'none'; this.parentNode.appendChild(f); f.method = 'POST'; f.action = this.href;var m = document.createElement('input'); m.setAttribute('type', 'hidden'); m.setAttribute('name', '_method'); m.setAttribute('value', 'DELETE'); f.appendChild(m); f.submit(); };return false;">Delete</a>
    <? endif; ?>
  </p>
</div>
<div id="tag_cloud">
  <h6>Tag Cloud</h6>
  <?=build_tagcloud(); ?>
</div>