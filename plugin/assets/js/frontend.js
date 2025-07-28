/* WPMatch Plugin Frontend JavaScript */
(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        initWPMatchPlugin();
    });
    
    /**
     * Initialize WPMatch plugin functionality
     */
    function initWPMatchPlugin() {
        initLoadMoreMatches();
        initMatchActions();
        initShortcodeInteractions();
    }
    
    /**
     * Load more matches functionality
     */
    function initLoadMoreMatches() {
        $('.wpmatch-load-more').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var container = button.closest('.wpmatch-container');
            var matchesContainer = container.find('.wpmatch-matches');
            var type = container.data('type') || 'default';
            var limit = container.data('limit') || 5;
            var currentCount = matchesContainer.find('.wpmatch-match').length;
            
            // Show loading state
            button.prop('disabled', true).html('Loading... <span class="wpmatch-loading"></span>');
            
            // AJAX request
            $.ajax({
                url: wpmatch_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'wpmatch_action',
                    type: type,
                    limit: limit,
                    offset: currentCount,
                    nonce: wpmatch_ajax.nonce
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        // Add new matches
                        $.each(response.data, function(index, match) {
                            var matchHtml = buildMatchHtml(match);
                            matchesContainer.append(matchHtml);
                        });
                        
                        // Animate new matches
                        matchesContainer.find('.wpmatch-match').slice(-response.data.length).hide().fadeIn(300);
                        
                        // Reset button
                        button.prop('disabled', false).text('Load More Matches');
                        
                        // Hide button if no more matches
                        if (response.data.length < limit) {
                            button.fadeOut();
                        }
                    } else {
                        button.text('No more matches').prop('disabled', true);
                        setTimeout(function() {
                            button.fadeOut();
                        }, 2000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('WPMatch Error:', error);
                    button.prop('disabled', false).text('Load More Matches');
                    showNotice('Error loading matches. Please try again.', 'error');
                }
            });
        });
    }
    
    /**
     * Build match HTML from data
     */
    function buildMatchHtml(match) {
        var actionsHtml = '';
        if (typeof match.actions !== 'undefined') {
            actionsHtml = '<div class="wpmatch-match-actions">' + match.actions + '</div>';
        } else {
            actionsHtml = `
                <div class="wpmatch-match-actions">
                    <button class="btn btn-primary wpmatch-like" data-match-id="${match.id}">
                        Like
                    </button>
                    <button class="btn btn-secondary wpmatch-view" data-match-id="${match.id}">
                        View
                    </button>
                </div>
            `;
        }
        
        return `
            <div class="wpmatch-match" data-match-id="${match.id}">
                <h3 class="wpmatch-match-title">${escapeHtml(match.title)}</h3>
                <div class="wpmatch-match-score">${match.score}% Match</div>
                <p class="wpmatch-match-description">${escapeHtml(match.description)}</p>
                ${actionsHtml}
            </div>
        `;
    }
    
    /**
     * Initialize match actions (like, view, etc.)
     */
    function initMatchActions() {
        // Like button
        $(document).on('click', '.wpmatch-like', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var matchId = button.data('match-id');
            var originalText = button.text();
            
            // Show loading state
            button.prop('disabled', true).text('Liking...');
            
            $.ajax({
                url: wpmatch_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'wpmatch_like_match',
                    match_id: matchId,
                    nonce: wpmatch_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        button.removeClass('btn-primary').addClass('btn-success')
                              .text('Liked!').prop('disabled', true);
                        
                        // Show success message
                        if (response.data.message) {
                            showNotice(response.data.message, 'success');
                        }
                        
                        // Handle mutual match
                        if (response.data.mutual_match) {
                            showMutualMatchModal(response.data.match_name);
                        }
                    } else {
                        button.prop('disabled', false).text(originalText);
                        showNotice(response.data.message || 'Error liking match.', 'error');
                    }
                },
                error: function() {
                    button.prop('disabled', false).text(originalText);
                    showNotice('Error liking match. Please try again.', 'error');
                }
            });
        });
        
        // View button
        $(document).on('click', '.wpmatch-view', function(e) {
            e.preventDefault();
            
            var matchId = $(this).data('match-id');
            // This could open a modal or redirect to a profile page
            // For now, we'll just trigger an event that themes can listen to
            $(document).trigger('wpmatch:view-match', [matchId]);
        });
    }
    
    /**
     * Initialize shortcode interactions
     */
    function initShortcodeInteractions() {
        // Filter functionality if present
        $('.wpmatch-filter').on('change', function() {
            var container = $(this).closest('.wpmatch-container');
            var filterValue = $(this).val();
            
            // Hide/show matches based on filter
            container.find('.wpmatch-match').each(function() {
                var match = $(this);
                var matchType = match.data('match-type') || 'default';
                
                if (filterValue === 'all' || filterValue === matchType) {
                    match.show();
                } else {
                    match.hide();
                }
            });
        });
        
        // Sort functionality if present
        $('.wpmatch-sort').on('change', function() {
            var container = $(this).closest('.wpmatch-container');
            var sortValue = $(this).val();
            var matchesContainer = container.find('.wpmatch-matches');
            var matches = matchesContainer.find('.wpmatch-match').get();
            
            matches.sort(function(a, b) {
                var aValue, bValue;
                
                switch (sortValue) {
                    case 'score':
                        aValue = parseFloat($(a).find('.wpmatch-match-score').text());
                        bValue = parseFloat($(b).find('.wpmatch-match-score').text());
                        return bValue - aValue; // Descending
                    case 'name':
                        aValue = $(a).find('.wpmatch-match-title').text().toLowerCase();
                        bValue = $(b).find('.wpmatch-match-title').text().toLowerCase();
                        return aValue.localeCompare(bValue); // Ascending
                    default:
                        return 0;
                }
            });
            
            // Re-append sorted matches
            $.each(matches, function(index, match) {
                matchesContainer.append(match);
            });
        });
    }
    
    /**
     * Show mutual match modal
     */
    function showMutualMatchModal(matchName) {
        var modal = $(`
            <div class="wpmatch-modal-overlay">
                <div class="wpmatch-modal">
                    <div class="wpmatch-modal-content">
                        <h3>🎉 It's a Match!</h3>
                        <p>You and <strong>${escapeHtml(matchName)}</strong> liked each other!</p>
                        <div class="wpmatch-modal-actions">
                            <button class="btn btn-primary wpmatch-start-chat">Start Chatting</button>
                            <button class="btn btn-secondary wpmatch-close-modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `);
        
        // Add modal styles if not already present
        if (!$('#wpmatch-modal-styles').length) {
            $('head').append(`
                <style id="wpmatch-modal-styles">
                    .wpmatch-modal-overlay {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        z-index: 999999;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    .wpmatch-modal {
                        background: #fff;
                        border-radius: 8px;
                        max-width: 400px;
                        width: 90%;
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
                    }
                    .wpmatch-modal-content {
                        padding: 2rem;
                        text-align: center;
                    }
                    .wpmatch-modal-content h3 {
                        margin: 0 0 1rem 0;
                        color: #28a745;
                        font-size: 1.5rem;
                    }
                    .wpmatch-modal-content p {
                        margin: 0 0 1.5rem 0;
                        color: #555;
                    }
                    .wpmatch-modal-actions {
                        display: flex;
                        gap: 1rem;
                        justify-content: center;
                        flex-wrap: wrap;
                    }
                </style>
            `);
        }
        
        $('body').append(modal);
        modal.fadeIn(300);
        
        // Auto-hide after 10 seconds
        setTimeout(function() {
            modal.fadeOut(300, function() {
                modal.remove();
            });
        }, 10000);
        
        // Manual close
        modal.find('.wpmatch-close-modal').on('click', function() {
            modal.fadeOut(300, function() {
                modal.remove();
            });
        });
        
        // Start chat action
        modal.find('.wpmatch-start-chat').on('click', function() {
            // Trigger event that themes/other plugins can listen to
            $(document).trigger('wpmatch:start-chat', [matchName]);
            modal.fadeOut(300, function() {
                modal.remove();
            });
        });
        
        // Close on overlay click
        modal.on('click', function(e) {
            if (e.target === modal[0]) {
                modal.fadeOut(300, function() {
                    modal.remove();
                });
            }
        });
    }
    
    /**
     * Show notice message
     */
    function showNotice(message, type) {
        type = type || 'info';
        
        var notice = $(`
            <div class="wpmatch-notice wpmatch-notice-${type}">
                <p>${escapeHtml(message)}</p>
                <button class="wpmatch-notice-dismiss">&times;</button>
            </div>
        `);
        
        // Add notice styles if not already present
        if (!$('#wpmatch-notice-styles').length) {
            $('head').append(`
                <style id="wpmatch-notice-styles">
                    .wpmatch-notice {
                        position: fixed;
                        top: 32px;
                        right: 20px;
                        background: #fff;
                        border-left: 4px solid #0073aa;
                        padding: 12px 40px 12px 12px;
                        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
                        z-index: 999998;
                        max-width: 400px;
                        border-radius: 0 4px 4px 0;
                        position: relative;
                    }
                    .wpmatch-notice.wpmatch-notice-error {
                        border-left-color: #d63638;
                    }
                    .wpmatch-notice.wpmatch-notice-success {
                        border-left-color: #00a32a;
                    }
                    .wpmatch-notice.wpmatch-notice-warning {
                        border-left-color: #dba617;
                    }
                    .wpmatch-notice p {
                        margin: 0;
                        color: #23282d;
                        font-size: 14px;
                    }
                    .wpmatch-notice-dismiss {
                        position: absolute;
                        top: 8px;
                        right: 8px;
                        background: none;
                        border: none;
                        font-size: 18px;
                        cursor: pointer;
                        color: #666;
                    }
                    .wpmatch-notice-dismiss:hover {
                        color: #000;
                    }
                </style>
            `);
        }
        
        $('body').append(notice);
        notice.fadeIn(300);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            notice.fadeOut(300, function() {
                notice.remove();
            });
        }, 5000);
        
        // Manual dismiss
        notice.find('.wpmatch-notice-dismiss').on('click', function() {
            notice.fadeOut(300, function() {
                notice.remove();
            });
        });
    }
    
    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        
        return text.replace(/[&<>"']/g, function(m) {
            return map[m];
        });
    }
    
})(jQuery);