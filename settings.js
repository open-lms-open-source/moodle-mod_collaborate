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
M.mod_collaborate.settings = {
    init : function(Y, contextid) {
        M.cfg.context = contextid;
        if (!M.mod_collaborate.settings.initialised) {
            this.api_msg('connectionstatusunknown', 'alert-info');
        }
        M.mod_collaborate.settings.applySettingChangeCheck();
        M.mod_collaborate.settings.applyClickApiTest();
        if ($('#id_s_collaborate_server').val() +
            $('#id_s_collaborate_username').val() +
            $('#id_s_collaborate_password').val() !== '') {
            M.mod_collaborate.settings.testApi();
        }
        M.mod_collaborate.settings.initialised = true;
    },

    /**
     * Render new api status message.
     *
     * @param string stringkey
     * @param string alertclass
     */
    api_msg : function (stringkey, alertclass, extraclasses) {
        var msg = M.util.get_string(stringkey, 'mod_collaborate');

        var msgcontainer = $('#api_diag .noticetemplate.' + alertclass).clone();
        msgcontainer.removeClass('noticetemplate'); // Essential, this isn't a template anymore!
        $(msgcontainer).addClass(extraclasses);

        $(msgcontainer).html('<span class="api-connection-msg">' + msg + '</span>');

        $(msgcontainer).append($('#api_diag .api_diag_btn'));

        // Wipe out existing connection status msg container.
        $('#api_diag .api-connection-status').empty();

        // Put in new msg container
        $('#api_diag .api-connection-status').append($(msgcontainer));

    },

    /**
     * Test api.
     */
    testApi : function() {

        var self = this;
        self.api_msg('verifyingapi', 'alert-info', 'spinner');

        $.ajax({
            url: M.cfg.wwwroot + '/mod/collaborate/testapi.php',
            context: document.body,
            data: {
                'contextid': M.cfg.context,
                'server': $('#id_s_collaborate_server').val().trim(),
                'username': $('#id_s_collaborate_username').val().trim(),
                'password': $('#id_s_collaborate_password').val() // Never trim passwords!
            },
            success: function (data) {
                if (data.success) {
                    if (!M.mod_collaborate.settings.modified) {
                        self.api_msg('connectionverified', 'alert-success');
                    } else {
                        self.api_msg('connectionverifiedchanged', 'alert-success');
                    }
                } else {
                    self.api_msg('connectionfailed', 'alert-danger');
                }
            },
            error: function () {
                self.api_msg('connectionfailed', 'alert-danger');
            }
        });
    },

    /**
     * Apply listener for api test button.
     *
     * @author Guy Thomas
     */
    applyClickApiTest : function() {
        $('#api_diag_notice').hide();
        $('.api_diag_btn').click(function(e){
            e.preventDefault();
            M.mod_collaborate.settings.testApi();
        });
    },

    /**
     * Apply listener for when settings changed.
     *
     * @author Guy Thomas
     */
    applySettingChangeCheck : function() {
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
}