/**
 * WPFIA Currency Switcher - Frontend JavaScript
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const $floating = $('#wpfia-cs-floating');
        const $toggle   = $('#wpfia-cs-toggle');
        const $menu     = $('#wpfia-cs-menu');

        // Toggle Floating Menu
        $toggle.on('click', function(e) {
            e.stopPropagation();
            const isOpen = $menu.is(':visible');
            if (isOpen) {
                $menu.fadeOut(150);
                $toggle.attr('aria-expanded', 'false');
            } else {
                $menu.fadeIn(150);
                $toggle.attr('aria-expanded', 'true');
            }
        });

        // Close when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#wpfia-cs-floating').length) {
                $menu.fadeOut(150);
                $toggle.attr('aria-expanded', 'false');
            }
            if (!$(e.target).closest('#wpfia-cs-topbar').length) {
                $('#wpfia-cs-topbar-menu').fadeOut(150);
            }
        });

        // Toggle Topbar Menu
        $(document).on('click', '#wpfia-cs-topbar-btn', function(e) {
            e.stopPropagation();
            $('#wpfia-cs-topbar-menu').fadeToggle(150);
        });

        // Switch via Floating Item
        $(document).on('click', '.wpfia-cs-currency-item', function() {
            const currency = $(this).data('currency');
            if (!currency || $(this).hasClass('active')) return;
            switchCurrency(currency);
        });

        // Switch via Shortcode Select
        $(document).on('change', '.wpfia-cs-select', function() {
            const currency = $(this).val();
            if (currency) {
                switchCurrency(currency);
            }
        });

        function switchCurrency(currency) {
            if (typeof WPFIA_CS_Data === 'undefined') return;

            // Loading visual
            $toggle.css('opacity', '0.6').prop('disabled', true);

            $.post(WPFIA_CS_Data.ajax_url, {
                action:   'wpfia_cs_switch_currency',
                nonce:    WPFIA_CS_Data.nonce,
                currency: currency,
            }, function(res) {
                if (res && res.success) {
                    window.location.reload();
                } else {
                    $toggle.css('opacity', '1').prop('disabled', false);
                    alert(res.data?.message || 'Failed to switch currency.');
                }
            }).fail(function() {
                $toggle.css('opacity', '1').prop('disabled', false);
                alert('Connection error. Please try again.');
            });
        }
    });
})(jQuery);
