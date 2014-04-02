
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
 * Element manipulation
 */
function showDiv(id) {
    $('#' + id).fadeIn('50');
    return false;
}

function hideDiv(id) {
    $('#' + id).fadeOut('50');
    return false;
}

function removeDiv(id) {
    $('#' + id).remove();
    return false;
}

function swap_div(show,hide) {
    $('#' + hide).fadeOut('50', function() {
        $('#' + show).fadeIn('50');
    });
    return false;
}

function swap_multi_div(show,hide) {
    var spliting = hide.split(',');
    for (var i = 0; i < spliting.length; i++) {
        $('#' + spliting[i]).hide();
    }
    var splitingA = show.split(',');
    for (var i = 0; i < splitingA.length; i++) {
        $('#' + splitingA[i]).fadeIn('50');
    }
    return false;
}