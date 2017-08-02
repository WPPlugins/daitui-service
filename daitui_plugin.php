<?php if (realpath(@$_SERVER['SCRIPT_FILENAME']) == realpath(__FILE__)) exit("Access Denied"); ?>
<?php
/**
* Plugin Name: Daitui
* Plugin URI: http://www.daitui.com
* Description: 通过代推按钮帮助您的访客收藏/分享/订阅内容，从而给您的网站带来更多的流量。{{<a href="options-general.php?page=daitui-service/daitui_plugin.php">设置</a>}}
* Version: 0.1.1
*
* Author: gjb0518
* Author URI: http://www.daitui.com
*/

define("DAITUI_ROOT", dirname(__FILE__));
daitui_init();




function daitui_init() {
    global $daitui_places, $daitui_button, $daitui_button_styles_list, $daitui_button_style, $daitui_button_text;

    add_option("daitui_places", array("home", "page", "category", "archive"));
    add_option("daitui_button_style", "style_01");
    add_option("daitui_button_text", "分享");

    $daitui_places = get_option("daitui_places");
    $daitui_button_style = get_option("daitui_button_style");
    $daitui_button_text = get_option("daitui_button_text");

    $daitui_button_styles_list = array(
        "style_01" => '<img src="http://src.daitui.com/widget/01.gif" align="absmiddle" alt="收藏与分享" border="0" height="16" width="120" />',
        "style_02" => '<img src="http://src.daitui.com/widget/02.gif" align="absmiddle" alt="收藏与分享" border="0" height="16" width="120" />',
        "style_03" => '<img src="http://src.daitui.com/widget/03.gif" align="absmiddle" alt="收藏与分享" border="0" height="16" width="120" />',
        "style_11" => '<img src="http://src.daitui.com/widget/11.gif" align="absmiddle" alt="收藏与分享" border="0" height="16" width="55" />',
        "style_12" => '<img src="http://src.daitui.com/widget/12.gif" align="absmiddle" alt="收藏与分享" border="0" height="16" width="55" />',
        "style_13" => '<img src="http://src.daitui.com/widget/13.gif" align="absmiddle" alt="收藏与分享" border="0" height="16" width="55" />',
        "style_21" => '<img src="http://src.daitui.com/widget/21.gif" align="absmiddle" alt="收藏与分享" border="0" height="16" width="16" />&nbsp;<input class="text" id="button_text_21" name="button_text" type="text" value="' . addslashes($daitui_button_text) . '" style="width: 87px;" />',
        "style_31" => '<input class="text" id="button_text_31" name="button_text" type="text" value="' . addslashes($daitui_button_text) . '" style="width: 87px;" />'
    );

    $daitui_button = preg_replace("/<input[^<>]+>/i", $daitui_button_text, $daitui_button_styles_list[$daitui_button_style]);

    add_action('admin_menu', 'daitui_init_admin_menu');
    add_action('wp_footer', 'daitui_init_wp_footer', 99);
    add_filter('the_content', 'daitui_init_the_content', 99);
}

function daitui_init_wp_footer() {
    echo '<script type="text/javascript" src="http://src.daitui.com/dt.js" charset="utf-8"></script><script type="text/javascript">daitui.init();</script>';
}

function daitui_init_the_content($content) {
    global $daitui_button;

    if ((is_home() && !daitui_checked_place("home"))
            || (is_page() && !daitui_checked_place("page"))
            || (is_category() && !daitui_checked_place("category"))
            || (is_archive() && !daitui_checked_place("archive"))
            || (is_feed() && !daitui_checked_place("feed"))
            || (is_search() && !daitui_checked_place("search"))) {
        return $content;
    }

    $args = array(
        'title' => get_the_title(),
        'link' => get_permalink(),
        'tag' => daitui_get_tags(),
        'note' => daitui_get_excerpt()
    );

    $content .= "\n" . '<a class="dt_button" href="http://www.daitui.com/bookmark?' . daitui_join_args($args) . '">' . $daitui_button . '</a>';
    return $content;
}

function daitui_init_admin_menu() {
    add_options_page('代推，收藏与分享书签按钮服务', '代推', 10, __FILE__, 'daitui_admin');
}

function daitui_admin() {
    global $daitui_button_styles_list, $daitui_button_style;

    $btn_codes = "";
    foreach ($daitui_button_styles_list as $key => $value) {
        $btn_codes && $btn_codes .= ",";
        $btn_codes .= "\"$key\": \"" . addslashes($value) . "\"";
    }

	$footer = get_template_directory() . '/footer.php';
	if (!preg_match("/(^|[^\w])wp_footer([^\w]|$)/", @file_get_contents($footer))) {
	    echo "<div id=\"daitui-warning\" class=\"updated fade\"><p><strong>在模板{$footer}中没有找到对API钩子wp_footer的调用，可能导致分享服务下拉菜单无法显示。详见<a href=\"http://codex.wordpress.org/Theme_Development#Plugin_API_Hooks\">Plugin API Hooks</a></strong></p></div>";
	}

    require_once(DAITUI_ROOT . "/daitui_template_admin.php");
}

function daitui_checked_place($place, $echo = false) {
    global $daitui_places;

    $tof = is_array($daitui_places) && in_array($place, $daitui_places);
    if ($echo && $tof) echo "checked=\"checked\"";

    return $tof;
}




function daitui_get_tags() {
    $tags = get_the_tags();
    $arr = array();
    if (is_array($tags)) {
    	foreach ($tags as $tag) {
    		$arr[] = $tag->name;
    	}
    }
    return implode(",", $arr);
}

function daitui_get_excerpt() {
	return has_excerpt() ? get_the_excerpt() : "";
}

function daitui_join_args($args) {
    $arr = array();
    foreach ($args as $key => $value) {
        if (!$value) continue;
    	$arr[] = $key . "=" . rawurlencode($value);
    }
    return implode("&amp;", $arr);
}