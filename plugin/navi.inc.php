<?php
class xpwiki_plugin_navi extends xpwiki_plugin {
	function plugin_navi_init () {


	// PukiWiki - Yet another WikiWikiWeb clone.
	// $Id: navi.inc.php,v 1.4 2007/06/08 08:51:41 nao-pon Exp $
	//
	// Navi plugin: Show DocBook-like navigation bar and contents
	
	/*
	 * Usage:
	 *   #navi(contents-page-name)   <for ALL child pages>
	 *   #navi([contents-page-name][,reverse]) <for contents page>
	 *
	 * Parameter:
	 *   contents-page-name - Page name of home of the navigation (default:itself)
	 *   reverse            - Show contents revese
	 *
	 * Behaviour at contents page:
	 *   Always show child-page list like 'ls' plugin
	 *
	 * Behaviour at child pages:
	 *
	 *   The first plugin call - Show a navigation bar like a DocBook header
	 *
	 *     Prev  <contents-page-name>  Next
	 *     --------------------------------
	 *
	 *   The second call - Show a navigation bar like a DocBook footer
	 *
	 *     --------------------------------
	 *     Prev          Home          Next
	 *     <pagename>     Up     <pagename>
	 *
	 * Page-construction example:
	 *   foobar    - Contents page, includes '#navi' or '#navi(foobar)'
	 *   foobar/1  - One of child pages, includes one or two '#navi(foobar)'
	 *   foobar/2  - One of child pages, includes one or two '#navi(foobar)'
		 */
	
	// Exclusive regex pattern of child pages
		$this->cont['PLUGIN_NAVI_EXCLUSIVE_REGEX'] =  '';
	//define('PLUGIN_NAVI_EXCLUSIVE_REGEX', '#/_#'); // Ignore 'foobar/_memo' etc.
	
	// Insert <link rel=... /> tags into XHTML <head></head>
		$this->cont['PLUGIN_NAVI_LINK_TAGS'] =  FALSE;	// FALSE, TRUE

	}
	
	// ----
	
	function plugin_navi_convert()
	{
		// ���󥯥롼�ɤ���Ƥ������̵���ˤ���
		if ($this->root->rtf['convert_nest'] > 1) return '';
	
		static $navi = array();
		if (!isset($navi[$this->xpwiki->pid])) {$navi[$this->xpwiki->pid] = array();}
	
		$current = $this->root->vars['page'];
		$reverse = FALSE;
		if (func_num_args()) {
			list($home, $reverse) = array_pad(func_get_args(), 2, '');
			// strip_bracket() is not necessary but compatible
			$home    = $this->func->get_fullname($this->func->strip_bracket($home), $current);
			$is_home = ($home == $current);
			if (! $this->func->is_page($home)) {
				return '#navi(contents-page-name): No such page: ' .
				htmlspecialchars($home) . '<br />';
			} else if (! $is_home &&
			    ! preg_match('/^' . preg_quote($home, '/') . '/', $current)) {
				return '#navi(' . htmlspecialchars($home) .
				'): Not a child page like: ' .
				htmlspecialchars($home . '/' . basename($current)) .
				'<br />';
			}
			$reverse = (strtolower($reverse) == 'reverse');
		} else {
			$home    = $this->root->vars['page'];
			$is_home = TRUE; // $home == $current
		}
	
		$pages  = array();
		$footer = isset($navi[$this->xpwiki->pid][$home]); // The first time: FALSE, the second: TRUE
		if (! $footer) {
			$navi[$this->xpwiki->pid][$home] = array(
				'up'   =>'',
			'prev' =>'',
			'prev1'=>'',
			'next' =>'',
			'next1'=>'',
			'home' =>'',
			'home1'=>'',
		);
	
			//$pages = preg_grep('/^' . preg_quote($home, '/') .
			//'($|\/)/', $this->func->get_existpages());
			$pages = $this->func->get_existpages(FALSE, $home . '/');
			if ($this->cont['PLUGIN_NAVI_EXCLUSIVE_REGEX'] != '') {
				// If old PHP could use preg_grep(,,PREG_GREP_INVERT)...
				$pages = array_diff($pages,
				preg_grep($this->cont['PLUGIN_NAVI_EXCLUSIVE_REGEX'], $pages));
			}
			$pages[] = $current; // Sentinel :)
			$pages   = array_unique($pages);
			natcasesort($pages);
			if ($reverse) $pages = array_reverse($pages);
	
			$prev = $home;
			foreach ($pages as $page) {
				if ($page == $current) break;
				$prev = $page;
			}
			$next = current($pages);
	
			$pos = strrpos($current, '/');
			$up = '';
			if ($pos > 0) {
				$up = substr($current, 0, $pos);
				$navi[$this->xpwiki->pid][$home]['up']    = $this->func->make_pagelink($up, $this->root->_navi_up);
			}
			if (! $is_home) {
				$navi[$this->xpwiki->pid][$home]['prev']  = $this->func->make_pagelink($prev);
				$navi[$this->xpwiki->pid][$home]['prev1'] = $this->func->make_pagelink($prev, $this->root->_navi_prev);
			}
			if ($next != '') {
				$navi[$this->xpwiki->pid][$home]['next']  = $this->func->make_pagelink($next);
				$navi[$this->xpwiki->pid][$home]['next1'] = $this->func->make_pagelink($next, $this->root->_navi_next);
			}
			$navi[$this->xpwiki->pid][$home]['home']  = $this->func->make_pagelink($home);
			$navi[$this->xpwiki->pid][$home]['home1'] = $this->func->make_pagelink($home, $this->root->_navi_home);
	
			// Generate <link> tag: start next prev(previous) parent(up)
			// Not implemented: contents(toc) search first(begin) last(end)
			if ($this->cont['PLUGIN_NAVI_LINK_TAGS']) {
				foreach (array('start'=>$home, 'next'=>$next,
			    'prev'=>$prev, 'up'=>$up) as $rel=>$_page) {
					if ($_page != '') {
						$s_page = htmlspecialchars($_page);
						$r_page = rawurlencode($_page);
						$this->root->head_tags[] = ' <link rel="' .
						$rel . '" href="' . $this->root->script .
						'?' . $r_page . '" title="' .
						$s_page . '" />';
					}
				}
			}
		}
	
		$ret = '';
	
		if ($is_home) {
			// Show contents
			$count = count($pages);
			if ($count == 0) {
				return '#navi(contents-page-name): You already view the result<br />';
			} else if ($count == 1) {
				// Sentinel only: Show usage and warning
				$home = htmlspecialchars($home);
				$ret .= '#navi(' . $home . '): No child page like: ' .
				$home . '/Foo';
			} else {
				$ret .= '<ul>';
				foreach ($pages as $page)
					if ($page != $home)
						$ret .= ' <li>' . $this->func->make_pagelink($page) . '</li>';
				$ret .= '</ul>';
			}
	
		} else if (! $footer) {
			// Header
			$ret = <<<EOD
<ul class="navi">
 <li class="navi_left">{$navi[$this->xpwiki->pid][$home]['prev1']}</li>
 <li class="navi_right">{$navi[$this->xpwiki->pid][$home]['next1']}</li>
 <li class="navi_none">{$navi[$this->xpwiki->pid][$home]['home']}</li>
</ul>
<hr class="full_hr" style="clear:both;" />
EOD;
	
		} else {
			// Footer
			$ret = <<<EOD
<hr class="full_hr" />
<ul class="navi">
 <li class="navi_left">{$navi[$this->xpwiki->pid][$home]['prev1']}<br />{$navi[$this->xpwiki->pid][$home]['prev']}</li>
 <li class="navi_right">{$navi[$this->xpwiki->pid][$home]['next1']}<br />{$navi[$this->xpwiki->pid][$home]['next']}</li>
 <li class="navi_none">{$navi[$this->xpwiki->pid][$home]['home1']}<br />{$navi[$this->xpwiki->pid][$home]['up']}</li>
</ul>
<div style="clear:both;height:0px;"> </div>
EOD;
		}
		return $ret;
	}
}
?>