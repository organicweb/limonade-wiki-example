<?php



require_once(dirname(__FILE__).'/limonade.php');

class WikirPage 
{
	var $name = null;
	
	public static function find($page)
	{
		$file = file_path(option('pages_dir'),$page);
		if(!file_exists($file)) return false;
		return file_read($file, $return = true);
	}
	
	public static function find_all()
	{
		return file_list_dir(option('pages_dir'));
	}
	
	public static function create($page)
	{
		$handle = file_put_contents(option('pages_dir').'/'.filename($page));
		if ($handle!=false)
		{
			return true;
		}
	}
	
	public function update($page)
	{
		$handle = file_put_contents(option('pages_dir').'/'.filename($page));
		if ($handle!=false)
		{
			return true;
		}
	}
	
	public function destroy($page)
	{
		if (@unlink(filename($page)))
		{
			return true;
		}
	}
	
	public function slug($name, $replacement = '_')
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
	
	public function filename($name)
	{
		if (isset($name))
		{
			return $name.'.mkd';
		}
	}
}
?>