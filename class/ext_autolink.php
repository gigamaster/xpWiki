<?php
/*
 * Created on 2007/04/23 by nao-pon http://hypweb.net/
 * $Id: ext_autolink.php,v 1.12 2007/07/11 06:18:09 nao-pon Exp $
 */
class XpWikiPukiExtAutoLink {
	// External AutoLinks
	
	function XpWikiPukiExtAutoLink (& $xpwiki) {
		ini_set('mbstring.substitute_character', 'none');
		$this->xpwiki = & $xpwiki;
		$this->root = & $xpwiki->root;
		$this->cont = & $xpwiki->cont;
		$this->func = & $xpwiki->func;
	}
	
	function ext_autolink(& $str) {
		if (empty($this->root->ext_autolinks)) return $str;
		
		foreach($this->root->ext_autolinks as $valid => $autolink)
		{
			$pat = $this->get_ext_autolink($autolink, $valid);
			if ($pat) {
				if ($this->ci) {
					$pat_pre = '/(<a.*?<\/a>|<(?:textarea|style|script).*?<\/(?:textarea|style|script)>|<[^>]*>|&(?:#[0-9]+|#x[0-9a-f]+|[0-9a-z]+);)|(';
					$pat_aft = ')/isS';	
				} else {
					$pat_pre = '/(<(?:a|A).*?<\/(?:a|A)>|<(?:textarea|style|script).*?<\/(?:textarea|style|script)>|<[^>]*>|&(?:#[0-9]+|#x[0-9a-f]+|[0-9a-zA-Z]+);)|(';
					$pat_aft = ')/sS';	
				}
				foreach(explode("\t", $pat) as $_pat){
					$pattern = $pat_pre.$_pat.$pat_aft;
					$str = preg_replace_callback($pattern,array(&$this,'ext_autolink_replace'),$str);
				}
			}
		}
	}
	function ext_autolink_replace($match) {
		
		if (!empty($match[1])) return $match[1];
		$name = $match[2];
		
		static $forceignorepages = array();
		if (!isset($forceignorepages[$this->root->mydirname])) {
			@ list ( $dum , $dum , $_tmp) = @ file($this->cont['CACHE_DIR'].$this->cont['PKWK_AUTOLINK_REGEX_CACHE']);
			$forceignorepages[$this->root->mydirname]['cs'] = explode("\t", trim($_tmp));
			$forceignorepages[$this->root->mydirname]['ci'] = array_map('strtolower', $forceignorepages[$this->root->mydirname]['cs']);
		}
		// ̵��ꥹ�Ȥ˴ޤޤ�Ƥ���ڡ�����ΤƤ�
		if (in_array(($this->ci ? strtolower($name) : $name), $forceignorepages[$this->root->mydirname]['cs'])) {return $match[0];}	
		
		// minimum length of name
		if (strlen($name) < $this->ext_autolink_len) {return $match[0];}
		
		$page = $this->ext_autolink_base.$name;
		$title = htmlspecialchars(str_replace('[KEY]', $this->ext_autolink_base.$name, $this->ext_autolink_title));
		
		if ($this->ext_autolink_own !== false) {
			// own site
			if ($this->ext_autolink_own) {
				// other xpWiki
				return $this->ext_autolink_func->make_pagelink($page, $name, '', '', 'ext_autolink');
			} else {
				// own xpWiki
				return $this->func->make_pagelink($page, $name, '', '', 'autolink');
			}
		} else {
			$target = ($this->root->link_target)? ' target="' . $this->root->link_target . '"' : '';
			if ($this->ext_autolink_enc_conv) {
				$page = mb_convert_encoding($page, $this->ext_autolink_enc, $this->cont['CONTENT_CHARSET']);
			}
			if ($this->ext_autolink_pat) {
				if (isset($this->ext_autolink_replace['from'])) {
					$_url = str_replace($this->ext_autolink_replace['from'], $this->ext_autolink_replace['func']($page), $this->ext_autolink_pat); 
				}
				return '<a href="'.$_url.'" title="'.$title.'" class="ext_autolink"' . $target . '>'.htmlspecialchars($name).'</a>';
			} else {
				return '<a href="'.$this->ext_autolink_url.'?'.rawurlencode($page).'" title="'.$title.'" class="ext_autolink"' . $target . '>'.htmlspecialchars($name).'</a>';
			}
		}
	}
	function get_ext_autolink($autolink, $valid) {

		// check valid pages.
		if (is_string($valid) && isset($this->root->vars['page'])) {
			$_check = false;
			foreach(explode('&', $valid) as $_valid) {
				if ($_valid && strpos($this->root->vars['page'], $_valid) === 0) {
					$_check = true;
					break;
				}
			}
			if (! $_check ) return array('',0);
		}
		
		// initialize
		$inits = array(
			'url'   => '' ,
			'urldat'=> 0 ,
			'case_i'=> 0 ,
			'base'  => '' ,
			'len'   => 3 ,
			'enc'   => $this->cont['CONTENT_CHARSET'] ,
			'cache' => 10 ,
			'title' => 'Ext:[KEY]' ,
			'pat'   => ''
		);
		$autolink = array_merge($inits, $autolink);
		$this->ci = $autolink['case_i'];
		
		if (preg_match('#^https?://#', $autolink['url'])) {
			$this->ext_autolink_own = false;
		} else {
			$this->ext_autolink_own = $autolink['url'];
		}
		
		// plain_db_write() ����ƤФ�Ƥ��������Wiki�ʳ��ϥѥ�
		if (!empty($this->root->rtf['is_init']) && $this->ext_autolink_own !== '') {
			return '';
		}
		
		$autolink['base'] = trim($autolink['base'],'/');
		
		$this->ext_autolink_enc_conv = (strtoupper($this->cont['CONTENT_CHARSET']) !== strtoupper($autolink['enc']));
		
		if ($autolink['urldat']){
			$target = $autolink['url'];
		} else {
			$target = ($this->ext_autolink_enc_conv)?
				mb_convert_encoding($autolink['base'], $autolink['enc'], $this->cont['CONTENT_CHARSET']) : $autolink['base'];
			$target = $autolink['url'].'?plugin=api&pcmd=autolink&base='.rawurlencode($target);
		}
		$cache = $this->cont['CACHE_DIR'].md5($target).'.extautolink';
		
		// ��ʣ��Ͽ�����å�
		if (isset($this->root->rtf['get_ext_autolink_done'][$target])) {
			return '';
		}
		$this->root->rtf['get_ext_autolink_done'][$target] = true;
		
		$cache_min = intval(max($autolink['cache'], 10));
		// ����xpWiki�ʳ� & ����å��夢�� & ����å��夬ͭ���ϰ�
		if ($this->ext_autolink_own !== '' && file_exists($cache) && filemtime($cache) + $cache_min * 60 > time()) {
			$pat = join('',file($cache));
			if ($this->ext_autolink_own !== false) {
					//$obj = new XpWiki($this->ext_autolink_own);
					$obj = & XpWiki::getSingleton($this->ext_autolink_own);
					$obj->init('#RenderMode');
					$this->ext_autolink_func = & $obj->func;	
					$this->ci = $obj->root->page_case_insensitive;			
			}
		} else {
			if ($this->ext_autolink_own !== false) {
				if ($this->ext_autolink_own) {
					//$obj = new XpWiki($this->ext_autolink_own);
					$obj = & XpWiki::getSingleton($this->ext_autolink_own);
					$obj->init('#RenderMode');
					$this->ext_autolink_func = & $obj->func;
					$this->ci = $obj->root->page_case_insensitive;
					$plugin = & $obj->func->get_plugin_instance('api');
				} else {
					$this->ci = $this->root->page_case_insensitive;
					$plugin = & $this->func->get_plugin_instance('api');
				}
				$pat = $plugin->autolink(true, $autolink['base']);
			} else {
				$data = $this->func->http_request($target);
				if ($data['rc'] !== 200) {
					$pat = '';
				} else {
					$pat = ($this->ext_autolink_enc_conv)?
						mb_convert_encoding($data['data'], $this->cont['CONTENT_CHARSET'], $autolink['enc']) : $data['data'];
					$pat = trim($pat);
					@list($pat1, $pat2) = preg_split('/[\r\n]+/',$pat);
					// check regex pattern
					if ($pat1) {
						foreach(explode("\t", $pat1) as $_pat) {
							if (preg_match('/('.$_pat.')/s','') === false){
								$pat1 = '';
								break;
							}
						}
					}
					if ($pat2) {
						foreach(explode("\t", $pat2) as $_pat) {
							if (preg_match('/('.$_pat.')/s','') === false){
								$pat2 = '';
								break;
							}
						}
					}
					$pat = '';
					if ($pat1) { $pat = $pat1; }
					if ($pat2) { $pat .= "\t" . $pat2; }
				}
			}
			$fp = fopen($cache, 'w');
			fwrite($fp, $pat);
			fclose($fp);
		}
		$this->ext_autolink_url = $autolink['url'];
		$this->ext_autolink_base = ($autolink['base'])? $autolink['base'] . '/' : '';
		$this->ext_autolink_len = intval($autolink['len']);
		$this->ext_autolink_enc = (isset($autolink['enc']))? $autolink['enc'] : '';
		$this->ext_autolink_pat = (isset($autolink['pat']))? $autolink['pat'] : '';
		$this->ext_autolink_title = (isset($autolink['title']))? $autolink['title'] : 'Ext: [KEY]';
		
		if ($this->ext_autolink_pat) {
			if (strpos($this->ext_autolink_pat, '[URL_ENCODE]') !== false) {
				$this->ext_autolink_replace['from'] = '[URL_ENCODE]';
				$this->ext_autolink_replace['func'] = create_function('$key', 'return urlencode($key);');
			} else if (strpos($this->ext_autolink_pat, '[WIKI_ENCODE]') !== false) {
				$this->ext_autolink_replace['from'] = '[WIKI_ENCODE]';
				$this->ext_autolink_replace['func'] = create_function('$key', 'return XpWikiFunc::encode($key);');
			} else if (strpos($this->ext_autolink_pat, '[EWORDS_ENCODE]') !== false) {
				$this->ext_autolink_replace['from'] = '[EWORDS_ENCODE]';
				$this->ext_autolink_replace['func'] = create_function('$key', 'return str_replace(array(\'%\',\'.\'), array(\'\',\'2E\'), urlencode($key));');
			}
		}
		
		return $pat;
	}
}
?>