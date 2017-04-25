/**
 * AJAX
 *        Plugin to delete Records from a given table without a new page load (no reload)
 */
// Building a jQuery Plugin
// using the Tutorial: http://www.learningjquery.com/2007/10/a-plugin-development-pattern
// plugin definition
/*
ajaxActiveStatus OPTIONS
=====================================================================================
MODULE = 'modulename',   // (string)
DB_RECORD_TABLE: 'modulename_table',        // (string)
DB_COLUMN: 'item_id',                       // (string) the key column you will use as reference
sFTAN: ''                                   // (string) FTAN
*/

(function($) {
        $.fn.ajaxActiveStatus = function(options) {
                var aOpts = $.extend({}, $.fn.ajaxActiveStatus.defaults, options);
console.info(aOpts);
                $(this).find('a').removeAttr("href").css('cursor', 'pointer');
                $(this).click(function() {
                        var oLink = $(this).find('a');
                        var oElement = $(this).find('img');
                        var iRecordID = oElement.attr("id");
                        var oRecord = $("td#" + aOpts.DB_COLUMN +'_'+ iRecordID);
                        switch(oElement.attr("src")){
                            case News.ThemeUrl + 'img/24' +"/status_1.png": var action = "0"; break;
                            case News.ThemeUrl + 'img/24' +"/status_0.png": var action = "1"; break;
                        }
                                // pregenerate the data string
                        var sDataString = 'purpose=active_status&action=active_status'+'&MODULE='
                                        +aOpts.MODULE+'&DB_COLUMN='
                                        +aOpts.DB_COLUMN+'&iRecordID='
                                        +iRecordID;
                        $.ajax({
                                url: News.AddonUrl +"/ajax/ajax.php",
                                type: "POST",
                                dataType: 'json',
                                data: sDataString,
                                success: function(json_respond) {
                                    if(json_respond.success == true) {
//                                        oElement.animate({opacity: 0.55}), 'fast';
                                        oElement.attr("src", News.ThemeUrl + 'img/24' +"/status_"+ action +".png");
//                                        oElement.animate({opacity: 1});
                                    } else {
                                            alert(json_respond.message);
                                    }
                                }
                        });

                });
        }
})(jQuery);
