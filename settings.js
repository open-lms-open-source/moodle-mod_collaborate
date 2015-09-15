/**
 * @namespace M.mod_collaborate
 * @author Guy Thomas
 */
M.mod_collaborate = M.mod_collaborate || {};
M.mod_collaborate.settings = M.mod_collaborate.settings || {};

/**
 * Initialise advanced forum javascript.
 * @param Y
 */
M.mod_collaborate.settings.init = function(Y, contextid) {
    M.cfg.context = contextid;
    M.mod_collaborate.settings.applyClickApiTest();
}

M.mod_collaborate.settings.onClickApiTest = function() {

    /**
     * Render new api status message.
     *
     * @param string stringkey
     * @param string alertclass
     */
    var api_msg = function(stringkey, alertclass) {
        var msg = M.util.get_string(stringkey, 'mod_collaborate');
        $('#api_diag .api-connection-status').remove();
        $('#api_diag').append('<div class="api-connection-status alert '+alertclass+'">'+msg+'</div>');
    }

    api_msg('verifyingapi', 'alert-info');

    $.ajax({
        url: M.cfg.wwwroot+'/mod/collaborate/rest.php',
        context: document.body,
        data: {'action':
            'contextid' : M.cfg.context},
        success: function(data) {
            if (data.success) {
                api_msg('connectionverified', 'alert-success');
            }
        },
        error: function() {
            api_msg('connectionfailed', 'alert-danger');
        }
    });
}

/**
 * Apply listener for api test button.
 *
 * @author Guy Thomas
 */
M.mod_collaborate.settings.applyClickApiTest = function() {
    $('#api_diag_notice').hide();
    $('.api_diag_btn').click(function(e){
        e.preventDefault();
        M.mod_collaborate.settings.onClickApiTest();
    });
}
