
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
 * Make an ajax call.
 * @param string url URL to which data is being POSTed.
 * @param string form Form to be serialized, if any
 * @param string data Data to append to string, if any
 */
function ajax(url,form,data) {
    var send_data = '';
    if (form) { send_data += '&' + $('#' + form).serialize(); }
    if (data) { send_data += '&' + data;  }
    send_data.substr(1);
    $.post(url, send_data, function(theResponse) {
        // console.log(theResponse);
        handle_reply(theResponse);
    });
    return false;
}

/**
 * Handle an Ajax reply
 * @param string theResponse Data returned from the server.
 */
function handle_reply(theResponse) {
    var returned = theResponse.split('+++');
    if (returned['0'] == '1') {
        handle_success(theResponse);
    } else {
        handle_error(theResponse);
    }
}

/**
 * Handle a success reply.
 * @param theResponse
 *  ['0']   0+++
 *  ['1']   MESSAGE+++
 *  ['2']   COMMAND
 */
function handle_success(theResponse) {
    var returned = theResponse.split('+++');
    if (returned['2'] == 'redirect') {
        window.location = returned['1'];
    }
    else if (returned['2'] == 'show_error') {
        error_msg = "<div id='anet_block_success'>" + returned['1'] + "</div>";
        $('body').append(error_msg);
    }
}

/**
 * Handle an error reply.
 * @param theResponse
 *  ['0']   0+++
 *  ['1']   MESSAGE+++
 *  ['2']   COMMAND+++
 *  ['3']   string COMMAND_ACTION (DIV::MSG--DIV2::MSG2)
 */
function handle_error(theResponse) {
    var error_msg = '';
    var returned = theResponse.split('+++');
    if (returned['2'] == 'redirect') {
        window.location = returned['1'];
    }
    else if (returned['2'] == 'show_error') {
        error_msg = "<div id='anet_block_error'>" + returned['1'] + "</div>";
        $('body').append(error_msg);
    }
    else if (returned['2'] == 'show_inline_error') {
        var error_array = returned['3'].split('--');
        for (var i = 0; i < error_array.length; i++) {
            var this_error = error_array[i].split('::');
            error_msg = "<div class='anet_error'>" + this_error['1'] + "</div>";
            $('#' + this_error['0']).after(error_msg);
        }
    }
}