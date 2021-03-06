jQuery(function($) {

    var modal;

    $('#clearCache').on('click', function(e) {
        e.preventDefault();

        if (!modal) {
            modal = UIkit.modal('#modal-clearcache');

            modal.element.find('form').on('submit', function(e) {
                e.preventDefault();

                $.post($(this).attr('action'), $(this).serialize(),function(data) {
                    UIkit.notify(data.message, 'success');
                }).fail(function() {
                    UIkit.notify('Clearing cache failed.', 'danger');
                }).always(function() {
                    modal.hide();
                });
            });
        }

        modal.show();
    });

});