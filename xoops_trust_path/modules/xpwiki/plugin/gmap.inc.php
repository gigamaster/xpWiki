<?php
/* Pukiwiki GoogleMaps plugin 3.1
 * http://reddog.s35.xrea.com
 * -------------------------------------------------------------------
 * Copyright (c) 2005-2012 OHTSUKA, Yoshio
 * This program is free to use, modify, extend at will. The author(s)
 * provides no warrantees, guarantees or any responsibility for usage.
 * Redistributions in any form must retain this copyright notice.
 * ohtsuka dot yoshio at gmail dot com
 * -------------------------------------------------------------------
 * 2005-09-25 1.1   -Release
 * 2006-04-20 2.0   -GoogleMaps API ver2
 * 2006-07-15 2.1   -googlemaps2_insertmarker.inc.php���ɲá�usetool���ץ������ѻߡ�
 *                   �֥�å����ν񼰤�Ȥ���褦�ˤ�����
 *                  -googlemaps2��dbclickzoom, continuouszoom���ץ������ɲá�
 *                  -googlemaps2_mark��image���ץ�����ź�ղ�����Ȥ���褦�ˤ�����
 *                  -OverViewMap, �ޥ�������å����β��ɡ�
 *                  -XSS�к���googlemaps2_mark��formatlist, formatinfo���ѻߡ�
 *                  -�ޡ������Υ����ȥ��ġ�����åפ�ɽ����
 *                  -���󥫡�̾��pukiwikigooglemaps2_�Ȥ���prefix��Ĥ���褦�ˤ�����
 * 2006-07-29 2.1.1 -include��calender_viewer�ʤ�ʣ���Υڡ������ĤΥڡ����ˤޤȤ��
 *                   ���Ϥ���ץ饰����ǥޥåפ�ɽ������ʤ��Х�������
 * 2006-08-24 2.1.2 -ñ�측���ǥޡ�����̾���ϥ��饤�Ȥ��줿���ΥХ�������
 * 2006-09-30 2.1.3 -��������,PDA�ʤ����б��ΥǥХ����ǥ�����ץȤ���Ϥ��ʤ��褦�ˤ�����
 * 2006-12-30 2.2   -�ޡ������Υե������򳫤����˲������ɤ߹��ߤ��ԤĤ褦�ˤ�����
 *                  -GMapTypeControl�򾮤���ɽ����
 *                  -Google��URL��maps.google.com����maps.google.co.jp�ˡ�
 *                  -googlemaps2��geoctrl, crossctrl���ץ������ɲá�
 *                  -googlemaps2_mark��maxurl, minzoom, maxzoom���ץ������ɲá�
 *                  -googlemaps2_insertmarker��image, maxurl, minzoom, maxzoom�����ϲ�ǽ�ˡ�
 *                  -googlemaps2_draw��fillopacity, fillcolor, inradius���ץ������ɲá�
 *                  -googlemaps2_draw��polygon���ޥ�ɤ��ɲá�
 * 2007-01-10 2.2.1 -googlemaps2��overviewctrl�Υѥ�᡼�����Ͽޤ���ư�Կ��ˤʤ�Х�������
 *                  -googlemaps2_insertmarker��include�ʤɤ�ʣ��Ʊ����ɽ�����줿�Ȥ��ε�ư�Կ�����
 * 2007-01-22 2.2.2 -googlemaps2��width, height��ñ�̤����ꤵ��Ƥ��ʤ��Ȥ��ϰ��ۤ�px���䤦��
 *                  -googlemaps2��overviewtype��auto���ɲá��ϿޤΥ����פ˥����С��ӥ塼��Ϣư��
 * 2007-01-31 2.2.3 -googlemaps2��cross����ȥ���ɽ�����˥ե������Υѥ󤬵�ư�Կ��ʤΤ�����
 *                  -Google�Υ���Pukiwiki��CSS�ˤ�ä��طʤ�Ʃ�ᤷ�ʤ���������Ū�˽�����
 * 2007-08-04 2.2.4 -IE�ǿ޷�������Ǥ��ʤ��Х�������
 *                  -googlemaps2��geoxml���ץ������ɲá�
 * 2007-09-25 2.2.5 -geoxml�ǥ��顼�������insertmarker��ư���ʤ��Х�������
 * 2007-12-01 2.3.0 -googlemaps2��geoctrl, overviewtype���ץ������ѻ�
 *                  -googlemaps2��googlebar, importicon, backlinkmarker���ץ������ɲ�
 *                  -googlemaps2_mark��maxurl���ץ������ѻߡ��ʰ��Ū��maxcontent�˥ޥåԥ󥰤�����
 *                  -googlemaps2_mark��maxcontent, maxtitle, titleispagename���ץ������ɲá�
 * 2008-10-21 2.3.1 -api�ΥС�������2.132d�˸��ꤷ����
 * 2012-10-28 3.0.0 GoogleMaps API v3 (3.10)
 *                  -�ץ饰�����̾�Τ�googlemaps3���ѹ�����
 *                  -googlemaps3
 *                      -�ѻߥ��ץ����
 *                          -key
 *                          -api
 *                          -mapctrl        zoomctrl��panctrl��ʬΥ����
 *                          -overviewwidth  v3�Ǥϥ������ѹ��Ǥ��ʤ��ߤ���
 *                          -overviewheight v3�Ǥϥ������ѹ��Ǥ��ʤ��ߤ���
 *                          -continuouszoom
 *                          -googlebar		searchctrl�ˤʤ�ޤ���
 *                          -geoxml         kml�ˤʤ�ޤ���
 *                      -�ɲå��ץ����
 *                          -zoomctrl       �Ƚ̥���ȥ���
 *                          -panctrl        ��ư����ȥ���
 *                          -rotatectrl     45�ٲ�ž����ȥ���(45�ٲ�ž���Ͽޤ��б����Ƥʤ��ȥ���)
 *                          -streetviewctrl ���ȥ꡼�ȥӥ塼����ȥ���
 *                          -searchctrl		�����ܥå���
 *                          -kml            KML�ե�����ؤ�URL,ź�եե�����̾      
 *                      -�ѹ����ץ����
 *                          -type           (�ɲ�)roadmap, terrain
 *                          -typectrl       (�ɲ�)horizontal, dropdown
 *                          -crossctrl      (�ɲ�)normal
 *                                          (�ѻ�)show
 *                  -googlemaps3_mark
 *                      -�ѻߥ��ץ����
 *                          -maxcontent    v3��̵���ʤä�
 *                          -maxtitle      v3��̵���ʤä�
 *                          -maxurl        v3��̵���ʤä�
 *                      -�ɲå��ץ����
 *                          -flat           ��������αƤ�̵����
 *
 *                  -googlemaps3_icon
 *                      -�ѹ��ʤ�
 *
 * 2012-12-01 3.1.0 GoogleMaps API v3 (3.10)
 *                  -googlemaps3
 *                      -kml���ץ������ɲá�(��geoxml)
 *  
 */

/**
 * xpwiki_plugin_gmap : Google Maps V3 for xpWiki
 * @author nao-pon http://xoops.hypweb.net/
 *
 * 2013-02-17 xpWiki �� googlemaps2 �� reddog ����� PukiWiki �� googlemaps3 �����
 *            - reddog ����� google maps �ץ饰����Ȥ������ (V2 �����ޤ�)
 *            -- �ޥå�̾�μ�ư��Ϳ mark ���ѻ��ˤ�ޥå�̾���ά�����ľ���Υޥåפ˥ݥ���Ȥ����
 *            -- insertmarker �˽�����ɲ�
 *            -- �ޥåץ��ץ��������ɲ�
 *            --- autozoom ��ư�������ʣ���ޡ��������ˤ��٤ƤΥޡ�������ɽ�������
 *            --- wikitag �ޥåפ� Wiki ��ˡɽ�����ץ����
 *            --- dropmarker (�ޡ��������ư���ƥݥ���Ȼ���)���ɲ� (V3)
 *            --- googlebar ���ץ��������� �ʻ��Ѥ��Ƥ��� API �����Ǥ˥��ݡ����оݳ��ʤΤǻȤ��ʤ��ʤ뤫���Τ�ʤ��� (V3)
 *            -- �ޡ������Ѳ����� ref �ץ饰��������Ѥ���褦�ˤ����ʥ���ͥ��뼫ư������
 *            -- ����������ͤ� 17 ���� 21 ���ѹ�
 *            -- ���������ϰϤ���ꤷ���ޡ������Τߥ������ѹ����˥�饤�Ȥ���褦�ˤ��� (V3)
 *            -- icon �αƻ��ꡢInfowindow���ֻ��ꡢ�ݥꥴ�����ΥХ����� (V3)
 *            -- insertmarker �Υե�������ͤ� cookie �ؤ���¸������ܤ����䤷�� (V3)
 *            -- insertmarker �Υե�������ͤ� cookie ��¸�� path �� '/' �˻��ꤷ�� (V3)
 */

class xpwiki_plugin_gmap extends xpwiki_plugin {

	var $map_count = array();
	var $lastmap_name;
	var $google_staticmap_url = 'https://maps.googleapis.com/maps/api/staticmap?';

	function plugin_gmap_init () {

		// ����ե�������ɤ߹���
		$this->load_language();

		$this->cont['PLUGIN_GMAP_DEF_MAPNAME'] =        'map';			//Map̾(��ư���ꤵ���)
		$this->cont['PLUGIN_GMAP_DEF_WIDTH'] =          '400px';		//����
		$this->cont['PLUGIN_GMAP_DEF_HEIGHT'] =         '400px';		//����
		$this->cont['PLUGIN_GMAP_DEF_LAT'] =            35.036198;	 	//����
		$this->cont['PLUGIN_GMAP_DEF_LNG'] =            135.732103;		//����
		$this->cont['PLUGIN_GMAP_DEF_ZOOM'] =           13;				//�������٥�
		$this->cont['PLUGIN_GMAP_DEF_AUTOZOOM'] =       false;			//��ư������Ǥ��٤ƤΥޡ�������ɽ��
		$this->cont['PLUGIN_GMAP_DEF_TYPE'] =           'roadmap';		//�ޥåפΥ�����(normal, roadmap, satellite, hybrid, terrain)
		//$this->cont['PLUGIN_GMAP_DEF_MAPCTRL'] =      'normal';		//�ޥåץ���ȥ���(none,smallzoom,small,normal,large) //�ѻ�
		$this->cont['PLUGIN_GMAP_DEF_PANCTRL'] =        'normal';		//��ư����ȥ���(none,normal)
		$this->cont['PLUGIN_GMAP_DEF_ZOOMCTRL'] =       'normal';		//�Ƚ̥���ȥ���(none,normal,small,large)
		$this->cont['PLUGIN_GMAP_DEF_TYPECTRL'] =       'normal';		//maptype���إ���ȥ���(none, normal, horizontal, dropdown)
		//$this->cont['PLUGIN_GMAP_DEF_PHYSICALCTRL'] = 'show';	 		//�Ϸ�������(none, show) //�ѻ�
		$this->cont['PLUGIN_GMAP_DEF_SCALECTRL'] =      'none';	 		//�������륳��ȥ���(none, normal)
		$this->cont['PLUGIN_GMAP_DEF_ROTATECTRL'] =     'none';			//45�ٲ�ž����ȥ���(none, normal)
		$this->cont['PLUGIN_GMAP_DEF_STREETVIEWCTRL'] = 'normal';		//���ȥ꡼�ȥӥ塼����ȥ���(none, normal)
		$this->cont['PLUGIN_GMAP_DEF_OVERVIEWCTRL'] =   'none';	 		//�����С��ӥ塼�ޥå�(none, hide, show)
		$this->cont['PLUGIN_GMAP_DEF_CROSSCTRL'] =      'none';	 		//���󥿡���������ȥ���(none, show)
		$this->cont['PLUGIN_GMAP_DEF_SEARCHCTRL'] =     'none';			//�����ܥå�������ȥ���(none, normal)
		$this->cont['PLUGIN_GMAP_DEF_OVERVIEWWIDTH'] =  '150';			//�����С��ӥ塼�ޥåפβ���
		$this->cont['PLUGIN_GMAP_DEF_OVERVIEWHEIGHT'] = '150';			//�����С��ӥ塼�ޥåפν���
		//$this->cont['PLUGIN_GMAP_DEF_API'] =            2;			//API�θ����ߴ��ѥե饰(1=1��, 2=2��). �ѻ�ͽ�ꡣ
		$this->cont['PLUGIN_GMAP_DEF_DROPMARKER'] =     'none';			//�ɥ�åץޡ�������ɽ��
		$this->cont['PLUGIN_GMAP_DEF_TOGGLEMARKER'] =   false;			//�ޡ�������ɽ�����إ����å���ɽ��
		$this->cont['PLUGIN_GMAP_DEF_NOICONNAME'] = $this->msg['unnamed_icon_caption']; //��������̵���ޡ������Υ�٥�
		$this->cont['PLUGIN_GMAP_DEF_DBCLICKZOOM'] =    true;			//���֥륯��å��ǥ����ह��(true, false)
		//$this->cont['PLUGIN_GMAP_DEF_CONTINUOUSZOOM'] =  true;		//��餫�˥����ह��(true, false)��V3���ѻ�
		$this->cont['PLUGIN_GMAP_DEF_SCROLLWHEEL'] =    true;			//�ޥ����ۥ�����ǥ����ह��(true, false)
		$this->cont['PLUGIN_GMAP_DEF_KML'] =            '';				//�ɤ߹���KML, GeoRSS��URL
		$this->cont['PLUGIN_GMAP_DEF_GOOGLEBAR'] =      false;			//GoogleBar��ɽ��
		$this->cont['PLUGIN_GMAP_DEF_IMPORTICON'] =     '';				//����������������Pukiwiki�ڡ���
		$this->cont['PLUGIN_GMAP_DEF_BACKLINKMARKER'] = false;			//�Хå���󥯤ǥޡ������򽸤��
		$this->cont['PLUGIN_GMAP_DEF_WIKITAG'] =        'hide';			//���Υޥåפ�Wiki��ˡ (none, hide, show)

		//Pukiwiki��1.4.5����������äʤɤΥǥХ������Ȥ˥ץ�ե�������Ѱդ���
		//UA�ǥ�������ڤ��ؤ���ɽ���Ǥ���褦�ˤʤä�������������Ǥ�GoogleMaps��
		//ɽ����ǽ�ʥץ�ե���������ꤹ�롣
		//�б��ǥХ����Υץ�ե�����򥫥��(,)���ڤ�ǵ������롣
		//xpWiki�ǥ��ݡ��Ȥ��Ƥ�ǥե���ȤΥץ�ե������ default,mobile,keitai ��3�ġ�
		//�桼�������ɲä����ץ�ե����뤬���ꡢ�����GoogleMaps��ɽ����ǽ�ʥǥХ����ʤ��ɲä��뤳�ȡ�
		//�ޤ��ǥե���ȤΥץ�ե������"default"�ʳ���̾���ˤ��Ƥ�������ѹ����뤳�ȡ�
		//��:GoogleMaps�Ϸ���(���饱��)���ä�ɽ���Ǥ��ʤ���
		$this->cont['PLUGIN_GMAP_PROFILE'] =  'default,mobile';

		// This plugins config
		$this->conf['ApiVersion'] = '3';

		if ($this->cont['UA_PROFILE'] === 'mobile') {
			$this->conf['StaticMapSizeW'] = 480;
			$this->conf['StaticMapSizeH'] = 400;
			$this->conf['mapsize'] = 'width="480" height="400"';
		} else {
			$this->conf['StaticMapSizeW'] = 240;
			$this->conf['StaticMapSizeH'] = 200;
			$this->conf['mapsize'] = (isset($this->root->keitai_imageTwiceDisplayWidth) && $this->root->keitai_imageTwiceDisplayWidth && $this->root->keitai_display_width >= $this->root->keitai_imageTwiceDisplayWidth)? 'width="480" height="400"' : 'width="240" height="200"';
		}
		$this->conf['navsize'] = 'width="60" height="40"';

		$this->conf['StaticMapSize'] = $this->conf['StaticMapSizeW'] . 'x' . $this->conf['StaticMapSizeH'];
	}

	function plugin_gmap_is_supported_profile () {
		if ($this->cont['UA_PROFILE']) {
			return in_array($this->cont['UA_PROFILE'], preg_split('/[\s,]+/', $this->cont['PLUGIN_GMAP_PROFILE']));
		} else {
			return 1;
		}
	}

	function plugin_gmap_get_default () {
		return array(
			'mapname'		 => $this->cont['PLUGIN_GMAP_DEF_MAPNAME'],
			'key'			 => $this->root->google_api_key,
			'lat'			 => $this->cont['PLUGIN_GMAP_DEF_LAT'],
			'lng'			 => $this->cont['PLUGIN_GMAP_DEF_LNG'],
			'width'			 => $this->cont['PLUGIN_GMAP_DEF_WIDTH'],
			'height'		 => $this->cont['PLUGIN_GMAP_DEF_HEIGHT'],
			'zoom'			 => $this->cont['PLUGIN_GMAP_DEF_ZOOM'],
			'autozoom'       => $this->cont['PLUGIN_GMAP_DEF_AUTOZOOM'],
			//'mapctrl'		 => $this->cont['PLUGIN_GMAP_DEF_MAPCTRL'],//�ѻ�
			'panctrl'		 => $this->cont['PLUGIN_GMAP_DEF_PANCTRL'],
			'zoomctrl'		 => $this->cont['PLUGIN_GMAP_DEF_ZOOMCTRL'],
			'type'			 => $this->cont['PLUGIN_GMAP_DEF_TYPE'],
			'typectrl'		 => $this->cont['PLUGIN_GMAP_DEF_TYPECTRL'],
			'scalectrl'		 => $this->cont['PLUGIN_GMAP_DEF_SCALECTRL'],
			'rotatectrl'     => $this->cont['PLUGIN_GMAP_DEF_ROTATECTRL'],
			'streetviewctrl' => $this->cont['PLUGIN_GMAP_DEF_STREETVIEWCTRL'],
			'crossctrl'		 => $this->cont['PLUGIN_GMAP_DEF_CROSSCTRL'],
			'searchctrl'	 => $this->cont['PLUGIN_GMAP_DEF_SEARCHCTRL'],
			'overviewctrl'	 => $this->cont['PLUGIN_GMAP_DEF_OVERVIEWCTRL'],
			//'overviewwidth'	 => $this->cont['PLUGIN_GMAP_DEF_OVERVIEWWIDTH'], //�ѻ�
			//'overviewheight' => $this->cont['PLUGIN_GMAP_DEF_OVERVIEWHEIGHT'], //�ѻ�
			'googlebar'		 => $this->cont['PLUGIN_GMAP_DEF_GOOGLEBAR'],
			'dbclickzoom'	 => $this->cont['PLUGIN_GMAP_DEF_DBCLICKZOOM'],
			'scrollwheel'    => $this->cont['PLUGIN_GMAP_DEF_SCROLLWHEEL'],
			'dropmarker'	 => $this->cont['PLUGIN_GMAP_DEF_DROPMARKER'],
			'togglemarker'	 => $this->cont['PLUGIN_GMAP_DEF_TOGGLEMARKER'],
			'wikitag'        => $this->cont['PLUGIN_GMAP_DEF_WIKITAG'],
			'kml'			 => $this->cont['PLUGIN_GMAP_DEF_KML'],
			'noiconname'	 => $this->cont['PLUGIN_GMAP_DEF_NOICONNAME'],
			'importicon'	 => $this->cont['PLUGIN_GMAP_DEF_IMPORTICON'],
			'backlinkmarker' => $this->cont['PLUGIN_GMAP_DEF_BACKLINKMARKER'],
			//'physicalctrl'   => $this->cont['PLUGIN_GMAP_DEF_PHYSICALCTRL'], //�ѻ�
		);
	}

	function plugin_gmap_convert() {
		static $init = true;
		$args = func_get_args();
		$ret = $this->plugin_gmap_output($init, $args, 'block');
		$init = false;
		return $ret;
	}

	function plugin_gmap_inline() {
		if (isset($this->root->rtf['GET_HEADING_INIT'])) return 'Google Maps';
		static $init = true;
		$args = func_get_args();
		array_pop($args);
		$ret = $this->plugin_gmap_output($init, $args, 'inline-block');
		$init = false;
		return $ret;
	}

	function plugin_gmap_action() {
		$action = isset($this->root->vars['action']) ? $this->root->vars['action'] : '';
		$page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';

		switch($action) {
			case '':
				break;
			// maxContent�ѤΥ쥤�����ȥ�������ǥڡ�����body�����
			case 'showbody':
				if ($this->func->is_page($page)) {
					$body = $this->func->convert_html($this->func->get_source($page));
					$this->func->convert_finisher($body);
				} else {
					if ($page == '') {
						$page = '(Empty Page Name)';
					}
					$body = $this->func->htmlspecialchars($page);
					$body .= '<br>Unknown page name';
				}
				$this->func->pkwk_common_headers();
				header('Cache-control: no-cache');
				header('Pragma: no-cache');
				header('Content-Type: text/html; charset='.$this->cont['CONTENT_CHARSET']);
				print <<<EOD
<div>
$body
</div>
EOD;
				break;
			case 'static':
				$ret = $this->action_static();
				if ($ret) return array('msg' => 'Mobile Map', 'body' => $ret);
		}
		return array('exit' => 0);
	}

	function action_static() {
		////////////////////////////////////////////////////////////
		// This part is based on GNAVI (http://xoops.iko-ze.net/) //
		////////////////////////////////////////////////////////////

		if ($this->cont['UA_PROFILE'] === 'mobile') {
			$this->func->add_tag_head('gmap.css');
			$navi_tag = '';
		} else {
			$this->root->keitai_output_filter = 'SJIS';
			$this->root->rtf['no_accesskey'] = TRUE;
			$navi_tag = '<img src="'.$this->cont['LOADER_URL'].'?src=mnavi.gif" '.$this->conf['navsize'].' />';
		}

		$default_lat  = empty( $this->root->get['lat'] )  ? $this->cont['PLUGIN_GMAP_DEF_LAT']  : floatval( $this->root->get['lat'] ) ;
		$default_lng  = empty( $this->root->get['lng'] )  ? $this->cont['PLUGIN_GMAP_DEF_LNG']  : floatval( $this->root->get['lng'] ) ;
		$default_zoom = empty( $this->root->get['zoom'] ) ? $this->cont['PLUGIN_GMAP_DEF_ZOOM'] : intval( $this->root->get['zoom'] ) ;

		$markers = isset($this->root->get['markers'])? '&amp;markers=' . $this->func->htmlspecialchars($this->root->get['markers']) : '';
		$refer = isset($this->root->get['refer'])? $this->root->get['refer'] : '';
		$title = isset($this->root->get['t'])? $this->root->get['t'] : '';

		$back = '';
		if ($refer) {
			$refer = $this->func->htmlspecialchars(preg_replace('#^[a-zA-Z]+://[^/]+#', '', $refer));
			$back = '[ <a href="'.$this->root->siteinfo['host'].$refer.'">' . $this->root->_msg_back_word . '</a> ]';
			$refer = '&amp;refer=' . $refer;
		}

		$other = $markers . $refer;

		$mymap = $this->google_staticmap_url . "center=$default_lat,$default_lng&zoom=$default_zoom&size={$this->conf['StaticMapSize']}&maptype=mobile&key={$this->root->google_api_key}{$markers}";
		$google_link = $this->get_static_image_url($default_lat, $default_lng, $default_zoom, '', 2, $title);

		/*���٤� -90�� �� +90�٤��ϰϤˡ����٤� -180�� �� +180�٤��ϰϤ˼��ޤ�褦��*/

		$movex=$this->conf['StaticMapSizeW']/pow(2,$default_zoom);
		$movey=$this->conf['StaticMapSizeH']/pow(2,$default_zoom);

		//Amount of movement on Mini map . set bigger than 0 and 1 or less. (0 < value <=1).
		$mobile_mapmove_raito = 0.6;

		$latup = $this->latlnground($default_lat+$movey * $mobile_mapmove_raito);
		$latdown = $this->latlnground($default_lat-$movey * $mobile_mapmove_raito);
		$lngup = $this->latlnground($default_lng+$movex * $mobile_mapmove_raito);
		$lngdown = $this->latlnground($default_lng-$movex * $mobile_mapmove_raito);

		$latup =   $latup   > 90  ? $latup  -180 : ($latup   < -90  ? $latup   + 180 : $latup   );
		$latdown = $latdown > 90  ? $latdown-180 : ($latdown < -90  ? $latdown + 180 : $latdown );
		$lngup =   $lngup   > 180 ? $lngup  -360 : ($lngup   < -180 ? $lngup   + 360 : $lngup   );
		$lngdown = $lngdown > 180 ? $lngdown-360 : ($lngdown < -180 ? $lngdown + 360 : $lngdown );

		$mapkeys=array(
				'zoom' => $default_zoom,
				'zoomdown' => ($default_zoom-1 > 1 ? $default_zoom-1 : 1 ),
				'zoomup' => ($default_zoom+1 < 18 ? $default_zoom+1 : 18) ,
				'doublezoomdown' => ($default_zoom-3 > 1 ? $default_zoom-4 : 1 ),
				'doublezoomup' => ($default_zoom+3 < 18 ? $default_zoom+4 : 18) ,
				'lat' => $default_lat,
				'lng' => $default_lng,
				'latup' =>   $latup  ,
				'latdown' => $latdown  ,
				'lngup' =>   $lngup  ,
				'lngdown' => $lngdown  ,
			);

		$maplink = $this->root->script . '?plugin=gmap&amp;action=static&amp;';
		
		if ($this->cont['UA_PROFILE'] === 'mobile') {
			
		} 
		
		if ($default_zoom < 18) {
			$zoomup = <<<EOD
<a href="{$maplink}&amp;zoom={$mapkeys['zoomup']}&amp;lng={$mapkeys['lng']}&amp;lat={$mapkeys['lat']}{$other}"  accesskey="5" ></a>
<a href="{$maplink}&amp;zoom={$mapkeys['doublezoomup']}&amp;lng={$mapkeys['lng']}&amp;lat={$mapkeys['lat']}{$other}"  accesskey="*" ></a>
EOD;
		} else {
			$zoomup = '';
		}

		if ($default_zoom > 1) {
			$zoomdown = <<<EOD
<a href="{$maplink}&amp;zoom={$mapkeys['zoomdown']}&amp;lng={$mapkeys['lng']}&amp;lat={$mapkeys['lat']}{$other}"  accesskey="0" ></a>
<a href="{$maplink}&amp;zoom={$mapkeys['doublezoomdown']}&amp;lng={$mapkeys['lng']}&amp;lat={$mapkeys['lat']}{$other}"  accesskey="#" ></a>
EOD;
		} else {
			$zoomdown = '';
		}
		
		$title = $title? '<h3>' . $this->func->htmlspecialchars($title) . '</h3>' : '';
		$ret = <<<EOD
<a href="{$maplink}&amp;zoom={$mapkeys['zoom']}&amp;lng={$mapkeys['lngdown']}&amp;lat={$mapkeys['latup']}{$other}"  accesskey="1" ></a>
<a href="{$maplink}&amp;zoom={$mapkeys['zoom']}&amp;lng={$mapkeys['lng']}&amp;lat={$mapkeys['latup']}{$other}"  accesskey="2" ></a>
<a href="{$maplink}&amp;zoom={$mapkeys['zoom']}&amp;lng={$mapkeys['lngup']}&amp;lat={$mapkeys['latup']}{$other}"  accesskey="3" ></a>
<a href="{$maplink}&amp;zoom={$mapkeys['zoom']}&amp;lng={$mapkeys['lngdown']}&amp;lat={$mapkeys['lat']}{$other}"  accesskey="4" ></a>
<a href="{$maplink}&amp;zoom={$mapkeys['zoom']}&amp;lng={$mapkeys['lngup']}&amp;lat={$mapkeys['lat']}{$other}"  accesskey="6" ></a>
<a href="{$maplink}&amp;zoom={$mapkeys['zoom']}&amp;lng={$mapkeys['lngdown']}&amp;lat={$mapkeys['latdown']}{$other}"  accesskey="7" ></a>
<a href="{$maplink}&amp;zoom={$mapkeys['zoom']}&amp;lng={$mapkeys['lng']}&amp;lat={$mapkeys['latdown']}{$other}"  accesskey="8" ></a>
<a href="{$maplink}&amp;zoom={$mapkeys['zoom']}&amp;lng={$mapkeys['lngup']}&amp;lat={$mapkeys['latdown']}{$other}"  accesskey="9" ></a>
{$zoomup}
{$zoomdown}
{$title}
<div style="text-align:center">
	<div class="gmap_smap"><a class="link2googlemap" href="{$google_link}"><img src="{$mymap}" {$this->conf['mapsize']} /></a></div>
	{$navi_tag}
	<br />
	<a href="{$google_link}">GoogleMap</a>
	<hr />
	{$back}
</div>

EOD;
		return $ret;
	}


	function latlnground($value){
		////////////////////////////////////////////////////////////
		// This part is based on GNAVI (http://xoops.iko-ze.net/) //
		////////////////////////////////////////////////////////////
		return round(floatval($value)*1000000)/1000000 ;
	}

	function plugin_gmap_getbool($val) {
		if ($val == false) return 0;
		if (!strcasecmp ($val, "false") ||
			!strcasecmp ($val, "none") ||
			!strcasecmp ($val, "no"))
			return 0;
		return 1;
	}
	
	function gmap_getpos($val) {
		if (! is_numeric($val)) {
			$val = strtoupper($val);
			if (in_array($val, array('TL','TC','TR','LT','RT','LC','RC','LB','RB','BL','BC','BR'))) {
				return $val;
			}
		}
		return $this->plugin_gmap_getbool($val);
	}
	
	// 	+----------------+
	// 	+ TL    TC    TR +
	// 	+ LT          RT +
	// 	+                +
	// 	+ LC          RC +
	// 	+                +
	// 	+ LB          RB +
	// 	+ BL    BC    BR +
	// 	+----------------+
	function gmap_get_pos_constant($val, $default = '') {
		static $pos = array(
				'T' => 'TOP',
				'B' => 'BOTTOM',
				'L' => 'LEFT',
				'C' => 'CENTER',
				'R' => 'RIGHT');
		
		if (!$default) {
			$default = 'RB';
		}
		if (is_numeric($val) || strlen($val) < 2 || !isset($pos[$val[0]]) || !isset($pos[$val[0]])) {
			$val = $default;
		}
		return 'google.maps.ControlPosition.'.$pos[$val[0]].'_'.$pos[$val[1]];
	}
	
	function plugin_gmap_addprefix($page, $name) {
		$page = $this->get_pgid($page);
		//if (!$page) $page = uniqid('r_');
		if ($name === $this->cont['PLUGIN_GMAP_DEF_MAPNAME']) {
			if (!isset($this->map_count[$page])) {
				$this->map_count[$page] = 0;
			}
			$this->map_count[$page]++;
			$name .= strval($this->map_count[$page]);
		}
		$this->lastmap_name = 'pukiwikigmap_'.$page.'_'.$name;
		return $this->lastmap_name;
	}

	function get_static_image_url($lat, $lng, $zoom, $markers = '', $useAction = 0, $title = '') {
		if ($useAction === 2) {
			if ($this->cont['UA_PROFILE'] === 'mobile') {
				if ($title) {
					$title = rawurlencode(mb_convert_encoding(' ('.$title.')', 'UTF-8', $this->cont['SOURCE_ENCODING']));
				}
				$url = 'https://maps.google.com/maps?q=loc:'.$lat.','.$lng.$title.'&z='.$zoom.'&iwloc=A';
			} else {
				$url = 'https://www.google.co.jp/m/local?site=local&ll='.$lat.','.$lng.'&z='.$zoom;
			}
		} else if ($useAction) {
			$url = $this->root->script . '?plugin=gmap&amp;action=static&amp;lat='.$lat.'&amp;lng='.$lng.'&amp;zoom='.$zoom.'&amp;refer='.rawurlencode(@ $_SERVER['REQUEST_URI']);
			if ($title) {
				$url .= '&amp;t='.rawurlencode($title);
			}
		} else {
			if ($this->cont['UA_PROFILE'] === 'keitai' && $zoom > 10) {
				$zoom = $zoom - 1;
			}
			$params = ($lng)? 'center='.$lat.','.$lng.'&amp;zoom='.$zoom.'&amp;' : $lat;
			$url = $this->google_staticmap_url.$params.'size='.$this->conf['StaticMapSize'].'&amp;type=mobile&amp;key='.$this->root->google_api_key;
		}
		if ($markers && $useAction < 2) {
			$url .= '&amp;markers=' . $this->func->htmlspecialchars($markers);
		}
		return $url;
	}

	function make_static_maps($lat, $lng, $zoom) {
		$markers = '__GOOGLE_MAPS_STATIC_MARKERS_' . $this->lastmap_name;
		$this->root->replaces_finish[$markers] = '';
		$params = '__GOOGLE_MAPS_STATIC_PARAMS_' . $this->lastmap_name;
		$_zoom = ($zoom > 10)? ($zoom - 1) : $zoom;
		$this->root->replaces_finish[$params] = 'center='.$lat.','.$lng.'&amp;zoom='.$_zoom.'&amp;';
		$imgurl = $this->get_static_image_url($params, '', 0, $markers);
		$img = '<div class="gmap_smap"><img class="img_margin" src="'.$imgurl.'" '.$this->conf['mapsize'].' /></div>';
		$map = '<br />[ <a href="'.$this->get_static_image_url($lat, $lng, $zoom, '__GOOGLE_MAPS_STATIC_MARKERS_' . $this->lastmap_name, 1).'">Map</a> | <a href="'.$this->get_static_image_url($lat, $lng, $zoom, '__GOOGLE_MAPS_STATIC_MARKERS_' . $this->lastmap_name, 2).'">Google</a> ]';
		return '<div style="text-align:center;">' . $img . $map . '</div>';
	}

	function plugin_gmap_output($doInit, $params, $display) {
		$this->root->rtf['disable_render_cache'] = true;

		$this->root->pagecache_profiles = $this->cont['PLUGIN_GMAP_PROFILE'];

		$defoptions = $this->plugin_gmap_get_default();

		$inoptions = array();
		$isSetZoom = false;
		$align = '';
		$around = false;
		foreach ($params as $param) {
			$pos = strpos($param, '=');
			if ($pos === false) {
				$param = strtolower(trim($param));
				if (in_array($param, array('left', 'right', 'center'))) {
					$align = $param;
				} else {
					if ($param === 'around') {
						$around = true;
					}
				}
				continue;
			}
			$index = trim(substr($param, 0, $pos));
			$value = $this->func->htmlspecialchars(trim(substr($param, $pos+1)), ENT_QUOTES);
			$inoptions[$index] = $value;
			if ($index == 'cx') {$cx = (float)$value;}//for old api
			if ($index == 'cy') {$cy = (float)$value;}//for old api
			if ($index == 'zoom') {$isSetZoom = true;}//for old api
		}

		if (array_key_exists('define', $inoptions)) {
			$this->root->vars['gmap'][$inoptions['define']] = $inoptions;
			return "";
		}

		$this->func->add_tag_head('gmap.css');

		$coptions = array();
		if (array_key_exists('class', $inoptions)) {
			$class = $inoptions['class'];
			if (array_key_exists($class, $this->root->vars['gmap'])) {
				$coptions = $this->root->vars['gmap'][$class];
			}
		}
		$options = array_merge($defoptions, $coptions, $inoptions);
		$mapname		= $this->plugin_gmap_addprefix($this->root->vars['page'], $options['mapname']);
		$key			= $options['key'];
		$width			= $options['width'];
		$height			= $options['height'];
		$lat			= (float)$options['lat'];
		$lng			= (float)$options['lng'];
		$zoom			= (integer)$options['zoom'];
		$type			= $options['type'];
		$mapctrl		= isset($options['mapctrl'])? $options['mapctrl'] : false; //����
		$panctrl		= $options['panctrl'];
		$zoomctrl		= $options['zoomctrl'];
		$typectrl		= $options['typectrl'];
		$scalectrl		= $this->gmap_getpos($options['scalectrl']);
		$rotatectrl     = $this->plugin_gmap_getbool($options['rotatectrl']);
		$streetviewctrl = $this->plugin_gmap_getbool($options['streetviewctrl']);
		$overviewctrl	= $this->plugin_gmap_getbool($options['overviewctrl']);
		$crossctrl		= $this->plugin_gmap_getbool($options['crossctrl']);
		$searchctrl		= $this->gmap_getpos($options['searchctrl']);
		$dropmarker		= $this->plugin_gmap_getbool($options['dropmarker']);
		$togglemarker	= $this->plugin_gmap_getbool($options['togglemarker']);
		$googlebar		= 0; // $this->gmap_getpos($options['googlebar']); // ���ݡ��Ƚ�λ
		//$overviewwidth	= $options['overviewwidth']; // �ѻ�
		//$overviewheight = $options['overviewheight']; // �ѻ�
		$api			= isset($options['api'])? (integer)$options['api'] : 2; //����
		$noiconname		= $options['noiconname'];
		$dbclickzoom	= $this->plugin_gmap_getbool($options['dbclickzoom']);
		$scrollwheel    = $this->plugin_gmap_getbool($options['scrollwheel']);
		$kml			= preg_replace("/&amp;/i", '&', ($options['kml']? $options['kml'] : (!empty($options['geoxml'])? $options['geoxml'] : '')));
		$importicon		= $options['importicon'];
		$backlinkmarker = $this->plugin_gmap_getbool($options['backlinkmarker']);
		$wikitag        = $options['wikitag'];
		$autozoom       = $this->plugin_gmap_getbool($options['autozoom']);
		$page = $this->get_pgid($this->root->vars['page']);
		//api�Υ����å�
		if ( ! (is_numeric($api) && $api >= 0 && $api <= 2) ) {
			 $api = 2;
		}
		$this->root->vars['gmap_info'][$mapname]['api'] = $api;
		//�Ť�1��API�Ȥθߴ����Τ���cx, cy���Ϥ��줿���lng, lat���������롣
		if ($api < 2) {
			if (isset($cx) && isset($cy)) {
				$lat = $cx;
				$lng = $cy;
			} else {
				$tmp = $lng;
				$lng = $lat;
				$lat = $tmp;
			}
		} else {
			if (isset($cx)) $lng = $cx;
			if (isset($cy)) $lat = $cy;
		}

		// zoom��٥�
		if ($api < 2 && $isSetZoom) {
			$zoom = 17 - $zoom;
		}

		if (!$this->plugin_gmap_is_supported_profile()) {
			//return "gmap:unsupported device";
			return $this->make_static_maps($lat, $lng, $zoom);
		}

		// width, height���ͥ����å�
		if (is_numeric($width)) { $width = (int)$width . "px"; }
		if (is_numeric($height)) { $height = (int)$height . "px"; }

		// Map�����פ�̾����������
		$type = $this->plugin_gmap_get_maptype($type);

		// ����������ν���
		if ($doInit) {
			$output = $this->plugin_gmap_init_output($key);
		} else {
			$output = "";
		}
		$pukiwikiname = $options['mapname'];
		$output .= <<<EOD
<div id="$mapname" class="gmap_map" style="width: $width; height: $height;"></div>
EOD;
		if ($wikitag !== 'none') {
			if ($wikitag === 'show') {
				$_display = '';
				$_icon = '-';
			} else {
				$_display = 'display:none;';
				$_icon = '+';
			}
			$output .= <<<EOD

<div class="gmap_tag_base" style="width: $width;">
<span id="{$mapname}_handle" class="gmap_handle" onclick="this.innerHTML = (this.innerHTML == '+')? '-' : '+';$('{$mapname}_info').toggle();">{$_icon}</span>
 {$this->msg['wikitag_thismap']}
<div id="{$mapname}_info" class="gmap_tag_info" style="width: $width;{$_display}">&nbsp;</div>
</div>
EOD;
		}

		// Make map options
		$mOptions = array();
		
		// Basic
		$mOptions[] = "center: {lat: $lat, lng: $lng}";
		$mOptions[] = "zoom: $zoom";
		$mOptions[] = "mapTypeId: google.maps.MapTypeId.$type";
		
		// Show Map Control/Zoom
		if ($mapctrl !== false) {
			switch($mapctrl) {
				case "small":
					$panctrl = 'normal';
					$zoomctrl = 'small';
					break;
				case "smallzoom":
					$panctrl = 'none';
					$zoomctrl = 'small';
					break;
				case "none":
					$panctrl = 'none';
					$zoomctrl = 'none';
					break;
				case "large":
					$panctrl = 'normal';
					$zoomctrl = 'large';
					break;
				default:
					break;
			}
		}
		
		// panControl
		if ($panctrl == 'none') {
			$mOptions[] = "panControl: false";
		}
		
		// zoomControl
		switch($zoom) {
			case 'small':
				$mOptions[] = "zoomControl: true";
				$mOptions[] = "zoomControlOptions: {style: google.maps.ZoomControlStyle.SMALL}";
				break;
			case 'large':
				$mOptions[] = "zoomControl: true";
				$mOptions[] = "zoomControlOptions: {style: google.maps.ZoomControlStyle.LARGE}";
				break;
			case 'none':
				$mOptions[] = "zoomControl: false";
			default:
				break;
		}
		
		// Scale
		//if ($scalectrl != "none") {
		if($scalectrl) {
			$mOptions[] = "scaleControl: true";
			$_def = '';
			if ($scalectrl === 1 && ($googlebar === 1 || $googlebar === 'BL')) {
				$_def = 'RB';
			}
			$_pos = $this->gmap_get_pos_constant($scalectrl, $_def);
			$mOptions[] = "scaleControlOptions: {position: {$_pos}}";
		}
		
		// Show Map Type Control and Center
		if ($typectrl == 'none') {
			$mOptions[] = "mapTypeControl: false";
		} else {
			$mOptions[] = "mapTypeControl: true";
			switch($typectrl[0]) {
				//horizontal
				case "h":
					$mOptions[] = "mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR }";
					break;
					//dropdown
				case "d":
					$mOptions[] = "mapTypeControlOptions: { style: google.maps.MapTypeControlStyle.DROPDOWN_MENU }";
					break;
					//normal(default)
				case "n":
				default:
					break;
			}
		}
		
		// rotateControl
		if ($rotatectrl) {
			$mOptions[] = "rotateControl: true";
		}
		
		// streetViewControl
		if (!$streetviewctrl) {
			$mOptions[] = "streetViewControl: false";
		}
		
		// OverviewMap
		if ($overviewctrl != "none") {
			// V3 �Ǥϥ��������꤬�Ǥ��ʤ�
			//$ovw = preg_replace("/(\d+).*"."/i", "\$1", $overviewwidth);
			//$ovh = preg_replace("/(\d+).*"."/i", "\$1", $overviewheight);
			//if ($ovw == "") $ovw = $this->cont['PLUGIN_GMAP_DEF_OVERVIEWWIDTH'];
			//if ($ovh == "") $ovh = $this->cont['PLUGIN_GMAP_DEF_OVERVIEWHEIGHT'];
				
			$mOptions[] = "overviewMapControl: true";
			$mOptions[] = "overviewMapControlOptions: {opened: ".(($overviewctrl == "hide")? 'false' : 'true')."}";
		}
		
		// Double click zoom
		if (! $dbclickzoom) {
			$mOptions[] = "disableDoubleClickZoom: true";
		}
		
		// scrollwheel zooming
		if (! $scrollwheel) {
			$mOptions[] = "scrollwheel: false";
		}
		
		// set map options
		$mOptions = "{" . join(",", $mOptions) . "}";
		
		// �ޥå׺���
		$output .= <<<EOD

<script type="text/javascript">
//<![CDATA[
onloadfunc.push( function () {
	var map = new PGMap('$page', '$mapname', $mOptions);
EOD;

		// Auto Zoom
		if ($autozoom) {
			$output .= <<<EOD

	onloadfunc3.push( function () {
		p_gmap_auto_zoom("$page", "$mapname");
	});
EOD;
		}

		// Drop Marker
		if ($dropmarker) {
			$output .= "googlemaps_dropmarker['$page']['$mapname'] = new PGDropMarker(map, {title:'{$this->msg['drop_marker']}'})\n";
		}
		
		// ���󥿡���������ȥ���
		if ($crossctrl) {
			$output .= "googlemaps_crossctrl['$page']['$mapname'] = new PGCross(map);\n";
		}
		
		// �����ܥå�������ȥ���
		if ($searchctrl) {
			$_pos = $this->gmap_get_pos_constant($searchctrl, 'TC');
			$_opt = "{position: $_pos}";
			$output .= "var searchctrl = new PGSearch();\n";
			$output .= "searchctrl.initialize(map, $_opt);\n";
			$output .= "googlemaps_searchctrl['$page']['$mapname'] = searchctrl;\n";
		}
		
		// KML (Geo XML)
		if ($kml != "") {
			$ismatch = preg_match("/^https?:\/\/.*/", $kml, $matches);
			if (!$ismatch) {
				$ref =& $this->func->get_plugin_instance('ref');
				$pagename = $this->func->get_name_by_pgid($page);
				if (strpos($kml, '/') !== false) {
					$_page = preg_replace('#/[^/]+$#', '', $kml);
					$kml = basename($kml);
					$pagename = $this->func->get_fullname($_page, $pagename);
				}
				$kml = $ref->get_ref_url($pagename, $kml, false, true);
			} else {
				$kml = $this->func->htmlspecialchars($kml, ENT_QUOTES, $this->cont['SOURCE_ENCODING']);
			}
			$kml = str_replace('&amp;', '&', $kml);
			$output .= "var kmllayer = new google.maps.KmlLayer(\"$kml\");\n";
			$output .= "kmllayer.setMap(map);\n";
		}

		// GoogleBar
		if ($googlebar) {
			$this->func->add_js_head('https://www.google.com/uds/api?file=uds.js&amp;v=1');
			$this->func->add_tag_head('jGoogleBarV3.css');
			$this->func->add_tag_head('jGoogleBarV3.js');
			$output .= "var gbarOptions={searchFormOptions:{hintString:'{$this->msg['do_local_search']}',buttonText:'{$this->root->_LANG['skin']['search_s']}'}};\n";
			$output .= "var gbar=new window.jeremy.jGoogleBar(map,gbarOptions);\n";
			$_pos = $this->gmap_get_pos_constant($googlebar, 'BL');
			$output .= "map.controls[$_pos].push(gbar.container);\n";
		}

		// �ޡ�������ɽ����ɽ�������å��ܥå���
		if ($togglemarker) {
			$output .= "onloadfunc2.push( function(){p_gmap_togglemarker_checkbox('$page', '$mapname', '$noiconname', '{$this->msg['default_icon_caption']}');} );";
		}

		// Map tag
		if ($wikitag !== 'none') {
			$keys = $defoptions;
			unset($keys['api'], $keys['key']);
			$keys['page'] = '';
			foreach(array_keys($keys) as $key) {
				$_options[] = $key . ':"' . str_replace('"', '\\"', (string)$$key) . '"';
			}
			$_options = join(',', $_options);
			$output .= <<<EOD
	var wikiOptions = {{$_options}};
	google.maps.event.addListener(googlemaps_maps['$page']['$mapname'], 'bounds_changed', function(){PGTool.setWikiTag(wikiOptions);});
	google.maps.event.addListener(googlemaps_maps['$page']['$mapname'], 'maptypeid_changed', function(){PGTool.setWikiTag(wikiOptions);});
	onloadfunc2.push(function () {
		if (googlemaps_dropmarker['$page']['$mapname']) {
			google.maps.event.addListener(googlemaps_dropmarker['$page']['$mapname'], 'position_changed', function(){PGTool.setWikiTag(wikiOptions);});
		}
	});
EOD;
		}

		// close script
		$output .= <<<EOD

}); // close onloadfunc
//]]>
</script>
EOD;

		// ���ꤵ�줿Pukiwiki�ڡ������饢��������������
		if ($importicon != "") {
			$lines = $this->func->get_source($this->func->get_fullname($importicon, $this->root->vars['page']));
			foreach ($lines as $line) {
				$ismatch = preg_match('/gmap_icon\(([^()]+?)\)/i', $line, $matches);
				if ($ismatch) {
					//$output .= $this->func->convert_html("#" . $matches[0]) . "\n";
					$output .= $this->func->do_plugin_convert('gmap_icon', $matches[1] . ',basepage=' . $importicon);
				}
			}
		}

		// ���Υڡ����ΥХå���󥯤���ޡ�������������롣
		if ($backlinkmarker) {
			$links = $this->func->links_get_related_db($this->root->vars['page']);
			if (! empty($links)) {
				$output .= "<ul>\n";
				foreach(array_keys($links) as $page) {
					$ismatch = preg_match('/#gmap_mark\(([^, \)]+), *([^, \)]+)(.*?)\)/i',
					$this->func->get_source($page, TRUE, TRUE), $matches);
					if ($ismatch) {
						$markersource = "&gmap_mark(" .
						$matches[1] . "," . $matches[2] .
						", title=" . $page;
						if ($matches[3] != "") {
							preg_match('/caption=[^,]+/', $matches[3], $m_caption);
							if ($m_caption) $markersource .= "," . $m_caption[0];
							preg_match('/icon=[^,]+/', $matches[3], $m_icon);
							if ($m_icon) $markersource .= "," . $m_icon[0];
						}
						$markersource .= ");";
						$output .= "<li>" . $this->func->make_link($markersource) . "</li>\n";
					}
				}
				$output .= "</ul>\n";
			}
		}

		// �ޡ������ǥե���ȥ������������
		$output .= $this->func->do_plugin_convert('gmap_icon');
		
		$class = 'gmap';
		if ($align && $display === 'block') {
			if ($around && $align !== 'center') {
				$class .= ' float_' . $align;
			} else {
				$class .= ' block_' . $align;
			}
		}
		$class = ' class="'.$class.' margin_'.(($display === 'block')? '10' : '0').'"';
		$output = '<div style="display: '.$display.'; width: '.$width.';"'.$class.'>'. $output . '</div>';

		return $output;
	}

	function plugin_gmap_get_maptype($type) {
		switch (strtolower($type[0])) {
			case "s": $type = 'SATELLITE'; break;
			case "h": $type = 'HYBRID'   ; break;
			case "t":
			case "p": $type = 'TERRAIN'   ; break;
			case "r":
			case "n":
			default:  $type = 'ROADMAP'   ; break;
		}
		return $type;
	}

	function plugin_gmap_init_output($key) {
		if (floatval($this->conf['ApiVersion']) < 3) {
			$this->conf['ApiVersion'] = '3';
		}
		$this->func->add_js_head('https://maps.googleapis.com/maps/api/js?v='.$this->conf['ApiVersion'].'&amp;libraries=places&amp;key='.$key, true, 'UTF-8');
		$this->func->add_tag_head('gmap.js');
		return;
	}

	function get_pgid ($page) {
		$pgid = $this->func->get_pgid_by_name($page);
		if (!$pgid) $pgid = '0';
		return strval($pgid);
	}
}
?>