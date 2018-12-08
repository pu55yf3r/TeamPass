<?php
/**
 * Teampass - a collaborative passwords manager.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @category  Teampass
 *
 * @author    Nils Laumaillé <nils@teampass.net>
 * @copyright 2009-2018 Nils Laumaillé
 * @license   https://spdx.org/licenses/GPL-3.0-only.html#licenseText GPL-3.0
 *
 * @version   GIT: <git_id>
 *
 * @see      http://www.teampass.net
 */
?>


<script type="text/javascript">
var requestRunning = false;

/**
 * ADMIN
 */
// <- PREPARE TOGGLES
$('.toggle').toggles({
    drag: true,
    click: true,
    text: {
        on: '<?php echo langHdl('yes'); ?>',
        off: '<?php echo langHdl('no'); ?>'
    },
    on: true,
    animate: 250,
    easing: 'swing',
    width: 50,
    height: 20,
    type: 'compact'
});
$('.toggle').on('toggle', function(e, active) {
    if (active) {
        $("#"+e.target.id+"_input").val(1);
        if (e.target.id == "allow_print") {
            $("#roles_allowed_to_print_select").prop("disabled", false);
        }
        if (e.target.id == "anyone_can_modify") {
            $("#form-item-row-modify").removeClass('hidden');
        }
        if (e.target.id == "restricted_to") {
            $("#form-item-row-restricted").removeClass('hidden');
        }
    } else {
        $("#"+e.target.id+"_input").val(0);
        if (e.target.id == "allow_print") {
            $("#roles_allowed_to_print_select").prop("disabled", true);
        }
        if (e.target.id == "anyone_can_modify") {
            $("#form-item-row-modify").addClass('hidden');
        }
        if (e.target.id == "restricted_to") {
            $("#form-item-row-restricted").addClass('hidden');
        }
    }

    var data = {
        "field" : e.target.id,
        "value" : $("#"+e.target.id+"_input").val(),
    }
console.log(data)
    // Store in DB   
    $.post(
        "sources/admin.queries.php",
        {
            type    : "save_option_change",
            data    : prepareExchangedData(JSON.stringify(data), "encode", "<?php echo $_SESSION['key']; ?>"),
            key     : "<?php echo $_SESSION['key']; ?>"
        },
        function(data) {
            // Handle server answer
            try {
                data = prepareExchangedData(data , "decode", "<?php echo $_SESSION['key']; ?>");
            }
            catch (e) {
                // error
                showAlertify(
                    '<?php echo langHdl('server_answer_error').'<br />'.langHdl('server_returned_data').':<br />'; ?>' + data.error,
                    0,
                    'top-right',
                    'error'
                );
                return false;
            }
            console.log(data)
            if (data.error === false) {
                showAlertify(
                    '<?php echo langHdl('saved'); ?>',
                    2,
                    'top-bottom',
                    'success'
                );
            }
        }
    );
});
// .-> END. TOGGLES

// <- PREPARE SELECT2
$('.select2').select2({
    language: '<?php echo $_SESSION['user_language_code']; ?>'
});

/**
 */
$(document).on('change', '.form-control-sm', function() {
    var field = $(this).attr('id'),
        value = $(this).val();

    if (field === '') return false;

    // prevent launch of similar query in case of doubleclick
    if (requestRunning === true) {
        return false;
    }
    requestRunning = true;

    var data = {
        "field" : field,
        "value" : value,
    }
    console.log(data)
    
    // Store in DB   
    $.post(
        "sources/admin.queries.php",
        {
            type    : "save_option_change",
            data    : prepareExchangedData(JSON.stringify(data), "encode", "<?php echo $_SESSION['key']; ?>"),
            key     : "<?php echo $_SESSION['key']; ?>"
        },
        function(data) {
            // Handle server answer
            try {
                data = prepareExchangedData(data , "decode", "<?php echo $_SESSION['key']; ?>");
            }
            catch (e) {
                // error
                showAlertify(
                    '<?php echo langHdl('server_answer_error').'<br />'.langHdl('server_returned_data').':<br />'; ?>' + data.error,
                    0,
                    'top-right',
                    'error'
                );
                return false;
            }
            console.log(data)
            if (data.error === false) {
                showAlertify(
                    '<?php echo langHdl('saved'); ?>',
                    2,
                    'top-bottom',
                    'success'
                );

                // Force page reload in case of encryptClientServer
                if (field === 'ldap_type') {
                    $('.tr-ldap').each(function() {
                        if ($(this).hasClass('tr-' + value) === false) {
                            $(this).addClass('hidden');
                        } else {
                            $(this).removeClass('hidden');
                        }
                    });
                }
            }
            requestRunning = false;
        }
    );
});



</script>
