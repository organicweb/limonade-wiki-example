<?php



require_once(dirname(__FILE__).'/limonade.php');

class WikirPage 
{
	public $name = null;
	public $content = null;
	
	public function __construct()
	{
	  
	}
	
	public static function find($page)
	{
		$file = self::filepath($page);
		if(!file_exists($file)) return false;
		return file_read($file, $return = true);
	}
	
	public static function find_all()
	{
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
		return file_path(option('pages_dir'), self::filename($page));
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
		return file_put_contents(option('pages_dir').'/'.filename($this->name),
		        $this->content);
	}
	
	public function destroy()
	{
		return unlink(filename($this->name));
	}
	
}
?>