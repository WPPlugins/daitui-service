<?php if (realpath(@$_SERVER['SCRIPT_FILENAME']) == realpath(__FILE__)) exit("Access Denied"); ?>

<style type="text/css">
.daitui input.text, .daitui input.password, .daitui textarea {
    border-color: #999999 #CCCCCC #DDDDDD #CCCCCC;
    border-style: solid;
    border-width: 1px;
    padding: 3px 5px 2px;
}
#get_img_list {
    border-collapse: separate;
}
#get_img_list td.button_image {
    border-color: transparent;
    border-style: solid;
    border-width: 5px 10px;
    cursor: pointer;
    padding: 5px 10px;
    _border-style: none;
    _padding: 10px 20px;
}
#get_img_list td.button_image.hover {
    border-color: #EEEEEE;
    _border-style: solid;
    _padding: 5px 10px;
}
#get_img_list td.button_image.select {
    border-color: #CCCCCC;
    _border-style: solid;
    _padding: 5px 10px;
}
#button_preview {
    font-size: 12px;
}
</style>

<div class="wrap daitui">
<h2>代推，收藏与分享书签按钮服务</h2>

<form method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>
    <input type="hidden" name="action" value="update" />
    <input type="hidden" name="page_options" value="daitui_button_style,daitui_button_text,daitui_places"/>

    <input type="hidden" id="daitui_button_style" name="daitui_button_style" value="<?php echo $daitui_button_style; ?>"/>
    <input type="hidden" id="daitui_button_text" name="daitui_button_text" value="<?php echo $daitui_button_text; ?>"/>

    <table width="100%"><tr><td width="60%">
        <h3>请选择按钮显示形式</h3>
    </td><td width="40%">
        <h3>预览</h3>
    </td></tr><tr><td>
        <table id="get_img_list">
            <tr>
                <td class="button_image" _index="style_01"></td>
                <td class="button_image" _index="style_11"></td>
            </tr>
            <tr>
                <td class="button_image" _index="style_02"></td>
                <td class="button_image" _index="style_12"></td>

            </tr>
            <tr>
                <td class="button_image" _index="style_03"></td>
                <td class="button_image" _index="style_13"></td>
            </tr>
            <tr>
                <td class="button_image" _index="style_21"></td>
                <td class="button_image" _index="style_31"></td>
            </tr>
        </table>
    </td><td id="button_preview">
    </td></tr></table>
    <br />
    <br />
    <h3>选择显示位置</h3>
    <table class="form-table">
        <tr>
            <th scope="row">在首页显示</th>
            <td><input type="checkbox" name="daitui_places[]" value="home" <?php daitui_checked_place("home", true); ?> /></td>
        </tr>
        <tr>
            <th scope="row">在页面（page）中显示</th>
            <td><input type="checkbox" name="daitui_places[]" value="page" <?php daitui_checked_place("page", true); ?> /></td>
        </tr>
        <tr>
            <th scope="row">在分类页显示</th>
            <td><input type="checkbox" name="daitui_places[]" value="category" <?php daitui_checked_place("category", true); ?> /></td>
        </tr>
        <tr>
            <th scope="row">在存档页显示</th>
            <td><input type="checkbox" name="daitui_places[]" value="archive" <?php daitui_checked_place("archive", true); ?> /></td>
        </tr>
        <tr>
            <th scope="row">在feed页显示</th>
            <td><input type="checkbox" name="daitui_places[]" value="feed" <?php daitui_checked_place("feed", true); ?> /> <em>部分阅读器无法显示</em></td>
        </tr>
        <tr>
            <th scope="row">在搜索页显示</th>
            <td><input type="checkbox" name="daitui_places[]" value="search" <?php daitui_checked_place("search", true); ?> /></td>
        </tr>
    </table>

    <p class="submit">
        <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
    </p>
</form>

</div>
<script type="text/javascript" src="http://src.daitui.com/dt.js" charset="utf-8"></script>
<script type="text/javascript">
(function ($, $$) {
$.ready(function () {
    $.fixIeBgBug();
    $.loadCssFile($$.STYLESHEET_URL);

    var btn_codes = {<?php echo $btn_codes; ?>};
    var get_img_list = $.byId("get_img_list");
    var button_images = $.byClass("button_image", get_img_list);
    var button_preview = $.byId("button_preview");
    var daitui_button_style = $.byId("daitui_button_style");
    var daitui_button_text = $.byId("daitui_button_text");
    var update_code_sub = function (obj) {
        get_img_list._last_select && $.removeClass(get_img_list._last_select, "select");
        $.addClass(obj, "select");
        get_img_list._last_select = obj;

        daitui_button_style.value = $.attr(obj, "_index");

        var s = btn_codes[$.attr(obj, "_index")];
        if (/id="button_text_\d+"/i.test(s)) {
            var a = /id="(button_text_\d+)"/i.exec(s);
            s += $.byId(a[1]).value;
            s = s.replace(/<input[^<>]*\/>/i, "");

            daitui_button_text.value = $.byId(a[1]).value;
        }

        s = '<a class="dt_button" href="http://www.daitui.com/bookmark">' + /*(/<img/i.test(s) ? ("\n" + "&nbsp;&nbsp;&nbsp;&nbsp;" + s + "\n") : */s/*)*/ +  '</a>';
        s = s.replace(/&nbsp;/g, "\ "); /* space missed in IE, use "\ " instead */

        button_preview.innerHTML = s;
        $$.initWidget($.byClass("dt_button", button_preview)[0]);
    };

    for (var i = 0; i < button_images.length; i++) {
        button_images[i].innerHTML = btn_codes[$.attr(button_images[i], "_index")];

        if ($.attr(button_images[i], "_index") == "<?php echo $daitui_button_style; ?>") {
            update_code_sub(button_images[i]);
        }
    }

    $.bind(button_images, "click", function () {
        update_code_sub(this);
    });
    $.bind($.byName("button_text"), "keyup", function () {
        update_code_sub(this.parentNode);
    });
    $.bind(button_images, "mouseover", function () {
        $.addClass(this, "hover");
    });
    $.bind(button_images, "mouseout", function () {
        $.removeClass(this, "hover");
    });
});
})(window.dt_$, window.daitui);
</script>