<?php
class xpwiki_plugin_search extends xpwiki_plugin {
	function plugin_search_init () {


	// PukiWiki - Yet another WikiWikiWeb clone.
	// $Id: search.inc.php,v 1.4 2007/05/28 07:57:42 nao-pon Exp $
	//
	// Search plugin
	
	// Allow search via GET method 'index.php?plugin=search&word=keyword'
	// NOTE: Also allows DoS to your site more easily by SPAMbot or worm or ...
		$this->cont['PLUGIN_SEARCH_DISABLE_GET_ACCESS'] =  0; // 1, 0
	
		$this->cont['PLUGIN_SEARCH_MAX_LENGTH'] =  80;
		$this->cont['PLUGIN_SEARCH_MAX_BASE'] =    16; // #search(1,2,3,...,15,16)

		// ����t�@�C���̓ǂݍ���
		$this->load_language();
	}
	
	// Show a search box on a page
	function plugin_search_convert()
	{
		static $done = array();
		
		if (! empty($done[$this->xpwiki->pid])) {
			return '#search(): You already view a search box<br />' . "\n";
		} else {
			$done[$this->xpwiki->pid] = TRUE;
			$args = func_get_args();
			return $this->plugin_search_search_form('', '', $args);
		}
	}
	
	function plugin_search_action()
	{
	//	global $post, $vars, $_title_result, $_title_search, $_msg_searching;
	
		if ($this->cont['PLUGIN_SEARCH_DISABLE_GET_ACCESS']) {
			$s_word = isset($this->root->post['word']) ? htmlspecialchars($this->root->post['word']) : '';
		} else {
			$s_word = isset($this->root->vars['word']) ? htmlspecialchars($this->root->vars['word']) : '';
		}
		if (strlen($s_word) > $this->cont['PLUGIN_SEARCH_MAX_LENGTH']) {
			unset($this->root->vars['word']); // Stop using $_msg_word at lib/html.php
			$this->func->die_message('Search words too long');
		}
	
		$type = isset($this->root->vars['type']) ? $this->root->vars['type'] : '';
		$base = isset($this->root->vars['base']) ? $this->root->vars['base'] : '';
	
		if ($s_word != '') {
			// Search
			$fields = array();
			if (!empty($this->root->vars['search_name'])) $fields[] = 'name';
			if (!empty($this->root->vars['search_text'])) $fields[] = 'text';
			if (!empty($this->root->vars['search_source'])) $fields[] = 'source';
			$filed = join(',', $fields);
			
			$msg  = str_replace('$1', $s_word, $this->root->_title_result);
			$body = $this->func->do_search($this->root->vars['word'], $type, FALSE, $base, TRUE, $filed);
		} else {
			// Init
			unset($this->root->vars['word']); // Stop using $_msg_word at lib/html.php
			$msg  = $this->root->_title_search;
			$body = '<br />' . "\n" . $this->root->_msg_searching . "\n";
		}
	
		// Show search form
		$bases = ($base == '') ? array() : array($base);
		$body .= $this->plugin_search_search_form($s_word, $type, $bases);
	
		return array('msg'=>$msg, 'body'=>$body);
	}
	
	function plugin_search_search_form($s_word = '', $type = '', $bases = array())
	{
	//	global $script, $_btn_and, $_btn_or, $_btn_search;
	//	global $_search_pages, $_search_all;
	
		$and_check = $or_check = '';
		if ($type == 'OR') {
			$or_check  = ' checked="checked"';
		} else {
			$and_check = ' checked="checked"';
		}
		
		if ($s_word) {
			$search_check['name'] = (empty($this->root->vars['search_name']))? '' : ' checked="checked"';
			$search_check['text'] = (empty($this->root->vars['search_text']))? '' : ' checked="checked"';
			$search_check['source'] = (empty($this->root->vars['search_source']))? '' : ' checked="checked"';
		} else {
			$search_check['name'] = $search_check['text'] = ' checked="checked"';
			$search_check['source'] = '';
		}

		$base_option = '';
		if (!empty($bases)) {
			$base_msg = '';
			$_num = 0;
			$check = ' checked="checked"';
			foreach($bases as $base) {
				++$_num;
				if ($this->cont['PLUGIN_SEARCH_MAX_BASE'] < $_num) break;
				$label_id = '_p_search_base_id_' . $_num;
				$s_base   = htmlspecialchars($base);
				$base_str = '<strong>' . $s_base . '</strong>';
				$base_label = str_replace('$1', $base_str, $this->root->_search_pages);
				$base_msg  .=<<<EOD
 <div>
  <input type="radio" name="base" id="$label_id" value="$s_base" $check />
  <label for="$label_id">$base_label</label>
 </div>
EOD;
				$check = '';
			}
			$base_msg .=<<<EOD
  <input type="radio" name="base" id="_p_search_base_id_all" value="" />
  <label for="_p_search_base_id_all">{$this->root->_search_all}</label>
EOD;
			$base_option = '<div class="small">' . $base_msg . '</div>';
		}
		$method = $this->cont['PLUGIN_SEARCH_DISABLE_GET_ACCESS']? 'POST' : 'GET';
		return <<<EOD
<form action="{$this->root->script}" method="$method">
 <div>
  <input type="hidden" name="cmd" value="search" />
  <input type="text"  name="word" value="$s_word" size="20" />
  <input type="radio" name="type" id="_p_search_AND" value="AND" $and_check />&nbsp;<label for="_p_search_AND">{$this->root->_btn_and}</label>
  <input type="radio" name="type" id="_p_search_OR"  value="OR"  $or_check  />&nbsp;<label for="_p_search_OR">{$this->root->_btn_or}</label>
  &nbsp;<input type="submit" value="{$this->root->_btn_search}" />
  <p>
  <input type="checkbox" name="search_name" id="_p_search_name" value="1"{$search_check['name']} />&nbsp;<label for="_p_search_name">{$this->msg['btn_search_name']}</label>&nbsp;
  <input type="checkbox" name="search_text" id="_p_search_text" value="1"{$search_check['text']} />&nbsp;<label for="_p_search_text">{$this->msg['btn_search_text']}</label>&nbsp;
  <input type="checkbox" name="search_source" id="_p_search_source" value="1"{$search_check['source']} />&nbsp;<label for="_p_search_source">{$this->msg['btn_search_source']}</label>
  </p>
 </div>
$base_option
</form>
EOD;
	}
}
?>