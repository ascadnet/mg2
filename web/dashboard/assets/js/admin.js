
/**
 * Admin functions
 *
 * This class is included in this project
 * but belongs to the "Ascad Networks Framework".
 * While the overall project is copyrighted to
 * "Penn Foster", the contents of this file are
 * distributed under the "GPL3" license:
 * http://www.gnu.org/licenses/gpl.html
 *
 * @author      Ascad Networks
 * @link        http://www.ascadnetworks.com/
 * @version     v1.0
 * @project     Penn Foster Forms
 */


var debug = true;
var active_popup = false;
var active_page = '';
var active_id = '';
var error_found = 0;


/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Deletion Functions
 */
function delete_item(scope,id)
{
    html = '<form action="returnnull.php" id="delete_form" method="post" onsubmit="return complete_delete();">';
    html += '<h1>Please Confirm Deletion</h1>';
    html += '<div class="pad24">';
    html += '<input type="hidden" name="id[' + id + ']" value="1" />';
    html += '<input type="hidden" name="scope" value="' + scope + '" />';
    html += '<p>Please confirm that you wish to delete selected items.</p>';
    html += '</div>';
    html += '<div class="popup_confirm"><div class="float_right"><input type="submit" value="Delete" class="delete" /></div>';
    html += '<div class="float_left"><input type="button" value="Cancel" onclick="return close_popup();" /></div>';
    html += '<div class="clear"></div></div>';
    html += '</form>';
    show_popup(html);
    return false;
}

function complete_delete()
{
    show_loading();
    send_data = $('#delete_form').serialize();
    $.post(pf_url + '/' + pf_admin + '/functions/delete.php', send_data, function(theResponse) {
        console.log(theResponse);
        var returned = theResponse.split('+++');
        // returned
        // ['0'] => 1 or 0
        // ['1'] => IDs that were deleted. JSON.
        // ['2'] => IDs that were not deleted. JSON.
        if (returned['0'] == '1') {
            data = $.parseJSON(returned['1']);
            $.each(data, function(i, item) {
                table_cell_id = 'row-' + item;
                if ($("#" + table_cell_id).length > 0) {
                    $('#' + table_cell_id).fadeTo('fast', 0.25);
                    $('#' + table_cell_id).addClass('been_deleted');
                }
            });
            close_popup();
            close_loading();
        } else {
            handle_error(returned['1']);
        }
    });
    return false;
}


/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Save: add and edit functions.
 */

function save(scope,id,form_id,skip_form_validate)
{
    // Validate the form first.
    if (! form_id) { form_id = 'popupform'; }
    if (skip_form_validate != '1') {
        req = validate_form(form_id);
        if (req === false) {
            close_loading();
            return false;
        }
    }
    // Make the update/addition
    show_loading();
    $.ajax({
        type: "POST",
        url: pf_url + '/' + pf_admin + '/functions/save.php',
        data: 'scope=' + scope + '&id=' + id + '&' + $('#popupform').serialize(),
        success: function (theResponse) {
            if (debug) { console.log(theResponse); }
            var returned = theResponse.split('+++');
            if (returned['0'] == '1') {
                update_table(id,returned['2']);
                close_popup();
                close_error();
                close_loading();
                show_saved();
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

function update_table(id,data)
{
    //if (id && data && $('#listings').length > 0) {
        var object = jQuery.parseJSON(data);
        $.each(object, function(key, value) {
            var cell = 'cell-' + id + '-' + key;
            $('#' + cell).fadeOut('100', function() {
                $('#' + cell).html(value);
                $('#' + cell).fadeIn('100');
                $('#' + cell).addClass('updated');
                setTimeout("hide_updated()", 8000);
            });
        });
    //}
}

function hide_updated()
{
    $('.updated').removeClass('updated');
}

function show_saved(msg)
{
    if (! msg) {
        msg = 'Saved';
    }
    var saved_data = '<div id="saved" onclick="return close_saved();">' + msg + '</div>';
    $('body').append($(saved_data).hide().fadeIn(75));
    setTimeout("close_saved()", 6000);
    return false;
}

function close_saved()
{
    $('#saved').fadeOut('75',function() {
        $('#saved').remove();
    });
    return false;
}



/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Inline popup functions
 * Flow: popup -> show_popup -> check_populate
 * Returned: [SUCCESS:1 or 0]+++[DATA]+++[FORM_DATA:json]
 */

function popup(page,scope,id)
{
    show_loading();

    active_page = page;
    active_id = id;
    active_scope = scope;

    $.ajax({
        type: "POST",
        url: pf_url + '/' + pf_admin + '/functions/popup.php',
        data: 'p=' + page + '&id=' + id + '&scope=' + scope,
        success: function (theResponse) {
            if (debug) { console.log(theResponse); }
            var returned = theResponse.split('+++');
            if (returned['0'] == '1') {
                show_popup(returned['1'],returned['2']);
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

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Inline popup functions
 * Similar to popup(), except that it runs a specific
 * file within admin/functions rather than popup.php.
 * Flow: custom -> page -> check_populate
 * Returned: [SUCCESS:1 or 0]+++[DATA]+++[FORM_DATA:json]
 */

function custom(page,scope,id)
{
    show_loading();

    active_page = page;
    active_id = id;
    active_scope = scope;

    $.ajax({
        type: "POST",
        url: pf_url + '/' + pf_admin + '/functions/' + page + '.php',
        data: 'id=' + id + '&scope=' + scope,
        success: function (theResponse) {
            if (debug) { console.log(theResponse); }
            var returned = theResponse.split('+++');
            if (returned['0'] == '1') {
                show_popup(returned['1'],returned['2']);
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

/**
 * Displays popup and populates the
 * form, if any.
 * @param content Popup content.
 * @param data JSON data, if any.
 */
function show_popup(content,data)
{
    if (active_popup) {
        check_populate(data);
    } else {
        // Active
        active_popup = true;
        // Get positioning
        var top = $(window).scrollTop();
        top = top + 30;
        // Content
        final_content = '<div class="close_popup" style="top:' + top + 'px;" onclick="return close_popup();"></div><div class="popup" id="active_popup" style="top:' + top + 'px;">';
        final_content += '<div id="popup_inner">' + content + '</div>';
        final_content += '</div>';
        // Append it.
        $('body').append($(final_content).hide().add('<div class="faded"></div>').fadeIn(250,function() {
            check_populate(data);
        }));
    }
}

function check_populate(data)
{
    var frm = '#popupform';
    if (data && $(frm).length > 0) {
        var object = jQuery.parseJSON(data);
        $.each(object, function(key, value) {
            var $ctrler = $('[name=' + key + ']', frm);
            switch($ctrler.attr("type"))
            {
                case "text":
                case "hidden":
                case "textarea":
                    $ctrler.val(value);
                    break;
                case "radio" : case "checkbox":
                $ctrler.each(function(){
                    if($(this).attr('value') == value) {  $(this).attr("checked",value); } });
                break;
                default:
                    $ctrler.val(value);
                    break;
            }
        });
    }
    close_loading();
}

function close_popup()
{
    $('.popup').add('.close_popup').add('.faded').fadeOut(200,function(){
        $('.popup').remove();
        $('.close_popup').remove();
        $('.faded').remove();
        active_popup = false;
    });
    return false;
}



/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Miscellaneous function
 */

function handle_error(error)
{
    if (error) {
        if (error == 'redirect') {
            window.location = pf_url + '/' + pf_admin + '/' + 'login?error=E007';
        } else {
            $('#error_slide').remove();
            $('#error_slide_close').remove();
            var error_data = '<div id="error_slide">';
            error_data += '  <div id="error_slide_close" onclick="return close_error();"></div>';
            error_data += error;
            error_data += '</div>';
            $('body').append(error_data);
            $('#error_slide').fadeIn('75');
            close_loading();
        }
    }
}


function close_error()
{
    $('#error_slide').fadeOut('75',function() {
       $('#error_slide').remove();
    });
}



/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Validate a Form
 */

function validate_form(formid) {
    if (! formid) { formid = 'popupform'; }

    remove_all_errors();
    error_found = 0;

    // Required fields
    $('#' + formid + ' input.req, #' + formid + ' select.req, #' + formid + ' textarea.req').each(function (i) {
        name = $(this).attr('name');
        type = $(this).attr('type');
        removeError(name);
        // Checkbox
        if (type == 'checkbox') {

        }
        else {
            if ($(this).val().length === 0) {
                error_found = 1;
                applyError(name);
            }
        }
    });

    // Data types
    // Numbers only
    $('.num').each(function (i) {
        name = $(this).attr('name');
        removeError(name);
        if ($(this).hasClass('req') || $(this).val().length > 0) {
            if (/^[0-9]+$/.test( $(this).val() ) !== true) {
                error_found = 1;
                applyError(name,'Numbers only!');
            }
        }
    });

    // Letters and numbers
    $('.letnum').each(function (i) {
        name = $(this).attr('name');
        removeError(name);
        if ($(this).hasClass('req') || $(this).val().length > 0) {
            if (/^[0-9a-zA-Z�����������������������������������������������������]+$/.test( $(this).val() ) !== true) {
                error_found = 1;
                applyError(name,'Letters and numbers only!');
            }
        }
    });

    // Letters only
    $('.let').each(function (i) {
        name = $(this).attr('name');
        removeError(name);
        if ($(this).hasClass('req') || $(this).val().length > 0) {
            if (/^[a-zA-Z�����������������������������������������������������]+$/.test( $(this).val() ) !== true) {
                error_found = 1;
                applyError(name,'Letters only!');
            }
        }
    });

    // Money
    $('.money').each(function (i) {
        name = $(this).attr('name');
        removeError(name);
        if ($(this).hasClass('req') || $(this).val().length > 0) {
            if (/^[0-9.]+$/.test( $(this).val() ) !== true) {
                error_found = 1;
                applyError(name,'Input a proper value.');
            }
        }
    });

    // E-Mails
    $('.email').each(function (i) {
        check_em = check_email($(this).val());
        name = $(this).attr('name');
        removeError(name);
        if (check_em != '1') {
            error_found = 1;
            applyError(name,'Incorrect email format!');
        }
    });

    // Data Lengths
    if (error_found == 1) {
        return false;
    } else {
        return true;
    }
}

/**
 * Add/remove errors
 */
function applyError(name,message) {
    $('[name="' + name + '"]').addClass('warning');
    if (message) {
        html = '<div id="blockerror-' + message + '" class="block_error">' + message + '</div>';
        $('[name=' + name + ']').after(html);
    }
}

function removeError(name) {
    $('[name="' + name + '"]').addClass('warning');
    $('#blockerror-' + name).remove();
}

function remove_all_errors() {
    $('.block_error').remove();
    $('input.warning').removeClass('warning');
}

/**
 * Verify an email address
 */
function check_email(email) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    if (pattern.test(email) === false) {
        return '0';
    } else {
        return '1';
    }
};
