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
    if (!M.mod_collaborate.settings.initialised) {
        var msg = M.util.get_string('connectionstatusunknown', 'mod_collaborate');
        var statushtml = '<div class="api-connection-status alert alert-info">' +
            '<span class="api-connection-msg">' + msg + '</span>' +
            '</div>';
        $('#api_diag').append(statushtml);
        $('#api_diag .api-connection-status').append($('#api_diag .api_diag_btn'));
    }
    M.mod_collaborate.settings.applySettingChangeCheck();
    M.mod_collaborate.settings.applyClickApiTest();
    if ($('#id_s_collaborate_server').val() +
        $('#id_s_collaborate_username').val() +
        $('#id_s_collaborate_password').val() !== '') {
        M.mod_collaborate.settings.testApi();
    }
    M.mod_collaborate.settings.initialised = true;
}

/**
 * Test api.
 */
M.mod_collaborate.settings.testApi = function() {

    /**
     * Render new api status message.
     *
     * @param string stringkey
     * @param string alertclass
     */
    var api_msg = function(stringkey, alertclass) {
        var msg = M.util.get_string(stringkey, 'mod_collaborate');
        var classes = ['alert-info', 'alert-danger', 'alert-success', 'spinner'];
        for (var c in classes) {
            $('#api_diag .api-connection-status').removeClass(classes[c]);
        }
        $('#api_diag .api-connection-status').addClass(alertclass);
        $('#api_diag .api-connection-msg').html(msg);
    }

    api_msg('verifyingapi', 'alert-info spinner');

    $.ajax({
        url: M.cfg.wwwroot + '/mod/collaborate/testapi.php',
        context: document.body,
        data: {
            'contextid' : M.cfg.context,
            'server' : $('#id_s_collaborate_server').val().trim(),
            'username'  : $('#id_s_collaborate_username').val().trim(),
            'password'  : $('#id_s_collaborate_password').val() // Never trim passwords!
        },
        success: function(data) {
            if (data.success) {
                if (!M.mod_collaborate.settings.modified) {
                    api_msg('connectionverified', 'alert-success');
                } else {
                    api_msg('connectionverifiedchanged', 'alert-success');
                }
            } else {
                api_msg('connectionfailed', 'alert-danger');
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
        M.mod_collaborate.settings.testApi();
    });
}

/**
 * Apply listener for when settings changed.
 *
 * @author Guy Thomas
 */
M.mod_collaborate.settings.applySettingChangeCheck = function() {
    var settingfields = [
        '#id_s_collaborate_server',
        '#id_s_collaborate_username',
        '#id_s_collaborate_password'
    ];
    for (var s in settingfields) {
        $(settingfields[s]).keypress(function(e) {
            M.mod_collaborate.settings.modified = true;
        });
    }
}
