/**
 * @file
 * Custom JavaScript for Event Theme.
 */

(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.eventTheme = {
    attach: function (context, settings) {
      // Add smooth scrolling to event links
      $('.node--type-event a[href^="#"]', context).once('event-smooth-scroll').on('click', function (e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if (target.length) {
          $('html, body').animate({
            scrollTop: target.offset().top - 100
          }, 800);
        }
      });

      // Add hover effects to event cards
      $('.node--type-event', context).once('event-hover').hover(
        function () {
          $(this).addClass('event-hover');
        },
        function () {
          $(this).removeClass('event-hover');
        }
      );

      // Form validation enhancement
      $('.node-event-form', context).once('event-form-validation').on('submit', function (e) {
        var title = $(this).find('input[name="title[0][value]"]').val();
        var date = $(this).find('input[name="field_event_date[0][value][date]"]').val();

        if (!title || !date) {
          e.preventDefault();
          alert('Please fill in all required fields.');
          return false;
        }
      });

      // Date picker enhancement
      $('input[type="date"]', context).once('event-date-picker').on('change', function () {
        var selectedDate = new Date(this.value);
        var today = new Date();

        if (selectedDate < today) {
          $(this).addClass('date-past');
          if (!$(this).next('.date-warning').length) {
            $(this).after('<div class="date-warning">This date is in the past.</div>');
          }
        } else {
          $(this).removeClass('date-past');
          $(this).next('.date-warning').remove();
        }
      });

      // Add loading states to forms
      $('.node-event-form input[type="submit"]', context).once('event-form-loading').on('click', function () {
        var $form = $(this).closest('form');
        var $submitBtn = $(this);

        if ($form.valid()) {
          $submitBtn.prop('disabled', true).text('Saving...');
          setTimeout(function () {
            $submitBtn.prop('disabled', false).text('Save');
          }, 2000);
        }
      });
    }
  };

  // Add CSS for new classes
  $('<style>')
    .prop('type', 'text/css')
    .html(`
      .event-hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
      }
      .date-past {
        border-color: #dc3545 !important;
      }
      .date-warning {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
      }
    `)
    .appendTo('head');

})(jQuery, Drupal);
