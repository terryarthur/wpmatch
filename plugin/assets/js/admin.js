/* WPMatch Plugin Admin JavaScript */
(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        initWPMatchAdmin();
    });
    
    /**
     * Initialize WPMatch admin functionality
     */
    function initWPMatchAdmin() {
        initTabs();
        initBulkActions();
        initAjaxForms();
        initStatisticsRefresh();
        initSettingsValidation();
    }
    
    /**
     * Initialize tab functionality
     */
    function initTabs() {
        $('.wpmatch-tabs a').on('click', function(e) {
            e.preventDefault();
            
            var tab = $(this);
            var target = tab.attr('href');
            
            // Update active tab
            tab.closest('ul').find('a').removeClass('active');
            tab.addClass('active');
            
            // Show/hide tab content
            $('.wpmatch-tab-content').hide();
            $(target).show();
            
            // Save active tab in localStorage
            localStorage.setItem('wpmatch_active_tab', target);
        });
        
        // Restore active tab from localStorage
        var activeTab = localStorage.getItem('wpmatch_active_tab');
        if (activeTab && $(activeTab).length) {
            $('.wpmatch-tabs a[href="' + activeTab + '"]').click();
        } else {
            $('.wpmatch-tabs a:first').click();
        }
    }
    
    /**
     * Initialize bulk actions
     */
    function initBulkActions() {
        // Select all checkbox
        $('.wpmatch-select-all').on('change', function() {
            var checked = $(this).is(':checked');
            $('.wpmatch-select-item').prop('checked', checked);
            updateBulkActionButton();
        });
        
        // Individual checkboxes
        $(document).on('change', '.wpmatch-select-item', function() {
            updateBulkActionButton();
            
            // Update select all checkbox
            var total = $('.wpmatch-select-item').length;
            var checked = $('.wpmatch-select-item:checked').length;
            
            $('.wpmatch-select-all').prop('checked', checked === total);
        });
        
        // Bulk action form submission
        $('.wpmatch-bulk-action-form').on('submit', function(e) {
            var action = $(this).find('select[name="action"]').val();
            var selected = $('.wpmatch-select-item:checked').length;
            
            if (action === '-1') {
                e.preventDefault();
                alert('Please select an action.');
                return;
            }
            
            if (selected === 0) {
                e.preventDefault();
                alert('Please select items to perform the action on.');
                return;
            }
            
            // Confirm destructive actions
            if (action === 'delete' || action === 'reject') {
                if (!confirm('Are you sure you want to ' + action + ' ' + selected + ' item(s)?')) {
                    e.preventDefault();
                }
            }
        });
    }
    
    /**
     * Update bulk action button state
     */
    function updateBulkActionButton() {
        var selected = $('.wpmatch-select-item:checked').length;
        var button = $('.wpmatch-bulk-action-submit');
        
        if (selected > 0) {
            button.prop('disabled', false).text('Apply to ' + selected + ' item(s)');
        } else {
            button.prop('disabled', true).text('Apply');
        }
    }
    
    /**
     * Initialize AJAX forms
     */
    function initAjaxForms() {
        $('.wpmatch-ajax-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitButton = form.find('button[type="submit"]');
            var originalText = submitButton.text();
            
            // Show loading state
            submitButton.prop('disabled', true).html(originalText + ' <span class="wpmatch-loading"></span>');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        showAdminNotice(response.data.message || 'Action completed successfully.', 'success');
                        
                        // Refresh page if requested
                        if (response.data.refresh) {
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }
                        
                        // Update form if data provided
                        if (response.data.form_data) {
                            updateFormData(form, response.data.form_data);
                        }
                    } else {
                        showAdminNotice(response.data.message || 'An error occurred.', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('WPMatch Admin Error:', error);
                    showAdminNotice('An error occurred. Please try again.', 'error');
                },
                complete: function() {
                    submitButton.prop('disabled', false).text(originalText);
                }
            });
        });
    }
    
    /**
     * Initialize statistics refresh
     */
    function initStatisticsRefresh() {
        $('.wpmatch-refresh-stats').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var originalText = button.text();
            
            button.prop('disabled', true).html('Refreshing... <span class="wpmatch-loading"></span>');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wpmatch_refresh_stats',
                    nonce: wpmatch_admin.nonce
                },
                success: function(response) {
                    if (response.success && response.data.stats) {
                        // Update statistics
                        $.each(response.data.stats, function(key, value) {
                            $('.wpmatch-stat-' + key + ' .wpmatch-stat-number').text(value);
                        });
                        
                        showAdminNotice('Statistics refreshed successfully.', 'success');
                    } else {
                        showAdminNotice('Error refreshing statistics.', 'error');
                    }
                },
                error: function() {
                    showAdminNotice('Error refreshing statistics.', 'error');
                },
                complete: function() {
                    button.prop('disabled', false).text(originalText);
                }
            });
        });
    }
    
    /**
     * Initialize settings validation
     */
    function initSettingsValidation() {
        // Real-time validation for email fields
        $('input[type="email"]').on('blur', function() {
            var email = $(this).val();
            var field = $(this).closest('.wpmatch-form-field');
            
            field.find('.validation-message').remove();
            
            if (email && !isValidEmail(email)) {
                field.append('<div class="validation-message error">Please enter a valid email address.</div>');
            }
        });
        
        // Number field validation
        $('input[type="number"]').on('blur', function() {
            var value = parseInt($(this).val());
            var min = parseInt($(this).attr('min'));
            var max = parseInt($(this).attr('max'));
            var field = $(this).closest('.wpmatch-form-field');
            
            field.find('.validation-message').remove();
            
            if (!isNaN(min) && value < min) {
                field.append('<div class="validation-message error">Value must be at least ' + min + '.</div>');
            } else if (!isNaN(max) && value > max) {
                field.append('<div class="validation-message error">Value must be no more than ' + max + '.</div>');
            }
        });
    }
    
    /**
     * Show admin notice
     */
    function showAdminNotice(message, type) {
        type = type || 'info';
        
        var notice = $(`
            <div class="notice notice-${type} is-dismissible wpmatch-admin-notice">
                <p>${message}</p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Dismiss this notice.</span>
                </button>
            </div>
        `);
        
        // Insert notice after the page title
        if ($('.wrap h1').length) {
            $('.wrap h1').after(notice);
        } else {
            $('.wrap').prepend(notice);
        }
        
        notice.hide().slideDown(300);
        
        // Auto-hide success messages
        if (type === 'success') {
            setTimeout(function() {
                notice.slideUp(300, function() {
                    notice.remove();
                });
            }, 5000);
        }
        
        // Handle dismiss button
        notice.find('.notice-dismiss').on('click', function() {
            notice.slideUp(300, function() {
                notice.remove();
            });
        });
    }
    
    /**
     * Update form data
     */
    function updateFormData(form, data) {
        $.each(data, function(name, value) {
            var field = form.find('[name="' + name + '"]');
            
            if (field.length) {
                if (field.is(':checkbox') || field.is(':radio')) {
                    field.prop('checked', value);
                } else {
                    field.val(value);
                }
            }
        });
    }
    
    /**
     * Validate email address
     */
    function isValidEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    /**
     * Confirmation dialogs for destructive actions
     */
    $(document).on('click', '.wpmatch-confirm-action', function(e) {
        var message = $(this).data('confirm-message') || 'Are you sure?';
        if (!confirm(message)) {
            e.preventDefault();
        }
    });
    
    /**
     * Auto-save settings
     */
    var autoSaveTimeout;
    $('.wpmatch-auto-save input, .wpmatch-auto-save select, .wpmatch-auto-save textarea').on('change', function() {
        clearTimeout(autoSaveTimeout);
        
        autoSaveTimeout = setTimeout(function() {
            $('.wpmatch-auto-save').submit();
        }, 1000);
    });
    
})(jQuery);

// Add validation styles
jQuery(document).ready(function($) {
    if (!$('#wpmatch-admin-validation-styles').length) {
        $('head').append(`
            <style id="wpmatch-admin-validation-styles">
                .validation-message {
                    margin-top: 5px;
                    padding: 5px 8px;
                    border-radius: 3px;
                    font-size: 12px;
                }
                .validation-message.error {
                    background: #ffebee;
                    color: #c62828;
                    border: 1px solid #ef5350;
                }
                .validation-message.success {
                    background: #e8f5e8;
                    color: #2e7d32;
                    border: 1px solid #66bb6a;
                }
                .wpmatch-admin-notice {
                    margin: 15px 0;
                }
            </style>
        `);
    }
});