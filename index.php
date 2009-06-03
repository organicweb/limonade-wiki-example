<?php
require 'lib/limonade.php';
require 'lib/wikir.php';

function configure()
{
  option('pages_dir', file_path(option('root_dir'), 'pages'));
}

function before()
{
  layout('default_layout.php');
}


dispatch('/', 'wikir_home');
  function wikir_home()
  {
    redirect('/Home');
  }


dispatch('/:page', 'wikir_page_show');
  function wikir_page_show()
  {
    $page_name = params('page');
    if(empty($page_name)) halt(NOT_FOUND);
    if($page = WikirPage::find($page_name))
    {
      set('page_name', $page->name);
      set('page_content', $page->content);
      html('show.php');
    }
    halt(NOT_FOUND, 'No page '.$page_name);
  }
  
dispatch('/new/:page', 'wikir_page_new');
  function wikir_page_new()
  {
    $page_name = params('page');
    if(empty($page_name)) halt('A page name must be provided !');
    set('page_name', $page_name);
    html('new.php');
  }

dispatch_post('/new/:page', 'wikir_page_create');
  function wikir_page_create()
  {
    $page_name    = $_POST['page_name'];
    $page_content = $_POST['page_content'];
    $page = new WikirPage();
    $page->name($page_name);
    $page->content($page_content); 
    if($page->save())
    {
      redirect('/'.$page->name());
    }
    halt('An error occured. Unable to create this page. Please check page/ dir is writable.');
  }
  
dispatch('/:page/edit', 'wikir_page_edit');
  function wikir_page_edit()
  {
    $page_name = params('page');
    if($page = WikirPage::find($page_name))
    {
      set('page_name', $page->name());
      set('page_content', $page->content());
      html('edit.php');
    }
    halt(NOT_FOUND);
  }


dispatch_put('/:page', 'wikir_page_update');
  function wikir_page_update()
  {
    $page_name = params('page');
    $page_content = $_POST['page_content'];
    if($page = WikirPage::find($page_name))
    {
      $page->name($page_name);
      $page->content($page_content);
      if($page->save())
      {
        redirect('/'.$page->name());
      }
      halt('An error occured. Unable to update this page. Please check page/ dir is writable.');
    }
    halt(NOT_FOUND);
  }
  
dispatch_delete('/:page', 'wikir_page_destroy');
  function wikir_page_destroy()
  {
    $page_name = params('page');
    if($page = WikirPage::find($page_name))
    {
      $page->name($page_name);
      $page->content($page_content);
      if($page->destroy())
      {
        redirect('/');
      }
      halt('An error occured. Unable to destroy this page. Please check page/ dir is writable.');
    }
    halt(NOT_FOUND);
  }

?>