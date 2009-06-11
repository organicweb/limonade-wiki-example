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

/**
 * Returns html string for a given markdown string.
 * Also converts double brackets wiki links in html links.
 *
 * @param string $str markdown content with [[wiki links]]
 * @return str
 */
function html_wikir_render($str)
{
  $str = Markdown($str);
  
  $regexps = array();
  $replacements = array();
  $refs = wikir_pages_references($str);
  foreach($refs as $ref)
  {
    $regexps[] = '/\[\['.preg_quote($ref).'\]\]/';
    
    $link  = '<a href="';
    $link .= url_for($ref);
    $link .= '">';
    $link .= h($ref);
    $link .= '</a>';
    if(!WikirPage::exists($ref)) $link .= '<sup>(?)</sup>';
    $replacements[] = $link;
  }
  return preg_replace($regexps, $replacements, $str);
}


/**
 * Parse a string and returns found [[pages links]]
 *
 * @param string $str 
 * @return array
 */
function wikir_pages_references($str)
{
  $refs = array();
  $link_regexp = '/\[\[(.*?)\]\]/';
  preg_match_all($link_regexp, $str, $matches, PREG_SET_ORDER);
  foreach($matches as $match) $refs[] = $match[1];
  return $refs;
}

/**
 * Returns html pages cloud
 *
 * @param string $separator string separator between links
 * @return str
 */
function html_pages_cloud($separator = ' - ')
{
  $files = file_list_dir(option('pages_dir'));
  $pages_names = array();
  
  foreach ($files as $file)
  {
    $content = file_read(file_path(option('pages_dir'), $file), $return = true);
    $link_regexp = '/\[\[(.*?)\]\]/';
    $refs = wikir_pages_references($content);
    foreach($refs as $ref)
    {
      if(!array_key_exists($ref, $pages_names)) $pages_names[$ref] = 0;
      $pages_names[$ref] += 1;
    }
  }

  $maxscore = max($pages_names);
  $minscore = min($pages_names);
  $html_links = array();
  foreach ($pages_names as $page_name=>$score)
  {
    $size = _get_percent_size($maxscore, $minscore, $score);
    $html_links[] = render ( 
                      'html_pages_cloud_link',
                      null, 
                      array('page_name' => $page_name, 'size' => $size)
                    ); 
  }
  
  shuffle($html_links);
  return implode($html_links, $separator);
}

/**
 * Returns a percent size
 *
 * @access private
 * @param string $maxscore 
 * @param string $minscore 
 * @param string $current_value 
 * @param string $minsize 
 * @param string $maxsize 
 * @return int
 */
function _get_percent_size($maxscore, $minscore, $current_value, $minsize = 90, $maxsize = 200)
{
  if ($minscore < 1) $minscore = 1;
  $spread = $maxscore - $minscore;
  if($spread == 0) $spread = 1;
  $step = ($maxsize - $minsize) / $spread;
  $size = $minsize + (($current_value - $minscore) * $step);
  return $size;
}


#
# INLINE PARTIALS
#

function html_pages_cloud_link($vars){ extract($vars);?> 
  <a href="<?=url_for($page_name);?>" style="font-size:<?=$size?>%;">
    <?=h($page_name);?>
  </a>
<?}

?>
