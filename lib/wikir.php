<?php



require_once dirname(__FILE__).'/limonade.php';
require_once dirname(__FILE__).'/vendors.php';

class WikirPage 
{
	public $name = null;
	public $content = null;
	
	public function __construct()
	{
	  
	}
	
	public static function find($page_name)
	{
		$file = self::filepath($page_name);
		if(!file_exists($file)) return false;
		$page = new self();
		$page->name($page_name);
		$page->content(file_read($file, $return = true));
		return $page;
	}
	
	public static function find_all()
	{
		# TODO: return WikirPage instances
		return file_list_dir(option('pages_dir'));
	}
	
	public static function slug($name, $replacement = '_')
	{
		$map = array(
			'/à|á|å|â/' => 'a',
			'/è|é|ê|ẽ|ë/' => 'e',
			'/ì|í|î/' => 'i',
			'/ò|ó|ô|ø/' => 'o',
			'/ù|ú|ů|û/' => 'u',
			'/ç/' => 'c',
			'/ñ/' => 'n',
			'/ä|æ/' => 'ae',
			'/ö/' => 'oe',
			'/ü/' => 'ue',
			'/Ä/' => 'Ae',
			'/Ü/' => 'Ue',
			'/Ö/' => 'Oe',
			'/ß/' => 'ss',
			'/[^\w\s]/' => ' ',
			'/\\s+/' => $replacement
		);
		return preg_replace(array_keys($map), array_values($map), $name);
	}
	
	public static function filename($name)
	{
		return self::slug($name).'.mkd';
	}
	
	public static function filepath($name)
	{
		return file_path(option('pages_dir'), self::filename($name));
	}
	
	public function name($name = null)
	{
	  if(!is_null($name)) $this->name = $name;
	  return $this->name;
	}
	
	public function content($content = null)
	{
	  if(!is_null($content)) $this->content = $content;
	  return $this->content;
	}
	
	public function save()
	{
		return file_put_contents(self::filepath($this->name), $this->content);
	}
	
	public function destroy()
	{
		return unlink(self::filepath($this->name));
	}
	
}


## HELPERS

function wikir_render($str)
{
  # TODO: implements markdown and linkification...
  $str = Markdown($str);
  return $str;
}


?>