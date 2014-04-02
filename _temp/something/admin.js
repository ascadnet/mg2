
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


var debug = true;
var active_popup = false;
var active_page = '';
var active_updating_id = '';
var active_id = '';
var error_found = 0;
var active_pass_fields = '';
var deleting = '0';
var askConfirm = false;
var move = true;

/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Deletion Functions
 */
function delete_item(scope,id,pass_field)
{
    html = '<form action="returnnull.php" id="delete_form" method="post" onsubmit="return complete_delete();">';
    html += '<h1>Please Confirm Deletion</h1>';
    html += '<div class="pad24">';
    html += '<input type="hidden" name="id[' + id + ']" value="1" />';
    html += '<input type="hidden" name="scope" value="' + scope + '" />';
    html += '<input type="hidden" name="pass" value="' + pass_field + '" />';
    html += '<div class="pad32"><p>Please confirm that you wish to delete selected item(s).</p></div>';
    html += '</div>';
    html += '<div class="popup_confirm"><div class="float_right"><input type="submit" value="Delete" class="delete" /></div>';
    html += '<div class="float_left"><input type="button" value="Cancel" onclick="return close_popup();" /></div>';
    html += '<div class="clear"></div></div>';
    html += '</form>';
    if (active_popup && active_scope) {
        deleting = 1;
        change_popup(html);
    } else {
        // deleting = 1;
        show_popup(html);
    }
    return false;
}

function complete_delete()
{
    show_loading();
    send_data = $('#delete_form').serialize();
    $.post('/app/admin/functions/delete.php', send_data, function(theResponse) {
        var returned = theResponse.split('+++');
        // returned
        // ['0'] => 1 or 0
        // ['1'] => IDs that were deleted. JSON.
        // ['2'] => IDs that were not deleted. JSON.
        console.log(theResponse);
        if (returned['0'] == '1') {
            data = $.parseJSON(returned['1']);
            $.each(data, function(i, item) {
                table_cell_id = 'row-' + item;
                if ($("#" + table_cell_id).length > 0) {
                    $('#' + table_cell_id).fadeTo('fast', 0.25);
                    $('#' + table_cell_id).addClass('been_deleted');
                }
            });
            if (deleting == '1') {
                switch_popup(active_page, active_scope, active_id, active_pass_fields);
                deleting = 0;
            } else {
                close_popup();
            }
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
    askConfirm = false;
    move = true;
    show_loading();
    $.ajax({
        type: "POST",
        url: '/app/admin/functions/save.php',
        data: 'scope=' + scope + '&id=' + id + '&' + $('#popupform').serialize(),
        success: function (theResponse) {
            if (debug == 1) { console.log(theResponse); }
            var returned = theResponse.split('+++');
            // Success
            if (returned['0'] == '1') {
                handle_success(returned['1']);
                $('.remove_save').hide();
                needToConfirm = false;
                // move = true;
            }
            // Failed
            else {
                handle_json_error(returned['1']);
            }
        },
        error: function(error, txt) {
            handle_error(error.status);
        }
    });
    return false;
}


function handle_success(json_data)
{
    var data = $.parseJSON(json_data);
    if (debug == 1) { console.log(data); }
    $.each(data, function(action, additional_data) {
        process_success_action(action,additional_data);
    });
    active_updating_id = '';
    close_loading();
    close_error();
}


//   show_saved: Shows "Saved" message
//   close_popup: Closes popup
//   update_popup: Updates popup content
//   redirect_popup: Changes the location of a popup
//      page
//      fields (Query String)
//   append_table_row:
//   append
//   update_cells:
//   remove_cells
//   close_slider
//   refresh_slider:
//   reload
//   redirect_window
//   load_slider:
//   add_class:
//   image_src
//	id
//	class
//   remove_class:
//	id
//	class
//   change_popup_tab
//   change_slider:
function process_success_action(action,additional_data)
{
    if (action == 'show_saved') {
        show_saved(additional_data);
    }
    else if (action == 'close_popup') {
        close_popup();
    }
    else if (action == 'image_src') {
        $.each(additional_data, function(image_id, image_src) {
            $('#' + image_id).attr('src',image_src);
        });
    }
    else if (action == 'update_popup') {
        $('.popupbody').html(additional_data);
    }
    // Changes the view of the current popup
    // page,pass_fields,large
    else if (action == 'switch_popup') {
        switch_popup(additional_data.page,additional_data.scope,additional_data.id,additional_data.fields);
    }
    else if (action == 'append_table_row') {
        if ($("#active_table tbody").length > 0) {
            $("#active_table tbody:first").prepend(additional_data);
            $('#active_table tr.no_cells').remove();
        }
        else if ($("#active_div").length > 0) {
            $('#active_div').prepend(additional_data);
        }
    }
    else if (action == 'append') {
        $("#" + additional_data.id).append(additional_data.data);
    }
    else if (action == 'update_cells') {
        $.each(additional_data, function(cell_name, cell_value) {
            update_cell(cell_name,cell_value);
        });
    }
    else if (action == 'add_class') {
        $('#' + additional_data.id).addClass(additional_data.class);
    }
    else if (action == 'remove_class') {
        $('#' + additional_data.id).removeClass(additional_data.class);
    }
    else if (action == 'change_popup_tab') {
        goToStep(additional_data);
    }
    else if (action == 'remove_cells') {
        $.each(additional_data, function(cell_name, cell_value) {
            hide_div(cell_value);
        });
    }
    else if (action == 'update_row') {
        if ($('#row-' + active_updating_id).length > 0) {
            $('#row-' + active_updating_id).replaceWith(additional_data).addClass('cell_updated');
            setTimeout("hide_updated()", 8000);
        }
        else if ($("#active_div").length > 0) {
            $('#active_div').replaceWith(additional_data).addClass('cell_updated');
        }
    }
    else if (action == 'refresh_slider') {
        refresh_slider();
    }
    else if (action == 'close_slider') {
        close_slider();
    }
    else if (action == 'reload') {
        window.location.reload();
    }
    else if (action == 'redirect_window') {
        window.location = additional_data;
    }
    else if (action == 'load_slider') {
        load_page(additional_data.page,additional_data.subpage,additional_data.id);
    }
    else if (action == 'change_slider') {
        get_slider_subpage(additional_data.subpage);
    }
}

function update_cell(id, value)
{
    $('#' + id).html(value);
    $('#' + id).addClass('cell_updated');
    setTimeout("hide_updated()", 8000);
}

function hide_updated()
{
    $('.cell_updated').removeClass('cell_updated');
}

function handle_json_error(json_data)
{
    try {
        var data = $.parseJSON(json_data);
        if (debug == 1) { console.log(data); }
        $.each(data, function(field_name, anObject) {
            var field_errors = '';
            $.each(anObject, function(error_code, error_english) {
                field_errors += error_english + '<br />';
            });
            $('[name="' + field_name + '"]').addClass('warning');
            $('[name="' + field_name + '"]').after('<div class="zen_field_error">' + field_errors + '</div>');
        });
    } catch (e) {
        handle_error(json_data);
    }
    close_loading();
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

function popup(page,scope,id,pass_fields)
{
    show_loading();

    active_page = page;
    active_id = id;
    active_scope = scope;
    active_updating_id = id;
    active_pass_fields = pass_fields;

    $.ajax({
        type: "POST",
        url: '/app/admin/functions/popup.php',
        data: 'p=' + page + '&id=' + id + '&scope=' + scope + '&' + pass_fields,
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

function reload_popup()
{
    switch_popup(active_page, active_scope, active_id, active_pass_fields);
}

function switch_popup(page,scope,id,pass_fields)
{
    if (askConfirm === true) {
        move = false;
        if (confirm('You have made changes that are not yet saved. Please confirm that you wish to continue without saving?')) {
            move = true;
        }
    }
    if (move === true) {
        askConfirm = false;
        show_loading();
        active_page = page;
        active_id = id;
        active_scope = scope;
        active_pass_fields = pass_fields;
        active_updating_id = id;
        $.ajax({
            type: "POST",
            url: '/app/admin/functions/popup.php',
            data: 'p=' + page + '&id=' + id + '&scope=' + scope + '&' + pass_fields,
            success: function (theResponse) {
                if (debug) { console.log(theResponse); }
                // close_error();
                var returned = theResponse.split('+++');
                if (returned['0'] == '1') {
                    change_popup(returned['1'], returned['2']);
                } else {
                    handle_error(returned['1']);
                }
            },
            error: function(error, txt) {
                handle_error(error.status);
            }
        });
        return false;
    } else {
        return false;
    }
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
        url: pf_url + '/app/admin/functions/' + page + '.php',
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
        top = top; // + 30
        // Window Height
        //var windowHeight = $(window).height();
        //windowHeight = windowHeight - 100;
        // height:' + windowHeight + 'px;
        // Content
        final_content = '<div class="popup" id="active_popup" style="top:' + top + 'px;">';
        final_content += '<div class="close_popup" onclick="return close_popup();"></div>';
        final_content += '<div id="popup_inner">' + content + '</div>';
        final_content += '</div>';
        final_content += '<script src="/app/admin/assets/js/popup_forms.js" type="text/javascript" />';
        // Append it.
        $('body').append($(final_content).hide().add('<div class="faded"></div>').fadeIn(250,function() {
            check_populate(data);
        }));
    }
}

function delete_popup()
{

}

function change_popup(content,data)
{
    // Append it.
    // content += '<script src="/app/admin/assets/js/popup_forms.js" type="text/javascript" />';
    $('#popup_inner').html(content);
    check_populate(data)
}

function check_populate(data)
{
    var frm = '#popupform';
    if (data && $(frm).length > 0) {
        var object = jQuery.parseJSON(data);
        $.each(object, function(key, value) {
            var $ctrler = $('[name="' + key + '"]', frm);
            switch($ctrler.attr("type"))
            {
                case "text":
                case "hidden":
                case "textarea":
                case "select":
                    $ctrler.val(value);
                    break;
                case "radio" : case "checkbox":
                    $ctrler.each(function() {
                        if ($(this).attr('value') == value) {
                            $(this).attr("checked",value); }
                    });
                break;
                default:
                    $ctrler.val(value);
                    break;
            }
        });
    }
    check_limits();
    close_loading();
}


/**
 * Checks textarea maxlength.
 */

function check_limits()
{
    $('textarea.limit').each(function() {
        limits($(this));
    });
}

function limits(obj){
    var text = $(obj).val();
    var length = text.length;
    var name = $(obj).attr('name');
    var maxlength = $(obj).attr('maxlength');
    if(length > maxlength){
        $(obj).val(text.substr(0,maxlength));
    } else {
        var remaining = maxlength - length;

        // alert(remaining + '--' + maxlength + '--' + length);

        $('#' + name + '_limit').html(remaining);
    }
}

/**
 * Close a popup
 */

function close_popup()
{
    if (askConfirm === true) {
        move = false;
        if (confirm('You have made changes that are not yet saved. Please confirm that you wish to continue without saving?')) {
            move = true;
        }
    }
    if (move === true) {
        $('.popup').add('.close_popup').add('.faded').fadeOut(200,function(){
            $('.popup').remove();
            $('.close_popup').remove();
            $('.faded').remove();
            active_popup = false;
            askConfirm = false;
        });
    }
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
            error_data += '  <div id="error_slide_close" onclick="return close_error();"></div><p>The following errors occurred while attempting to complete that action. Please make the necessary corrections: the information being requested is vital for creating a proper SR&amp;ED claim.</p><ul>';
            error_data += error;
            error_data += '</ul></div>';
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
    all_errors = '';

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
                applyError(name, 'Required');
                all_errors += '<li>' + name + ' is required.</li>';
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
                all_errors += '<li>' + name + ' must be a number.</li>';
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
                all_errors += '<li>' + name + ' must be letters and numbers only.</li>';
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
                all_errors += '<li>' + name + ' must be letters only.</li>';
            }
        }
    });

    // Money
    $('.time').each(function (i) {
        name = $(this).attr('name');
        removeError(name);
        if ($(this).hasClass('req') || $(this).val().length > 0) {
            if (check_time($(this).val())) {

            } else {
                error_found = 1;
                applyError(name,'Input a correct time format (hh:mm)');
                all_errors += '<li>' + name + ' must be a valid time.</li>';
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
                all_errors += '<li>' + name + ' must be a valid monetary value.</li>';
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
            all_errors += '<li>' + name + ' must be a valid email address.</li>';
        }
    });

    // Data Lengths
    if (error_found == 1) {
        handle_error(all_errors);
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
        $('[name="' + name + '"]').after(html);
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


function check_time(val) {
    var regexp = /([01]?[0-9]|2[0-3]):[0-5][0-9]/;
    var correct = regexp.test(val);
    return correct;
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



$("input, textarea").live("keyup", function() {
    askConfirm = true;
});
$("select").live("change", function() {
    askConfirm = true;
});
