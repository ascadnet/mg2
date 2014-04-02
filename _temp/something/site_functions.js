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

/**
 * Form Functions
 */

$(document).ready(function() {
    $('.anet_validate').submit(function() {
        id = $(this).attr('id');
        return validateForm(id);
    });
});

function validateForm(id)
{
    // Begin process
    show_loading();
    // Remove classes and errors.
    $('#' + id + ' input, #' + id + ' select').removeClass('anet_error_highlight');
    hide('anet_field_error','1');
    // Make ajax request
    $.ajax({
        type: "POST",
        url: "/app/admin/functions/ajax_check_form.php",
        data: $('#' + id).serialize(),
        dataType: "json",
        success: function (reply) {
            if (reply != '1') {
                // Loop JSON errors
                $.each(reply, function(field_name, anObject) {
                    $.each(anObject, function(key, value) {
                        $('[name=' + field_name + ']').addClass('anet_error_highlight');
                        $('[name=' + field_name + ']').after('<div class="anet_field_error">' + value + '</div>');
                    });
                });
                // Enable Submit
                $('#submit_button').removeAttr('disabled');
                // Close loading
                close_loading();
                return false;
            } else {
                document.getElementById(id).submit();
            }
        },
        error: function(error, txt) {
            alert("Error: " + error.status);
        }
    });
    return false;
}


/**
 * Element manipulation functions
 */

function show(id,use_class)
{
    if (use_class == '1') {
        $('.' + id).fadeIn('200');
    } else {
        $('#' + id).fadeIn('200');
    }
    return false;
}


function hide(id,use_class)
{
    if (use_class == '1') {
        $('.' + id).fadeOut('200');
    } else {
        $('#' + id).fadeOut('200');
    }
    return false;
}


function remove(id,use_class)
{
    if (use_class == '1') {
        $('.' + id).remove();
    } else {
        $('#' + id).remove();
    }
    return false;
}


function toggle(id)
{
    $('#' + id).slideToggle();
    return false;
}


function swap(show,hide)
{
    var spliting = hide.split(',');
    for (var i = 0; i < spliting.length; i++) {
        $('#' + spliting[i]).hide();
    }
    var splitingA = show.split(',');
    for (var i = 0; i < splitingA.length; i++) {
        $('#' + splitingA[i]).fadeIn('100');
    }
    return false;
}


function show_loading()
{
    var top = $(window).scrollTop();
    top = top + 8;
    var final_content = '<div class="anet_loading" onclick="return close_loading();"><img src="/app/admin/assets/imgs/loading.gif" width="32" height="32" alt="Loading" border="0" /></div>';
    $('body').append(final_content);
    return false;
}

function close_loading()
{
    remove('anet_loading','1');
}