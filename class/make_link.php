<?php

// Converters of inline element
class XpWikiInlineConverter {

	var $func;

	var $converters; // as array()
	var $pattern;
	var $pos;
	var $result;

	function get_clone($obj) {
		static $clone_func;

		if (!isset ($clone_func)) {
			if (version_compare(PHP_VERSION, '5.0.0', '<')) {
				$clone_func = create_function('$a', 'return $a;');
			} else {
				$clone_func = create_function('$a', 'return clone $a;');
			}
		}
		return $clone_func ($obj);
	}

	function __clone() {
		$converters = array ();
		foreach ($this->converters as $key => $converter) {
			$converters[$key] = $this->get_clone($converter);
		}
		$this->converters = $converters;
	}

	function XpWikiInlineConverter(& $xpwiki, $converters = NULL, $excludes = NULL) {

		$this->func = & $xpwiki->func;

		if ($converters === NULL) {
			$converters = array ('plugin', // Inline plugins
				'note', // Footnotes
				'url', // URLs
				'url_interwiki', // URLs (interwiki definition)
				'mailto', // mailto: URL schemes
				'interwikiname', // InterWikiNames
				'autoalias', // AutoAlias
				//'autolink', // AutoLinks
				'bracketname', // BracketNames
				'wikiname', // WikiNames
				'autoalias_a', // AutoAlias(alphabet)
				//'autolink_a', // AutoLinks(alphabet)
			);
		}

		if ($excludes !== NULL)
			$converters = array_diff($converters, $excludes);

		$this->converters = $patterns = array ();
		$start = 1;

		foreach ($converters as $name) {
			$classname = 'XpWikiLink_'.$name;
			$converter = new $classname ($xpwiki, $start);
			$pattern = $converter->get_pattern();
			if ($pattern === FALSE)
				continue;

			$patterns[] = '('."\n".$pattern."\n".')';
			$this->converters[$start] = $converter;
			$start += $converter->get_count();
			++ $start;
		}
		$this->pattern = join('|', $patterns);
	}

	function convert($string, $page) {
		$this->page = $page;
		$this->result = array ();

		$string = preg_replace_callback('/'.$this->pattern.'/x', array (& $this, 'replace'), $string);

		$arr = explode("\x08", $this->func->make_line_rules(htmlspecialchars($string)));
		$retval = '';
		while (!empty ($arr)) {
			$retval .= array_shift($arr).array_shift($this->result);
		}
		
		return $retval;
	}

	function replace($arr) {
		$obj = $this->get_converter($arr);

		$this->result[] = ($obj !== NULL && $obj->set($arr, $this->page) !== FALSE) ? $obj->toString() : $this->func->make_line_rules(htmlspecialchars($arr[0]));

		return "\x08"; // Add a mark into latest processed part
	}

	function get_objects($string, $page) {
		$matches = $arr = array ();
		preg_match_all('/'.$this->pattern.'/x', $string, $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			$obj = $this->get_converter($match);
			if ($obj->set($match, $page) !== FALSE) {
				$arr[] = $this->get_clone($obj);
				if ($obj->body != '')
					$arr = array_merge($arr, $this->get_objects($obj->body, $page));
			}
		}
		return $arr;
	}

	function & get_converter(& $arr) {
		foreach (array_keys($this->converters) as $start) {
			if ($arr[$start] == $arr[0])
				return $this->converters[$start];
		}
		return NULL;
	}

}

// Base class of inline elements
class XpWikiLink {
	var $root;
	var $const;
	var $func;

	var $start; // Origin number of parentheses (0 origin)
	var $text; // Matched string

	var $type;
	var $page;
	var $name;
	var $body;
	var $alias;

	var $is_image;

	// Constructor
	function XpWikiLink(& $xpwiki, $start) {

		$this->xpwiki = & $xpwiki;
		$this->root = & $xpwiki->root;
		$this->cont = & $xpwiki->cont;
		$this->func = & $xpwiki->func;

		$this->start = $start;
	}

	// Return a regex pattern to match
	function get_pattern() {
	}

	// Return number of parentheses (except (?:...) )
	function get_count() {
	}

	// Set pattern that matches
	function set($arr, $page) {
	}

	function toString() {
	}

	// Private: Get needed parts from a matched array()
	function splice($arr) {
		$count = $this->get_count() + 1;
		$arr = array_pad(array_splice($arr, $this->start, $count), $count, '');
		$this->text = $arr[0];
		return $arr;
	}

	// Set basic parameters
	function setParam($page, $name, $body, $type = '', $alias = '') {
		static $converter = NULL;

		$this->page = $page;
		$this->name = $name;
		$this->body = $body;
		$this->type = $type;
		if (!$this->cont['PKWK_DISABLE_INLINE_IMAGE_FROM_URI'] && $this->func->is_url($alias) && preg_match('/\.(gif|png|jpe?g)$/i', $alias)) {
			$this->is_image = TRUE;
			if ($this->cont['SHOW_EXTIMG_BY_REF'] && !$this->func->refcheck(0,$alias)) {
				$_dum = '';
				$alias = $this->func->do_plugin_inline('ref', $alias, $_dum);
			} else {
				$alias = '<img src="'.htmlspecialchars($alias).'" alt="'.$name.'" />';
			}
		} else
			if ($alias != '') {
				if ($converter === NULL)
					$converter = new XpWikiInlineConverter($this->xpwiki, array ('plugin'));

				$alias = $this->func->make_line_rules($converter->convert($alias, $page));

				// BugTrack/669: A hack removing anchor tags added by AutoLink
				$alias = preg_replace('#</?a[^>]*>#i', '', $alias);
			}
		$this->alias = $alias;

		return TRUE;
	}
}

// Inline plugins
class XpWikiLink_plugin extends XpWikiLink {
	var $pattern;
	var $plain, $param;

	function XpWikiLink_plugin(& $xpwiki, $start) {
		parent :: XpWikiLink($xpwiki, $start);
	}

	function get_pattern() {
		$this->pattern =<<<EOD
&
(      # (1) plain
 (\w+) # (2) plugin name
 (?:
  \(
   ((?:(?!\)[;{]).)*) # (3) parameter
  \)
 )?
)
EOD;
		return<<<EOD
{$this->pattern}
(?:
 \{
  ((?:(?R)|(?!};).)*) # (4) body
 \}
)?
;
EOD;
	}

	function get_count() {
		return 4;
	}

	function set($arr, $page) {
		list ($all, $this->plain, $name, $this->param, $body) = $this->splice($arr);

		// Re-get true plugin name and patameters (for PHP 4.1.2)
		$matches = array ();
		if (preg_match('/^'.$this->pattern.'/x', $all, $matches) && $matches[1] != $this->plain)
			list (, $this->plain, $name, $this->param) = $matches;

		return parent :: setParam($page, $name, $body, 'plugin');
	}

	function toString() {
		$body = ($this->body == '') ? '' : $this->func->make_link($this->body);
		$str = FALSE;

		// Try to call the plugin
		if ($this->func->exist_plugin_inline($this->name))
			$str = $this->func->do_plugin_inline($this->name, $this->param, $body);

		if ($str !== FALSE) {
			return $str; // Succeed
		} else {
			// No such plugin, or Failed
			$body = (($body == '') ? '' : '{'.$body.'}').';';
			return $this->func->make_line_rules(htmlspecialchars('&'.$this->plain).$body);
		}
	}
}

// Footnotes
class XpWikiLink_note extends XpWikiLink {
	function XpWikiLink_note(& $xpwiki, $start) {
		parent :: XpWikiLink($xpwiki, $start);
	}

	function get_pattern() {
		return<<<EOD
\(\(
 ((?:(?R)|(?!\)\)).)*) # (1) note body
\)\)
EOD;
	}

	function get_count() {
		return 1;
	}

	function set($arr, $page) {
		
		static $noteStr;
		
		$base_id = $this->xpwiki->pid . '_' . $this->root->rtf['oid'];
		
		list (, $body) = $this->splice($arr);

		if ($this->cont['PKWK_ALLOW_RELATIVE_FOOTNOTE_ANCHOR']) {
			$script = '';
		} else {
			$script = $this->func->get_page_uri($page, true);
		}

		$page = isset ($this->root->vars['page']) ? rawurlencode($this->root->vars['page']) : '';
		
		if (isset($noteStr[$base_id][md5($body)])) {
			list($id, $title) = $noteStr[$base_id][md5($body)];
			$elm_id = '';
		} else {
			$id = ++ $this->root->rtf['note_id'];
			$note = $this->func->make_link($body);
	
			// Footnote
			$this->root->foot_explain[$id] = '<a id="notefoot_'.$base_id.'_'.$id.'" name="notefoot_'.$base_id.'_'.$id.'" href="'.$script.'#notetext_'.$base_id.'_'.$id.'" class="note_super">*'.$id.'</a>'."\n".'<span class="small">'.$note.'</span><br />';

			// A hyperlink, content-body to footnote
			if (!is_numeric($this->cont['PKWK_FOOTNOTE_TITLE_MAX']) || $this->cont['PKWK_FOOTNOTE_TITLE_MAX'] <= 0) {
				$title = '';
			} else {
				$title = strip_tags($note);
				$count = mb_strlen($title, $this->cont['SOURCE_ENCODING']);
				$title = mb_substr($title, 0, $this->cont['PKWK_FOOTNOTE_TITLE_MAX'], $this->cont['SOURCE_ENCODING']);
				$abbr = (mb_strlen($title) < $count) ? '...' : '';
				$title = ' title="'.$title.$abbr.'"';
			}

			$noteStr[$base_id][md5($body)] = array($id, $title);
			
			$elm_id = 'notetext_'.$base_id.'_'.$id;
		}
		
		$name = '<a id="'.$elm_id.'" name="'.$elm_id.'" href="'.$script.'#notefoot_'.$base_id.'_'.$id.'" class="note_super"'.$title.'>*'.$id.'</a>';

		return parent :: setParam($page, $name, $body);
	}

	function toString() {
		return $this->name;
	}
}

// URLs
class XpWikiLink_url extends XpWikiLink {
	function XpWikiLink_url(& $xpwiki, $start) {
		parent :: XpWikiLink($xpwiki, $start);
	}

	function get_pattern() {
		$s1 = $this->start + 1;
		return<<<EOD
(\[\[             # (1) open bracket
 ((?:(?!\]\]).)+) # (2) alias
 (?:>|:)
)?
(                 # (3) url
 (?:(?:https?|ftp|news):\/\/|mailto:)[\w\/\@\$()!?&%#:;.,~'=*+-]+
)
(?($s1)\]\])      # close bracket
EOD;
	}

	function get_count() {
		return 3;
	}

	function set($arr, $page) {
		list (,, $alias, $name) = $this->splice($arr);
		return parent :: setParam($page, htmlspecialchars($name), '', 'url', $alias == '' ? $name : $alias);
	}

	function toString() {
		if (strpos($this->name, $this->cont['ROOT_URL']) === FALSE) {
			$rel = ' rel="nofollow"';
			$class = ' class="ext"';
			$target = ($this->root->link_target)? ' target="' . $this->root->link_target . '"' : '';
		} else {
			$target = $rel = $class = '';
		}
		$img = ($this->is_image)? ' type="img"' : '';
		return '<a href="'.$this->name.'" title="'.preg_replace('#^https?://#','',$this->name).'"'.$rel.$class.$img.$target.'>'.$this->alias.'</a>';
	}
}

// URLs (InterWiki definition on "InterWikiName")
class XpWikiLink_url_interwiki extends XpWikiLink {
	function XpWikiLink_url_interwiki(& $xpwiki, $start) {
		parent :: XpWikiLink($xpwiki, $start);
	}

	function get_pattern() {
		return<<<EOD
\[       # open bracket
(        # (1) url
 (?:(?:https?|ftp|news):\/\/|\.\.?\/)[!~*'();\/?:\@&=+\$,%#\w.-]*
)
\s
([^\]]+) # (2) alias
\]       # close bracket
EOD;
	}

	function get_count() {
		return 2;
	}

	function set($arr, $page) {
		list (, $name, $alias) = $this->splice($arr);
		return parent :: setParam($page, htmlspecialchars($name), '', 'url', $alias);
	}

	function toString() {
		return '<a href="'.$this->name.'" rel="nofollow">'.$this->alias.'</a>';
	}
}

// mailto: URL schemes
class XpWikiLink_mailto extends XpWikiLink {
	var $is_image, $image;

	function XpWikiLink_mailto(& $xpwiki, $start) {
		parent :: XpWikiLink($xpwiki, $start);
	}

	function get_pattern() {
		$s1 = $this->start + 1;
		return<<<EOD
(?:
 \[\[
 ((?:(?!\]\]).)+)(?:>|:)  # (1) alias
)?
([\w.-]+@[\w-]+\.[\w.-]+) # (2) mailto
(?($s1)\]\])              # close bracket if (1)
EOD;
	}

	function get_count() {
		return 2;
	}

	function set($arr, $page) {
		list (, $alias, $name) = $this->splice($arr);
		return parent :: setParam($page, $name, '', 'mailto', $alias == '' ? $name : $alias);
	}

	function toString() {
		return '<a href="mailto:'.$this->name.'" rel="nofollow">'.$this->alias.'</a>';
	}
}

// InterWikiName-rendered URLs
class XpWikiLink_interwikiname extends XpWikiLink {
	var $url = '';
	var $param = '';
	var $anchor = '';

	function XpWikiLink_interwikiname(& $xpwiki, $start) {
		parent :: XpWikiLink($xpwiki, $start);
	}

	function get_pattern() {
		$s2 = $this->start + 2;
		$s5 = $this->start + 5;
		return<<<EOD
\[\[                  # open bracket
(?:
 ((?:(?!\]\]).)+)>    # (1) alias
)?
(\[\[)?               # (2) open bracket
((?:(?!\s|:|\]\]).)+) # (3) InterWiki
(?<! > | >\[\[ )      # not '>' or '>[['
:                     # separator
(                     # (4) param
 (\[\[)?              # (5) open bracket
 (?:(?!>|\]\]).)+
 (?($s5)\]\])         # close bracket if (5)
)
(?($s2)\]\])          # close bracket if (2)
\]\]                  # close bracket
EOD;
	}

	function get_count() {
		return 5;
	}

	function set($arr, $page) {
		//		global $script;

		list (, $alias,, $name, $this->param) = $this->splice($arr);

		$matches = array ();
		if (preg_match('/^([^#]+)(#[A-Za-z][\w-]*)$/', $this->param, $matches))
			list (, $this->param, $this->anchor) = $matches;

		$url = $this->func->get_interwiki_url($name, $this->param);
		$this->url = ($url === FALSE) ? $this->root->script.'?'.rawurlencode('[['.$name.':'.$this->param.']]') : htmlspecialchars($url);

		return parent :: setParam($page, htmlspecialchars($name.':'.$this->param), '', 'InterWikiName', $alias == '' ? $name.':'.$this->param : $alias);
	}

	function toString() {
		return '<a href="'.$this->url.$this->anchor.'" title="'.$this->name.'" rel="nofollow">'.$this->alias.'</a>';
	}
}

// BracketNames
class XpWikiLink_bracketname extends XpWikiLink {
	var $anchor, $refer;

	function XpWikiLink_bracketname(& $xpwiki, $start) {
		parent :: XpWikiLink($xpwiki, $start);
	}

	function get_pattern() {
		//		global $WikiName, $BracketName;
		$WikiName = $this->root->WikiName;
		$BracketName = $this->root->BracketName;
		$s2 = $this->start + 2;
		return<<<EOD
\[\[                     # Open bracket
(?:((?:(?!\]\]).)+)>)?   # (1) Alias
(\[\[)?                  # (2) Open bracket
(                        # (3) PageName
 (?:$WikiName)
 |
 (?:$BracketName)
)?
(\#(?:[a-zA-Z][\w-]*)?)? # (4) Anchor
(?($s2)\]\])             # Close bracket if (2)
\]\]                     # Close bracket
EOD;
	}

	function get_count() {
		return 4;
	}

	function set($arr, $page) {
		//		global $WikiName;

		list (, $alias,, $name, $this->anchor) = $this->splice($arr);
		if ($name == '' && $this->anchor == '')
			return FALSE;

		if ($name == '' || !preg_match('/^'.$this->root->WikiName.'$/', $name)) {
			if ($alias == '')
				$alias = $name.$this->anchor;
			if ($name != '') {
				$name = $this->func->get_fullname($name, $page);
				if (!$this->func->is_pagename($name))
					return FALSE;
			}
		}

		return parent :: setParam($page, $name, '', 'pagename', $alias);
	}

	function toString() {
		return $this->func->make_pagelink($this->name, $this->alias, $this->anchor, $this->page);
	}
}

// WikiNames
class XpWikiLink_wikiname extends XpWikiLink {
	function XpWikiLink_wikiname(& $xpwiki, $start) {
		parent :: XpWikiLink($xpwiki, $start);
	}

	function get_pattern() {
		//		global $WikiName, $nowikiname;

		return $this->root->nowikiname ? FALSE : '('.$this->root->WikiName.')';
	}

	function get_count() {
		return 1;
	}

	function set($arr, $page) {
		list ($name) = $this->splice($arr);
		return parent :: setParam($page, $name, '', 'pagename', $name);
	}

	function toString() {
		return $this->func->make_pagelink($this->name, $this->alias, '', $this->page);
	}
}

// AutoLinks
class XpWikiLink_autolink extends XpWikiLink {
	var $forceignorepages = array ();
	var $auto;
	var $auto_a; // alphabet only

	function XpWikiLink_autolink(& $xpwiki, $start) {
		//		global $autolink;

		parent :: XpWikiLink($xpwiki, $start);

		if (!$this->root->autolink || !file_exists($this->cont['CACHE_DIR'].$this->cont['PKWK_AUTOLINK_REGEX_CACHE']))
			return;

		@ list ($auto, $auto_a, $forceignorepages) = file($this->cont['CACHE_DIR'].$this->cont['PKWK_AUTOLINK_REGEX_CACHE']);
		$this->auto = $auto;
		$this->auto_a = $auto_a;
		$this->forceignorepages = explode("\t", trim($forceignorepages));
	}

	function get_pattern() {
		return isset ($this->auto) ? '('.$this->auto.')' : FALSE;
	}

	function get_count() {
		return 1;
	}

	function set($arr, $page) {
		//		global $WikiName;

		list ($name) = $this->splice($arr);

		// Ignore pages listed, or Expire ones not found
		if (in_array($name, $this->forceignorepages) || !$this->func->is_page($name))
			return FALSE;

		return parent :: setParam($page, $name, '', 'pagename', $name);
	}

	function toString() {
		return $this->func->make_pagelink($this->name, $this->alias, '', $this->page, 'autolink');
	}
}

class XpWikiLink_autolink_a extends XpWikiLink_autolink {
	function XpWikiLink_autolink_a(& $xpwiki, $start) {
		parent :: XpWikiLink_autolink($xpwiki, $start);
	}

	function get_pattern() {
		return isset ($this->auto_a) ? '('.$this->auto_a.')' : FALSE;
	}
}

// AutoAlias
class XpWikiLink_autoalias extends XpWikiLink {
	var $forceignorepages = array ();
	var $auto;
	var $auto_a; // alphabet only
	var $alias;

	function XpWikiLink_autoalias(& $xpwiki, $start) {
		//		global $autoalias, $aliaspage;

		parent :: XpWikiLink($xpwiki, $start);

		if (!$this->root->autoalias || !file_exists($this->cont['CACHE_DIR'].$this->cont['PKWK_AUTOALIAS_REGEX_CACHE']) || $this->page == $this->root->aliaspage) {
			return;
		}

		@ list ($auto, $auto_a, $forceignorepages) = file($this->cont['CACHE_DIR'].$this->cont['PKWK_AUTOALIAS_REGEX_CACHE']);
		$this->auto = $auto;
		$this->auto_a = $auto_a;
		$this->forceignorepages = explode("\t", trim($forceignorepages));
		$this->alias = '';
	}
	function get_pattern() {
		return isset ($this->auto) ? '('.$this->auto.')' : FALSE;
	}
	function get_count() {
		return 1;
	}
	function set($arr, $page) {
		list ($name) = $this->splice($arr);
		// Ignore pages listed
		if (in_array($name, $this->forceignorepages)) {
			return FALSE;
		}
		return parent :: setParam($page, $name, '', 'pagename', $name);
	}

	function toString() {
		$this->alias = $this->func->get_autoaliases($this->name);
		if ($this->alias != '') {
			$link = '[['.$this->name.'>'.$this->alias.']]';
			return $this->func->make_link($link);
		}
		return '';
	}
}

class XpWikiLink_autoalias_a extends XpWikiLink_autoalias {
	function XpWikiLink_autoalias_a(& $xpwiki, $start) {
		parent :: XpWikiLink_autoalias($xpwiki, $start);
	}
	function get_pattern() {
		return isset ($this->auto_a) ? '('.$this->auto_a.')' : FALSE;
	}
}
?>