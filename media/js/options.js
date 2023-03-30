var TiledeskChatWP = {
    token: null,
    setRedirectLink: function (url) {
        jQuery('a[href="admin.php?page=tiledesk-chat"]')
            .attr('href', url)
            .attr('target', '_blank')
        jQuery('#open-panel-link').attr('href', url)
    },
    renderProjects: function (projectusers) {
        var select_project = jQuery('#select-tiledesk-project')
        projectusers.forEach(function (projectuser) {
            console.log('projectuser', projectuser)
            var project = projectuser.id_project;
            +console.log('project', project)
            var value = { project_id: project._id }
            var option = jQuery(
                '<option value="' + project._id + '">' + project.name +
                '</option>')
            option.data('value', value)
            select_project.append(option)
        })

        jQuery('#input-blocks').fadeOut('fast', function () {
            jQuery('#projects-selector').append(select_project)
            jQuery('#select-project').fadeIn('fast', function () {
                jQuery('#tiledesk-login-button')
                    .prop('disabled', false)
                    .text('Log in')
            })
        })
    },
    getProjects: function (token) {
        jQuery.ajax({
            type: 'GET',
            url: tiledesk_chat.api_url + '/projects',
            headers: { 'Authorization': token },
            success: function (response) {
                console.log('response22', response)
                TiledeskChatWP.renderProjects(response)
            },
        })
    },
    showError: function (message) {
        jQuery('#tiledesk-wrapper .error')
            .empty()
            .append('<p>' + message + '</p>')
            .show()
    },
    hideError: function () {
        jQuery('#tiledesk-wrapper .error').hide()
    },
    init: function () {
        var login_button = jQuery('#tiledesk-login-button')

        /* Login */
        login_button.click(function (e) {
            TiledeskChatWP.hideError()
            var error = false
            e.preventDefault()
            var email = jQuery('#tiledesk-login-input').val()
            var password = jQuery('#tiledesk-password-input').val()

            if (email.length === 0 || password.length === 0) {
                TiledeskChatWP.showError(
                    'Please fill email and password fields.')
                error = true
            }
            else if (email === '' || email.indexOf('@') === -1 ||
                email.indexOf('.') === -1) {
                TiledeskChatWP.showError('Email is wrong')
                error = true
            }

            if (error) {
                return false
            }

            login_button.prop('disabled', true).text('Loading...')

            jQuery.post(tiledesk_chat.api_url + 'auth/signin', {
                email: email,
                password: password,
            }, function (data) {
                console.log('data', data)
                TiledeskChatWP.token = data.token
                console.log('TiledeskChatWP', TiledeskChatWP)
                TiledeskChatWP.getProjects(TiledeskChatWP.token)
            }, 'json').fail(function (xhr) {
                alert(xhr.responseJSON.msg)
                login_button.prop('disabled', false).text('Login')
            })
        })

        /* Load project details */
        jQuery('#get-tiledesk-project').click(function (e) {
            e.preventDefault()
            jQuery('#get-tiledesk-project')
                .prop('disabled', true)
                .text('Loading...')

            var details = jQuery('#select-tiledesk-project option:selected')
                .data('value')

            jQuery.extend(details, { 'action': 'get_project_keys' })

            jQuery.post(ajaxurl, details, function (response) {
                TiledeskChatWP.setRedirectLink(response)
                jQuery('#welcome-text').fadeOut('fast', function () {
                    jQuery('#after-install-text').fadeIn('fast')
                })
                jQuery('#select-project').fadeOut('fast')
            })
        })

        /* No account login */
        /*  jQuery('#redirect-to-panel').click(function (e){
             e.preventDefault();
             jQuery('#redirect-to-panel').prop('disabled', true).text('Loading...');
             var details = {'action': 'get_private_key'};
             jQuery.post(ajaxurl, details, function(response) {
                 if(response=='error'){
                     // load trought ajax url
                     TiledeskChatWP.accessTroughtXHR(function(response){
                         window.open(response, "_blank");
                         TiledeskChatWP.setRedirectLink(response);
                         jQuery('#welcome-text').fadeOut('fast', function(){
                             jQuery('#after-install-text').fadeIn('fast');
                         });
                         jQuery('#input-blocks').fadeOut('fast');
                     });
                     return false;
                 }
                 //
                 window.open(response, "_blank");
                 TiledeskChatWP.setRedirectLink(response);
                 jQuery('#welcome-text').fadeOut('fast', function(){
                     jQuery('#after-install-text').fadeIn('fast');
                 });
                 jQuery('#input-blocks').fadeOut('fast');
             });
         }); */

        /* Trigger on enter */
        jQuery('#tiledesk-login-input, #tiledesk-password-input')
            .bind('keydown', function (e) {
                if (e.keyCode == 13) {
                    login_button.trigger('click')
                }
            })
    },
}

jQuery(document).ready(function () {
    TiledeskChatWP.init()
})
