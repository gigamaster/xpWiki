<?php
/**
 * gmap_street.inc.php - Google Maps API V3 StreetView Map
 * 
 * @author nao-pon  http://xoops.hypweb.net/
 * 
 * * gmap_street.inc.php
 * 
 *  #gmap_street(width=[��������], height=[��������], streetlayer=[0|1], heading=[����], pitch=[����], zoom=[����], markerzoom=[����])
 * 
 *  &gmap_street(width=[����], height=[����], streetlayer=[0|1], heading=[����], pitch=[����], zoom=[����], markerzoom=[����]);
 * 
 * gmap ��ɽ�������Ͽޤ˥�󥯤������ȥ꡼�ȥӥ塼��ɽ������ץ饰����
 * 
 * ��ˡ���󥯤����Ͽޤ� gmap �ץ饰����ǵ��Ҥ���ɬ�פ�����ޤ���(ľ���� gmap ���Ͽޤ˥�󥯤���ޤ���)
 * 
 * ** �ץ饰���󥪥ץ����(���٤ƾ�ά��ǽ) ���å�[]��Ͻ���͡�
 * :width|       ���� [400px]
 * :height|      ���� [400px]
 * :streetlayer| ����Ͽޤ˥��ȥ꡼�ȥӥ塼�оݥ쥤�䡼��ɽ������ [0]
 * :heading|     ���ȥ꡼�ȥӥ塼������ (0 - 360 or -180 - 180) [0]
 * :pitch|       ���ȥ꡼�ȥӥ塼�ζĳ� (-90 - 90) [0]
 * :zoom|        ���ȥ꡼�ȥӥ塼�Υ������� [1]
 * :markerzoom|  �ޡ���������å��Ǽ�ư�����ॢ�å׻��κ�����(0 - 21) 0:̵�� [18]
 *
 */

class xpwiki_plugin_gmap_street extends xpwiki_plugin {
	function plugin_gmap_street_init () {
		$this->cont['PLUGIN_GMAP_STREET_DEF_WIDTH'] =       '400px'; //����
		$this->cont['PLUGIN_GMAP_STREET_DEF_HEIGHT'] =      '400px'; //����
		$this->cont['PLUGIN_GMAP_STREET_DEF_STREETLAYER'] = 'none';  //���ȥ꡼�ȥӥ塼�оݥ쥤�䡼��ɽ������
		$this->cont['PLUGIN_GMAP_STREET_DEF_HEADING'] =     0;       //���ȥ꡼�ȥӥ塼������ (0 - 359)
		$this->cont['PLUGIN_GMAP_STREET_DEF_PITCH'] =       0;       //���ȥ꡼�ȥӥ塼�ζĳ� (-90 - 90)
		$this->cont['PLUGIN_GMAP_STREET_DEF_ZOOM'] =        1;       //���ȥ꡼�ȥӥ塼�Υ�������
		$this->cont['PLUGIN_GMAP_STREET_DEF_MARKERZOOM'] =  18;      //�ޡ���������å��Ǽ�ư�����ॢ�å׻��κ�����(0 - 21) 0:̵��
		$this->cont['PLUGIN_GMAP_STREET_DEF_LINKS']        = false;  //����Υ���ȥ���(true, false)
		$this->cont['PLUGIN_GMAP_STREET_DEF_IMAGEDATE']    = true;   //�����λ�������ɽ��
	}

	function get_default() {
		return array(
			'width'			 => $this->cont['PLUGIN_GMAP_STREET_DEF_WIDTH'],
			'height'		 => $this->cont['PLUGIN_GMAP_STREET_DEF_HEIGHT'],
			'streetlayer'	 => $this->cont['PLUGIN_GMAP_STREET_DEF_STREETLAYER'],
			'heading'		 => $this->cont['PLUGIN_GMAP_STREET_DEF_HEADING'],
			'pitch'			 => $this->cont['PLUGIN_GMAP_STREET_DEF_PITCH'],
			'zoom'			 => $this->cont['PLUGIN_GMAP_STREET_DEF_ZOOM'],
			'markerzoom'	 => $this->cont['PLUGIN_GMAP_STREET_DEF_MARKERZOOM'],
			'links'          => $this->cont['PLUGIN_GMAP_STREET_DEF_LINKS'],
			'imageDate'      => $this->cont['PLUGIN_GMAP_STREET_DEF_IMAGEDATE']
		);
	}

	function plugin_gmap_street_inline() {
		$args = func_get_args();
		return $this->get_body($args, 'inline-block');
	}
	
	function plugin_gmap_street_convert() {
		$args = func_get_args();
		return $this->get_body($args, 'block');
	}
	
	function get_body($params, $display) {

		$p_gmap =& $this->func->get_plugin_instance('gmap');

		if (!$p_gmap->plugin_gmap_is_supported_profile()) {
			return '';
		}

		if (! $mapname = $p_gmap->lastmap_name) {
			return "gmap_insertmarker: {$p_gmap->msg['err_need_gmap']}";
		}

		//���ץ����
		$defoptions = $this->get_default();
		$inoptions = array();
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
		}
		
		$options = array_merge($defoptions, $inoptions);

		$width = $options['width'];
		$height = $options['height'];
		$page = $p_gmap->get_pgid($this->root->vars['page']);
		$streetlayer = $p_gmap->plugin_gmap_getbool($options['streetlayer']);
		$heading = intval($options['heading']);
		$pitch = intval($options['pitch']);
		$zoom = intval($options['zoom']);
		$markerzoom = intval($options['markerzoom']);
		$links = $p_gmap->plugin_gmap_getbool($options['links'])? 'true' : 'false';
		$imageDate = $p_gmap->plugin_gmap_getbool($options['imageDate'])? 'true' : 'false';
		
		$optObj = array();
		
		$optObj[] = "streetlayer: $streetlayer";
		$optObj[] = "heading: $heading";
		$optObj[] = "pitch: $pitch";
		$optObj[] = "zoom: $zoom";
		$optObj[] = "markerzoom: $markerzoom";
		$optObj[] = "linksControl: $links";
		$optObj[] = "imageDateControl: $imageDate";

		$optObj = '{' . join(',' , $optObj) . '}';

		$output = <<<EOD

<div id="{$mapname}_street" class="gmap_streetview" style="width: $width; height: $height;"></div>
EOD;
		
		$output .= <<<EOD

<script type="text/javascript">
//<![CDATA[
onloadfunc.push(function(){
	PGStreet('$page', '$mapname', $optObj);
	var map = googlemaps_maps['$page']['$mapname'];
	if (!googlemaps_dropmarker['$page']['$mapname']) {
		map._onloadfunc.push(function(){PGTool.setCenterNearStreetViewPoint(map, null, false);});
	}
});
</script>
EOD;

		$class = 'gmap_street';
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
}
