<?php

require_once dirname(__FILE__).'/limonade.php';
require_once dirname(__FILE__).'/vendors.php';

/**
 * WikirPage class for Limonade.
 *
 * Handle name, content and file for the Wikir Project
 *
 */
class WikirPage 
{
/**
 * filename string
 * @var string
 */
	public $name = null;
/**
 * content string
 * @var string
 */
	public $content = null;
/**
 * Constructor
 */
	public function __construct()
	{
	  
	}
/**
 * Find the file in pages directory
 * @var string $page_name
 * @return true if file is found
 */
	public static function find($page_name)
	{
		$file = self::filepath($page_name);
		if(!file_exists($file)) return false;
		$page = new self();
		$page->name($page_name);
		$page->content(file_read($file, $return = true));
		return $page;
	}
/**
 * Find all files in pages directory
 * @return array files name
 */
	public static function find_all()
	{
		# TODO: return WikirPage instances
		return file_list_dir(option('pages_dir'));
	}
/**
 * Returns a string with all spaces converted to underscores (by default), accented
 * characters converted to non-accented characters, and non word characters removed.
 *
 * @param string $name
 * @param string $replacement
 * @return string
 */
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
/**
 * Returns a string with $name and $particle concatened
 *
 * @param string $name
 * @param string $particle
 * @return string
 */
	public static function filename($name, $particle = '.mkd')
	{
		return self::slug($name).$particle;
	}
/**
 * Returns filepath for $name
 *
 * @param string $name
 * @return string
 */
	public static function filepath($name)
	{
		return file_path(option('pages_dir'), self::filename($name));
	}
	
	/**
	 * Checks if a page exists or not
	 *
	 * @param string $name Name of the page
	 * @return bool
	 */
	public static function exists($name)
	{
	   return file_exists(self::filepath($name));
	}
	
/**
 * Return a string 
 *
 * @param string $name
 * @return string
 */
	public function name($name = null)
	{
	  if(!is_null($name)) $this->name = $name;
	  return $this->name;
	}
/**
 * Return a string 
 *
 * @param string $content
 * @return string
 */
	public function content($content = null)
	{
	  if(!is_null($content)) $this->content = $content;
	  return $this->content;
	}
/**
 * Write the file to the pages directory
 * @return returns the number of bytes if file written
 */
	public function save()
	{
		return file_put_contents(self::filepath($this->name), $this->content);
	}
/**
 * Remove the file from the pages directory
 * @return returns true if file deleted
 */
	public function destroy()
	{
		return unlink(self::filepath($this->name));
	}
	
}


## HELPERS

function wikir_render($str)
{
  $str = Markdown($str);
  
  $link_regexp = '/\[\[(.*?)\]\]/';
  $regexps = array();
  $replacements = array();
  preg_match_all($link_regexp, $str, $matches, PREG_SET_ORDER);
  foreach($matches as $match)
  {
    
    $regexps[] = '/\[\['.preg_quote($match[1]).'\]\]/';
    
    $link  = '<a href="';
    $link .= url_for($match[1]);
    $link .= '">';
    $link .= h($match[1]);
    $link .= '</a>';
    if(!WikirPage::exists($match[1])) $link .= '<sup>(?)</sup>';
    $replacements[] = $link;
  }
  return preg_replace($regexps, $replacements, $str);
}

function build_pages_cloud()
{
  $files = file_list_dir(option('pages_dir'));
  $keywords = array();
  foreach ($files as $each)
  {
    $parsable[] = file_read(file_path(option('pages_dir'), $each), $return = true);
    foreach ($parsable as &$parsed)
    {
      Markdown($parsed);
    }
    $link_regexp = '/\[\[(.*?)\]\]/';
    $regexps = array();
    $replacements = array();
    preg_match_all($link_regexp, $parsed, $matches, PREG_SET_ORDER);
    $matcher[] = $matches;
  }
  
  foreach ($matcher as $value)
  {
    foreach($value as $match)
    {
      if(!array_key_exists($match[1], $keywords))
      {
        if (WikirPage::exists($match[1]))
        {
          $keywords[$match[1]] = 1;
        }
      } else
      {
        $keywords[$match[1]] += 1;
      }
    }
  }
  $maxscore = max($keywords);
  $minscore = min($keywords);
  foreach ($keywords as $page_name=>$score)
  {
    $size = get_percent_size($maxscore, $minscore, $score, 90, 200);
    $link  = '<a href="';
    $link .= url_for($page_name);
    $link .= '" style="font-size:'.$size.'%">';
    $link .= h($page_name);
    $link .= '</a>';
    $data[$page_name] = $link;
    
  }
  $data = shuffle_pages_names($data);
  $data = '<p>'.implode($data, ' - ').'</p>';
  return $data;
}
function get_percent_size($maxscore, $minscore, $current_value, $minsize = 90, $maxsize = 200)
{
  if ($minscore < 1) $minscore = 1;
  $spread = $maxscore - $minscore;
  if($spread == 0) $spread = 1;
  $step = ($maxsize - $minsize) / $spread;
  $size = $minsize + (($current_value - $minscore) * $step);
  return $size;
}
function shuffle_pages_names ($pages_name) 
{
  while (count($pages_name) > 0) {
      $val = array_rand($pages_name);
      $shuffled_names[$val] = $pages_name[$val];
      unset($pages_name[$val]);
  }
  if (isset($shuffled_names))
  return $shuffled_names;
}
?>
