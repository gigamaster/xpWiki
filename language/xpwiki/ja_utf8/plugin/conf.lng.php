<?php
/*
 * Created on 2008/01/24 by nao-pon http://hypweb.net/
 * $Id: conf.lng.php,v 1.2 2008/01/30 07:29:41 nao-pon Exp $
 */

$msg = array(
	'title_form' => 'xpWiki 環境設定',
	'title_done' => 'xpWiki 環境設定完了',
	'btn_submit' => 'この設定を適用する',
	'msg_done' => '以下の内容で、$cache_file に保存しました。',
	'title_description' => '環境設定の説明',
	'msg_description' => '<p>この環境設定では、pukiwiki.ini.php の設定項目で、代表的な項目のみを設定することができます。</p>'
	                   . '<p>$trust_ini_file には、このほかにも数多くの設定項目があります。</p>'
	                   . '<p>この設定画面にない設定項目を変更したい場合は、$html_ini_file に、該当の項目を抜き出して設定をしてください。</p>'
	                   . '<p>※ この設定画面の設定内容が最優先で適用されます。</p>',

	'Yes' => 'はい',
	'No' => 'いいえ',

	'PKWK_READONLY' => array(
		'caption'     => '読み取り専用にする',
		'description' => '読み取り専用にすると、管理者も含め誰も編集することができません。',
	),

	'function_freeze' => array(
		'caption'     => '凍結機能を有効にする',
		'description' => '',
	),

	'adminpass' => array(
		'caption'     => '管理者パスワード',
		'description' => 'クリアテキストでも指定できますが、<a href="?cmd=md5" target="_blank">cmd=md5</a> を使い暗号化した文字列を入力してください。<br />'
		               . 'XOOPS環境下では、管理者としてログインすれば、管理者パスワードは必要ないため"{x-php-md5}!"としてすべて認証不可としても問題ありません。',
	),

	'html_head_title' => array(
		'caption'     => '&lt;head&gt;内の&lt;title&gt;設定',
		'description' => 'HTML の &lt;head&gt; 内 &lt;title&gt;タグに表示する内容を設定します。<br />'
		               . '<b>$page_title</b>: ページ名, <b>$content_title</b>: ページタイトル, <b>$module_title</b>:モジュール名 に置換されます。',
	),

	'modifier' => array(
		'caption'     => '管理者名',
		'description' => '',
	),
	
	'modifierlink' => array(
		'caption'     => '管理者のサイトURL',
		'description' => '',
	),
	
	'notify' => array(
		'caption'     => 'ページ更新時メール通知する',
		'description' => 'ページか更新されたら、管理者にメール通知します。',
	),

	'notify_diff_only' => array(
		'caption'     => 'メール通知は差分のみ',
		'description' => 'ページ更新時のメール通知は、変更差分のみ送信します。「いいえ」を選択すると全文送信されます。',
	),

	'defaultpage' => array(
		'caption'     => 'デフォルトページ',
		'description' => 'ページを指定しない場合に表示されるページ、トップページです。',
	),
	
	'page_case_insensitive' => array(
		'caption'     => 'ページ名の小文字・大文字を区別しない',
		'description' => 'ページ名の内、英字(アルファベット)の大文字・小文字を区別しません。',
	),
	
	'SKIN_NAME' => array(
		'caption'     => 'デフォルトのスキン名',
		'description' => 'デフォルトのスキン名を指定します。',
	),
	
	'SKIN_CHANGER' => array(
		'caption'     => 'スキンの変更を許可する',
		'description' => '「はい」を選択するとユーザーがスキンを選択できるようになります。<br />'
		               . 'また、tdiary プラグインなどを使いページ毎で指定することも可能になります。',
	),
	
	'referer' => array(
		'caption'     => '参照元を集計する',
		'description' => '閲覧者がどこからページに訪れたかをページ毎に集計する機能です。',
	),
	
	'allow_pagecomment' => array(
		'caption'     => 'ページコメント機能を有効にする',
		'description' => 'd3forum モジュールのコメント統合を使いページ毎にコメント機能を持たせることができます。<br />'
		               . '実際に使用するには、一般設定でコメント統合の設定をする必要があります。',
	),

	'nowikiname' => array(
		'caption'     => 'WikiName を無効にする',
		'description' => 'WikiName への自動リンク機能を無効にします。',
	),

	'pagename_num2str' => array(
		'caption'     => 'ページ名の具体表示をする',
		'description' => '二階層以上のページ名で最終階層部分が、数字と-(ハイフン)で構成されている場合にその部分をページタイトルに置換して表示します。',
	),

	'static_url' => array(
		'caption'     => '静的ページ風URLにする',
		'description' => 'ページURLを「ページID.html」とし、静的なページのURLのように振舞います。<br />'
		               . 'ただし、この機能を有効にした場合には .htaccess にて以下の記述を有効にする必要があります。<br />'
		               . '<code>RewriteEngine on<br />RewriteRule ^([0-9]+)\.html$ index.php?pgid=$1 [qsappend,L]</code>',
	),

	'link_target' => array(
		'caption'     => '外部リンクの target 属性',
		'description' => '',
	),

	'class_extlink' => array(
		'caption'     => '外部リンクの class 属性',
		'description' => '',
	),

	'nofollow_extlink' => array(
		'caption'     => '外部リンクに nofollow 属性をつける',
		'description' => '',
	),

	'autolink' => array(
		'caption'     => 'オートリンク有効ページ名バイト数',
		'description' => 'オートリンクとは、存在するページ名に自動的にリンクをする機能です。<br />'
		               . '有効になるページバイト数を入力します。(0 で無効)<br />'
		               . '文字数ではなくバイト数指定となりますので、ご注意ください。',
		'extention'   => 'バイト',
	),

	'autolink_omissible_upper' => array(
		'caption'     => '上階層を省略したオートリンク',
		'description' => '上階層を省略してもオートリンクする設定です。オートリンクが有効になっている必要があります。<br />'
		               . '/hoge/hoge というページで fuga と書くことで /hoge/fuga にオートリンクします。<br />'
		               . 'オートリンクと同様、バイト数指定となります。(fuga に相当するバイト数で指定)',
		'extention'   => 'バイト',
	),

	'autoalias' => array(
		'caption'     => 'オートエイリアス有効バイト数',
		'description' => '「指定した単語」に対し、指定した「URI、ページ、またはInterWiki」に対するリンクを自動的に張る機能です。<br />'
		               . 'オートリンクと同様、バイト数指定となります。(置換される文字列のバイト数で指定。0 で無効)<br />'
		               . '設定ページ: <a href="?'.rawurlencode($this->root->aliaspage).'" target="_blank">'.$this->root->aliaspage.'</a>',
		'extention'   => 'バイト',
	),

	'autoalias_max_words' => array(
		'caption'     => 'オートエイリアスの最大単語登録数',
		'description' => '',
		'extention'   => '組',
	),

	'plugin_follow_editauth' => array(
		'caption'     => 'プラグインにページ編集権限を適用する',
		'description' => 'ページ編集権限がない場合に、プラグインでの投稿を不許可にします。',
	),

	'plugin_follow_freeze' => array(
		'caption'     => 'プラグインにページ凍結を適用する',
		'description' => 'ページが凍結されている場合に、プラグインでの投稿を不許可にします。',
	),

	'fixed_heading_anchor_edit' => array(
		'caption'     => '章単位編集を有効にする',
		'description' => '',
	),

	'paraedit_partarea' => array(
		'caption'     => '章編集の範囲',
		'description' => '章編集の範囲を設定します。<br />'
		               . '章の範囲は、Wiki書式の * で始まる見出し行で開始されます。',
		'compat'      => '次の見出しまで',
		'level'       => '同レベル以上の見出しまで',
	),

	'pagecache_min' => array(
		'caption'     => 'ページキャッシュ有効期限',
		'description' => 'ページレンダリングしたHTMLをキャッシュして高速化する場合の有効期限(単位:分)を設定します。<br />'
		               . 'ただし、ゲストアカウントアクセス時のみキャッシュされます。ページビューが多いサイトの場合は、有効にされることをお勧め致します。',
		'extention'   => '分',
	),

	'pre_width' => array(
		'caption'     => '&lt;pre&gt;のCSS:width指定',
		'description' => '&lt;pre&gt;タグに指定するCSSのwidth値を指定します。',
	),

	'pre_width_ie' => array(
		'caption'     => '&lt;pre&gt;のCSS:width指定(IE専用)',
		'description' => 'こちらはブラウザのIE専用値です。使用しているXOOPSのテーマが&lt;table&gt;構成の場合は、700px など固定値を指定すると表示の崩れが軽減されると思います。',
	),

	'pagereading_enable' => array(
		'caption'     => 'ページ名読みで分類する',
		'description' => 'ページ一覧でページ名読みを利用して分類します。',
	),

	'pagereading_kanji2kana_converter' => array(
		'caption'     => 'ページ名読み取得方法',
		'description' => 'ページ名読みを取得する方法を選択してください。',
	),

	'pagereading_kanji2kana_encoding' => array(
		'caption'     => 'ページ名読み文字処理エンコーディング',
		'description' => 'サーバーが UNIX 系なら EUC-JP, Windows 系なら Shift-JIS が標準です。',
	),

	'pagereading_chasen_path' => array(
		'caption'     => 'ChaSen の絶対パス',
		'description' => '',
	),

	'pagereading_kakasi_path' => array(
		'caption'     => 'KAKASI の絶対パス',
		'description' => '',
	),

	'pagereading_config_dict' => array(
		'caption'     => 'ページ名読みの辞書ページ名',
		'description' => 'ページ名読み取得方法が"None"の場合使用されます。',
	),

);
?>