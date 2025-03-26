(function ($, Drupal) {
    Drupal.behaviors.apiModal = {
      attach: function (context, settings) {
        // Click tile to open modal
        once('api-modal-init', '.api-tile', context).forEach(function (el) {
          $(el).on('click', function () {
            const modalId = $(el).data('modal-id');
            $('#' + modalId).fadeIn(200);
          });
        });
  
        // Close modal on X click
        once('api-modal-close', '.api-close', context).forEach(function (el) {
          $(el).on('click', function () {
            $(el).closest('.api-modal').fadeOut(200);
          });
        });
  
        // Click outside modal to close
        once('api-modal-bg-close', '.api-modal', context).forEach(function (el) {
          $(el).on('click', function (e) {
            if ($(e.target).hasClass('api-modal')) {
              $(el).fadeOut(200);
            }
          });
        });
      }
    };
  })(jQuery, Drupal);
  