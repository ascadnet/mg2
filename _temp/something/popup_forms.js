
/**
 *
 *
 * @project     SREDengine
 * @link        http://www.sredengine.com/
 *
 * This file is built on the "Ascad Framework". All
 * elements of the Ascad Framework remain copyrighted
 * to their respective owner.
 *
 * @author      Ascad Networks
 * @link        http://www.ascadnetworks.com/
 * @license     Commercial
 * @link        http://www.ascadnetworks.com/Framework/License
 * @date        4/22/13
 * @version     v1.0
 * @project     Ascad Framework
 */

$(document).ready(function() {

    var height = $('#popup_inner').height();
    $('#current_tip').height(height);

    /**
     * Tooltips
     */
    $('#popupform input, #popupform textarea, #popupform select').on('focus', function() {
        /*
        var name = $(this).attr('name');
        var position = $(this).position();
        var clean = name.replace(/[,]/g, "");
        if ($('#' + name + '-tooltip').length > 0) {
            var content = $('#' + clean + '-tooltip').html();
            var dif = position.top - 45;
            //$('#current_tip_hold').css('top',dif + 'px');
            $('#current_tip').html(content);
            $('#current_tip_hold').show();
        }
        */
        var name = $(this).attr('name');
        show_tooltip(name);
    });

    $('#popupform input, #popupform textarea, #popupform select').off('focus', function() {
        $('#current_tip_hold').hide();
    });

    /**
     * Textarea limit checks
     * Functions on admin.js
     */
    $('textarea.limit').on('keyup',function() {
        limits($(this));
    });

    /**
     * For popup inline tabs
     */
    if ($('ul#tabs').length != 0) {
        $('ul#tabs li:not(:first)').not('ul.anet_form li').hide();
    }

    $("ul.sublinks li").on("click", function(){
        var index = $(this).index();
        load_tab(index);
    });

});

function show_tooltip(name)
{
    var position = $('#' + name + '-tooltip').position();
    var clean = name.replace(/[,]/g, "");
    if ($('#' + name + '-tooltip').length > 0) {
        var content = $('#' + clean + '-tooltip').html();
        var dif = position.top - 45;
        //$('#current_tip_hold').css('top',dif + 'px');
        $('#current_tip').html(content);
        $('#current_tip_hold').show();
    }
}

function load_tab(index)
{
    $('ul#tabs li').hide();
    $('ul#tabs li').not('ul.anet_form li').eq(index).show();
    $('ul.sublinks li').removeClass('on');
    $('ul.sublinks li').eq(index).addClass('on');
    hide_help();
    return false;
}

function hide_help()
{
    $('#current_tip_hold').hide();
}


function get_widget(scope,id)
{
    show_loading();
    $.ajax({
        type: "POST",
        url: '/app/admin/functions/get_widget.php',
        data: 'id=' + id + '&scope=' + scope,
        success: function (theResponse) {
            if (debug) { console.log(theResponse); }
            var returned = theResponse.split('+++');
            if (returned['0'] == '1') {
                $('#place-' + scope).append(returned['1']);
                close_loading();
            } else {
                handle_error(returned['1']);
            }
        },
        error: function(error, txt) {
            handle_error(error.status);
        }
    });
    return false;
}
