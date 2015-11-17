/**
 * Get/define the portal namespace for this file
 */
var portal = portal || {};


/**
 * Define the portal notification message using sweet alert
 *
 * @param type
 * @param options
 */
portal.notify = function(type, options){

    switch(type){
        case "confirm":

            var defaults = {
                title: 'Are you sure?',
                text: 'Please confirm',
                type: 'warning',
                showCancelButton: true,
                confirmButtontext: 'Yes',
                cancelButtonText: 'Cancel',
                closeOnConfirm: false,
                closeOnCancel: false,
                'callback': function(isConfirm){
                    if(isConfirm) {
                        swal("Confirmed!", "You have been confirmed.", 'success');
                    }else{
                        swal('Cancelled!', 'You have been cancelled.', 'error');
                    }
                }
            };

            options = jQuery.extend(defaults, options);
            swal(options , options.callback);

            break;
        case "alert":

            var defaults = {
                title: 'Alert',
                message: 'Alert',
                type: 'success',
            };

            options = jQuery.extend(defaults, options);

            swal(options.title, options.message, options.type);

            break;
    }

}


