@if (Session::has('flash_notification.message'))

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            var notification_level = '{{ Session::get('flash_notification.level') }}';
            var notification_message = '{{ Session::get('flash_notification.message') }}';

            var notification_type = '';
            switch(notification_level) {
                case 'info':
                    notification_type = 'info';
                    break;
                case 'danger':
                    notification_type = 'error';
                    break;
                case 'warning':
                    notification_type = 'warning';
                    break;
                case 'success':
                    notification_type = 'success';
                    break;
                default:
                    notification_type = 'info';
            }

            portal.notify('alert',{
                title: '',
                message: notification_message,
                type: notification_type
            });
        });

    </script>
@endif