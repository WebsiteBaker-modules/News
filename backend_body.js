
//console.info('News ==='+typeof News);
    // Function to toggle active/inavtive of a categorie in the overview
    function toggle_active_inactive(id) {
        var img = $("#i" + id);
    console.log(img);
        if( img.attr("src") == News.ThemeUrl+"/img/24/active_1.png") {
            var action = "disable";
            var src = News.ThemeUrl+"/img/24/active_0.png";
        } else {
            var action = "enable";
            var src = News.ThemeUrl+"/img/24/active_1.png";
        }
        $.ajax({
            url: News.AddonUrl+"/ajax/post_switch_active_inactive.php",
            type: "POST",
            data: 'cat_id='+id+'&action='+action,
            dataType: 'json',
            success: function(data) {
                if(data.success == "true") {
                    img.attr("src", src);
                    img.attr("title", data.message);
                } else {
                    alert(data.message);
                }
            },
            complete: function() {}
        });
    }
    // End of toggle_active_inactive

/*-------------------------------------------------------------------------------------------------*/
if (typeof News ==="object"){
    var NW_MODULE_URL = News.AddonUrl;
    var NW_ICONS = News.ThemeUrl + 'img';
    var NW_AJAX_PLUGINS =  News.AddonUrl + 'ajax';  // this var could change in the future
    var LANGUAGE = LANGUAGE ? LANGUAGE : 'EN'; // set var LANGUAGE to EN if LANGUAGE not set before
    /*
    console.info(News.AddonUrl + 'ajax' +"/ajaxActiveStatus.js");
    console.info(NW_ICONS);
    console.info(NW_AJAX_PLUGINS);
                    DB_COLUMN: 'post_id',
    */
            $.insert(  News.AddonUrl + 'ajax' +"/ajaxActiveStatus.js");
            // AjaxHelper change item active status
            $("td.active_status").ajaxActiveStatus({
                    MODULE : News.AddonUrl,
                    DB_RECORD_TABLE: '',
                    DB_COLUMN: 'post_id',
                    sFTAN: ''
            });
}
