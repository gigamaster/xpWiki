<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: rename.inc.php,v 1.6 2007/07/31 03:03:38 nao-pon Exp $
//
// Rename plugin: Rename page-name and related data
//
// Usage: http://path/to/pukiwikiphp?plugin=rename[&refer=page_name]

class xpwiki_plugin_rename extends xpwiki_plugin {
	function plugin_rename_init () {

		// �������̥⡼�ɻ���
		// �ص��塢�������󤷤Ƥ��ʤ��Ƥ�ѥ���ɤǼ¹ԤǤ���褦�ˤ��Ƥ�����
		if ($this->root->userinfo['admin'] && $this->root->module['platform'] == "xoops") {
			$this->root->runmode = "xoops_admin";
		}
	}
	
	function plugin_rename_action()
	{
	//	global $whatsnew;
	
		if ($this->cont['PKWK_READONLY']) $this->func->die_message('PKWK_READONLY prohibits this');
	
		$method = $this->plugin_rename_getvar('method');
		if ($method == 'regex') {
			$src = $this->plugin_rename_getvar('src');
			if ($src == '') return $this->plugin_rename_phase1();
	
			$src_pattern = '/' . preg_quote($src, '/') . '/';
			$arr0 = preg_grep($src_pattern, $this->func->get_existpages());
			if (! is_array($arr0) || empty($arr0))
				return $this->plugin_rename_phase1('nomatch');
	
			$dst = $this->plugin_rename_getvar('dst');
			$arr1 = preg_replace($src_pattern, $dst, $arr0);
			foreach ($arr1 as $page)
				if (! $this->func->is_pagename($page))
					return $this->plugin_rename_phase1('notvalid');
	
			return $this->plugin_rename_regex($arr0, $arr1);
	
		} else {
			// $method == 'page'
			$page  = $this->plugin_rename_getvar('page');
			$refer = $this->plugin_rename_getvar('refer');
	
			if ($refer == '') {
				return $this->plugin_rename_phase1();
	
			} else if (! $this->func->is_page($refer)) {
				return $this->plugin_rename_phase1('notpage', $refer);
	
			} else if ($refer == $this->root->whatsnew) {
				return $this->plugin_rename_phase1('norename', $refer);
	
			} else if ($page == '' || $page == $refer) {
				return $this->plugin_rename_phase2();
	
			} else if (! $this->func->is_pagename($page)) {
				return $this->plugin_rename_phase2('notvalid');
	
			} else {
				return $this->plugin_rename_refer();
			}
		}
	}
	
	// �ѿ����������
	function plugin_rename_getvar($key)
	{
	//	global $vars;
		return isset($this->root->vars[$key]) ? $this->root->vars[$key] : '';
	}
	
	// ���顼��å���������
	function plugin_rename_err($err, $page = '')
	{
	//	global $_rename_messages;
	
		if ($err == '') return '';
	
		$body = $this->root->_rename_messages['err_' . $err];
		if (is_array($page)) {
			$tmp = '';
			foreach ($page as $_page) $tmp .= '<br />' . $_page;
			$page = $tmp;
		}
		if ($page != '') $body = sprintf($body, htmlspecialchars($page));
	
		$msg = sprintf($this->root->_rename_messages['err'], $body);
		return $msg;
	}
	
	//����ʳ�:�ڡ���̾�ޤ�������ɽ��������
	function plugin_rename_phase1($err = '', $page = '')
	{
	//	global $script, $_rename_messages;
	
		$msg    = $this->plugin_rename_err($err, $page);
		$refer  = $this->plugin_rename_getvar('refer');
		$method = $this->plugin_rename_getvar('method');
	
		$radio_regex = $radio_page = '';
		if ($method == 'regex') {
			$radio_regex = ' checked="checked"';
		} else {
			$radio_page  = ' checked="checked"';
		}
		$select_refer = $this->plugin_rename_getselecttag($refer);
	
		$s_src = htmlspecialchars($this->plugin_rename_getvar('src'));
		$s_dst = htmlspecialchars($this->plugin_rename_getvar('dst'));
	
		$ret = array();
		$ret['msg']  = $this->root->_rename_messages['msg_title'];
		$ret['body'] = <<<EOD
$msg
<form action="{$this->root->script}" method="post">
 <div>
  <input type="hidden" name="plugin" value="rename" />
  <input type="radio"  name="method" id="_p_rename_page" value="page"$radio_page />
  <label for="_p_rename_page">{$this->root->_rename_messages['msg_page']}:</label>$select_refer<br />
  <input type="radio"  name="method" id="_p_rename_regex" value="regex"$radio_regex />
  <label for="_p_rename_regex">{$this->root->_rename_messages['msg_regex']}:</label><br />
  <label for="_p_rename_from">From:</label><br />
  <input type="text" name="src" id="_p_rename_from" size="80" value="$s_src" /><br />
  <label for="_p_rename_to">To:</label><br />
  <input type="text" name="dst" id="_p_rename_to"   size="80" value="$s_dst" /><br />
  <input type="submit" value="{$this->root->_rename_messages['btn_next']}" /><br />
 </div>
</form>
EOD;
		return $ret;
	}
	
	//�����ʳ�:������̾��������
	function plugin_rename_phase2($err = '')
	{
	//	global $script, $_rename_messages;
	
		$msg   = $this->plugin_rename_err($err);
		$page  = $this->plugin_rename_getvar('page');
		$refer = $this->plugin_rename_getvar('refer');
		if ($page == '') $page = $refer;
	
		$msg_related = '';
		$related = $this->plugin_rename_getrelated($refer);
		if (! empty($related))
			$msg_related = '<label for="_p_rename_related">' . $this->root->_rename_messages['msg_do_related'] . '</label>' .
		'<input type="checkbox" name="related" id="_p_rename_related" value="1" checked="checked" /><br />';
	
		$msg_rename = sprintf($this->root->_rename_messages['msg_rename'], $this->func->make_pagelink($refer));
		$s_page  = htmlspecialchars($page);
		$s_refer = htmlspecialchars($refer);
	
		$ret = array();
		$ret['msg']  = $this->root->_rename_messages['msg_title'];
		$ret['body'] = <<<EOD
$msg
<form action="{$this->root->script}" method="post">
 <div>
  <input type="hidden" name="plugin" value="rename" />
  <input type="hidden" name="refer"  value="$s_refer" />
  $msg_rename<br />
  <label for="_p_rename_newname">{$this->root->_rename_messages['msg_newname']}:</label>
  <input type="text" name="page" id="_p_rename_newname" size="80" value="$s_page" /><br />
  $msg_related
  <input type="submit" value="{$this->root->_rename_messages['btn_next']}" /><br />
 </div>
</form>
EOD;
		if (! empty($related)) {
			$ret['body'] .= '<hr /><p>' . $this->root->_rename_messages['msg_related'] . '</p><ul>';
			sort($related);
			foreach ($related as $name)
				$ret['body'] .= '<li>' . $this->func->make_pagelink($name) . '</li>';
			$ret['body'] .= '</ul>';
		}
		return $ret;
	}
	
	//�ڡ���̾�ȴ�Ϣ����ڡ�������󤷡�phase3��
	function plugin_rename_refer()
	{
		$page  = $this->plugin_rename_getvar('page');
		$refer = $this->plugin_rename_getvar('refer');
	
		$pages[$this->func->encode($refer)] = $this->func->encode($page);
		if ($this->plugin_rename_getvar('related') != '') {
			$from = $this->func->strip_bracket($refer);
			$to   = $this->func->strip_bracket($page);
			foreach ($this->plugin_rename_getrelated($refer) as $_page)
				$pages[$this->func->encode($_page)] = $this->func->encode(str_replace($from, $to, $_page));
		}
		return $this->plugin_rename_phase3($pages);
	}
	
	//����ɽ���ǥڡ������ִ�
	function plugin_rename_regex($arr_from, $arr_to)
	{
		$exists = array();
		foreach ($arr_to as $page)
			if ($this->func->is_page($page))
				$exists[] = $page;
	
		if (! empty($exists)) {
			return $this->plugin_rename_phase1('already', $exists);
		} else {
			$pages = array();
			foreach ($arr_from as $refer)
				$pages[$this->func->encode($refer)] = $this->func->encode(array_shift($arr_to));
			return $this->plugin_rename_phase3($pages);
		}
	}
	
	function plugin_rename_phase3($pages)
	{
	//	global $script, $_rename_messages;
	
		$msg = $input = '';
		$files = $this->plugin_rename_get_files($pages);
	
		$exists = array();
		foreach ($files as $_page=>$arr)
			foreach ($arr as $old=>$new)
				if (file_exists($new))
					$exists[$_page][$old] = $new;
	
		$pass = $this->plugin_rename_getvar('pass');
		$pmode = $this->plugin_rename_getvar('pmode');
		if ($pmode === 'proceed' && $this->func->pkwk_login($pass)) {
			return $this->plugin_rename_proceed($pages, $files, $exists);
		} else if ($pass != '') {
			$msg = $this->plugin_rename_err('adminpass');
		}
	
		$method = $this->plugin_rename_getvar('method');
		if ($method == 'regex') {
			$s_src = htmlspecialchars($this->plugin_rename_getvar('src'));
			$s_dst = htmlspecialchars($this->plugin_rename_getvar('dst'));
			$msg   .= $this->root->_rename_messages['msg_regex'] . '<br />';
			$input .= '<input type="hidden" name="method" value="regex" />';
			$input .= '<input type="hidden" name="src"    value="' . $s_src . '" />';
			$input .= '<input type="hidden" name="dst"    value="' . $s_dst . '" />';
		} else {
			$s_refer   = htmlspecialchars($this->plugin_rename_getvar('refer'));
			$s_page    = htmlspecialchars($this->plugin_rename_getvar('page'));
			$s_related = htmlspecialchars($this->plugin_rename_getvar('related'));
			$msg   .= $this->root->_rename_messages['msg_page'] . '<br />';
			$input .= '<input type="hidden" name="method"  value="page" />';
			$input .= '<input type="hidden" name="refer"   value="' . $s_refer   . '" />';
			$input .= '<input type="hidden" name="page"    value="' . $s_page    . '" />';
			$input .= '<input type="hidden" name="related" value="' . $s_related . '" />';
		}
	
		if (! empty($exists)) {
			$msg .= $this->root->_rename_messages['err_already_below'] . '<ul>';
			foreach ($exists as $page=>$arr) {
				$msg .= '<li>' . $this->func->make_pagelink($this->func->decode($page));
				$msg .= $this->root->_rename_messages['msg_arrow'];
				$msg .= htmlspecialchars($this->func->decode($pages[$page]));
				if (! empty($arr)) {
					$msg .= '<ul>' . "\n";
					foreach ($arr as $ofile=>$nfile)
						$msg .= '<li>' . $ofile .
					$this->root->_rename_messages['msg_arrow'] . $nfile . '</li>' . "\n";
					$msg .= '</ul>';
				}
				$msg .= '</li>' . "\n";
			}
			$msg .= '</ul><hr />' . "\n";
	
			$input .= '<input type="radio" name="exist" value="0" checked="checked" />' .
			$this->root->_rename_messages['msg_exist_none'] . '<br />';
			$input .= '<input type="radio" name="exist" value="1" />' .
			$this->root->_rename_messages['msg_exist_overwrite'] . '<br />';
		}
		
		$passform = ($this->root->userinfo['admin'])? '' :
			'<label for="_p_rename_adminpass">'.$this->root->_rename_messages['msg_adminpass'].'</label>
  <input type="password" name="pass" id="_p_rename_adminpass" value="" />';
		
		$ret = array();
		$ret['msg'] = $this->root->_rename_messages['msg_title'];
		$ret['body'] = <<<EOD
<p>$msg</p>
<form action="{$this->root->script}" method="post">
 <div>
  <input type="hidden" name="plugin" value="rename" />
  <input type="hidden" name="pmode" value="proceed" />
  $input
  $passform
  <input type="submit" value="{$this->root->_rename_messages['btn_submit']}" />
 </div>
</form>
<p>{$this->root->_rename_messages['msg_confirm']}</p>
EOD;
	
		ksort($pages);
		$ret['body'] .= '<ul>' . "\n";
		foreach ($pages as $old=>$new)
			$ret['body'] .= '<li>' .  $this->func->make_pagelink($this->func->decode($old)) .
			$this->root->_rename_messages['msg_arrow'] .
			htmlspecialchars($this->func->decode($new)) .  '</li>' . "\n";
		$ret['body'] .= '</ul>' . "\n";
		return $ret;
	}
	
	function plugin_rename_get_files($pages)
	{
		$files = array();
		$dirs  = array($this->cont['BACKUP_DIR'], $this->cont['DIFF_DIR'], $this->cont['DATA_DIR']);
		if ($this->func->exist_plugin_convert('attach'))  $dirs[] = $this->cont['UPLOAD_DIR'];
		if ($this->func->exist_plugin_convert('counter')) $dirs[] = $this->cont['COUNTER_DIR'];
		// and more ...
	
		$matches = array();
		foreach ($dirs as $path) {
			$dir = opendir($path);
			if (! $dir) continue;
	
			while ($file = readdir($dir)) {
				if ($file == '.' || $file == '..') continue;
	
				foreach ($pages as $from=>$to) {
					$pattern = '/^' . str_replace('/', '\/', $from) . '([._].+)$/';
					if (! preg_match($pattern, $file, $matches))
						continue;
	
					$newfile = $to . $matches[1];
					$files[$from][$path . $file] = $path . $newfile;
				}
			}
		}
		return $files;
	}
	
	function plugin_rename_proceed($pages, $files, $exists)
	{
	//	global $now, $_rename_messages;
	
		if ($this->plugin_rename_getvar('exist') == '')
			foreach ($exists as $key=>$arr)
				unset($files[$key]);
	
		foreach ($files as $page=>$arr) {
			foreach ($arr as $old=>$new) {
				@set_time_limit(30);
				if (isset($exists[$page][$old]) && $exists[$page][$old])
					unlink($new);
				rename($old, $new);
				
				// link�ǡ����١����򹹿����� BugTrack/327 arino
				//$this->func->links_update($old);
				//$this->func->links_update($new);
			}
		}
	
		$postdata = $this->func->get_source($this->cont['PLUGIN_RENAME_LOGPAGE']);
		$postdata[] = '*' . $this->root->now . "\n";
		if ($this->plugin_rename_getvar('method') == 'regex') {
			$postdata[] = '-' . $this->root->_rename_messages['msg_regex'] . "\n";
			$postdata[] = '--From:[[' . $this->plugin_rename_getvar('src') . ']]' . "\n";
			$postdata[] = '--To:[['   . $this->plugin_rename_getvar('dst') . ']]' . "\n";
		} else {
			$postdata[] = '-' . $this->root->_rename_messages['msg_page'] . "\n";
			$postdata[] = '--From:[[' . $this->plugin_rename_getvar('refer') . ']]' . "\n";
			$postdata[] = '--To:[['   . $this->plugin_rename_getvar('page')  . ']]' . "\n";
		}
	
		if (! empty($exists)) {
			$postdata[] = "\n" . $this->root->_rename_messages['msg_result'] . "\n";
			foreach ($exists as $page=>$arr) {
				$postdata[] = '-' . $this->func->decode($page) .
				$this->root->_rename_messages['msg_arrow'] . $this->func->decode($pages[$page]) . "\n";
				foreach ($arr as $ofile=>$nfile)
					$postdata[] = '--' . $ofile .
					$this->root->_rename_messages['msg_arrow'] . $nfile . "\n";
			}
			$postdata[] = '----' . "\n";
		}
	
		foreach ($pages as $old=>$new) {
			$postdata[] = '-' . $this->func->decode($old) .
			$this->root->_rename_messages['msg_arrow'] . $this->func->decode($new) . "\n";
			
			// pginfo DB ����
			$this->func->pginfo_rename_db_write($this->func->decode($old), $this->func->decode($new));
		}
		
		// �����ξ��ͤϥ����å����ʤ���
	
		// �ե�����ν񤭹���
		$this->func->page_write($this->cont['PLUGIN_RENAME_LOGPAGE'], join('', $postdata));
	
		//������쥯��
		$page = $this->plugin_rename_getvar('page');
		if ($page == '') $page = $this->cont['PLUGIN_RENAME_LOGPAGE'];
	
		$this->func->send_location($page);
	}
	
	function plugin_rename_getrelated($page)
	{
		$related = array();
		$pages = $this->func->get_existpages();
		$pattern = '/(?:^|\/)' . preg_quote($this->func->strip_bracket($page), '/') . '(?:\/|$)/';
		foreach ($pages as $name) {
			if ($name == $page) continue;
			if (preg_match($pattern, $name)) $related[] = $name;
		}
		return $related;
	}
	
	function plugin_rename_getselecttag($page)
	{
	//	global $whatsnew;
	
		$pages = array();
		foreach ($this->func->get_existpages() as $_page) {
			if ($_page == $this->root->whatsnew) continue;
	
			$selected = ($_page == $page) ? ' selected' : '';
			$s_page = htmlspecialchars($_page);
			$pages[$_page] = '<option value="' . $s_page . '"' . $selected . '>' .
			$s_page . '</option>';
		}
		ksort($pages);
		$list = join("\n" . ' ', $pages);
	
		return <<<EOD
<select name="refer">
 <option value=""></option>
 $list
</select>
EOD;
	
	}
}
?>