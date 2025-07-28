/* WPMatch Theme Frontend JavaScript */
(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        initWPMatch();
    });
    
    /**
     * Initialize WPMatch functionality
     */
    function initWPMatch() {
        initLoadMoreMatches();
        initLikeProfile();
        initCompatibilityCalculator();
        initMobileMenu();
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
            button.prop('disabled', true).text('Loading...');
            
            // AJAX request
            $.ajax({
                url: wpmatch_theme.ajax_url,
                type: 'POST',
                data: {
                    action: 'wpmatch_action',
                    type: type,
                    limit: limit,
                    offset: currentCount,
                    nonce: wpmatch_theme.nonce
                },
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        // Add new matches
                        $.each(response.data, function(index, match) {
                            var matchHtml = buildMatchHtml(match);
                            matchesContainer.append(matchHtml);
                        });
                        
                        // Animate new matches
                        matchesContainer.find('.wpmatch-match').slice(-response.data.length).hide().fadeIn();
                        
                        // Reset button
                        button.prop('disabled', false).text('Load More Matches');
                        
                        // Hide button if no more matches
                        if (response.data.length < limit) {
                            button.hide();
                        }
                    } else {
                        button.text('No more matches').prop('disabled', true);
                    }
                },
                error: function() {
                    button.prop('disabled', false).text('Load More Matches');
                    alert('Error loading matches. Please try again.');
                }
            });
        });
    }
    
    /**
     * Build match HTML from data
     */
    function buildMatchHtml(match) {
        return `
            <div class="wpmatch-match" data-match-id="${match.id}">
                <h3 class="wpmatch-match-title">${match.title}</h3>
                <div class="wpmatch-match-score">${match.score}% Match</div>
                <p class="wpmatch-match-description">${match.description}</p>
                <div class="wpmatch-match-actions">
                    <button class="btn btn-primary like-profile" data-profile-id="${match.id}">
                        Like
                    </button>
                    <button class="btn btn-secondary">
                        View Profile
                    </button>
                </div>
            </div>
        `;
    }
    
    /**
     * Like profile functionality
     */
    function initLikeProfile() {
        $(document).on('click', '.like-profile', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var profileId = button.data('profile-id');
            var originalText = button.text();
            
            // Check if user is logged in
            if (!$('body').hasClass('logged-in')) {
                alert('Please log in to like profiles.');
                return;
            }
            
            // Show loading state
            button.prop('disabled', true).text('Liking...');
            
            // AJAX request to like profile
            $.ajax({
                url: wpmatch_theme.ajax_url,
                type: 'POST',
                data: {
                    action: 'wpmatch_like_profile',
                    profile_id: profileId,
                    nonce: wpmatch_theme.nonce
                },
                success: function(response) {
                    if (response.success) {
                        button.removeClass('btn-secondary').addClass('btn-success')
                              .text('Liked!').prop('disabled', true);
                        
                        // Show match notification if it's a mutual like
                        if (response.data.mutual_like) {
                            showMatchNotification(response.data.match_name);
                        }
                    } else {
                        button.prop('disabled', false).text(originalText);
                        alert(response.data.message || 'Error liking profile.');
                    }
                },
                error: function() {
                    button.prop('disabled', false).text(originalText);
                    alert('Error liking profile. Please try again.');
                }
            });
        });
    }
    
    /**
     * Show match notification
     */
    function showMatchNotification(matchName) {
        var notification = $(`
            <div class="wpmatch-notification match-notification">
                <div class="notification-content">
                    <h4>🎉 It's a Match!</h4>
                    <p>You and ${matchName} liked each other!</p>
                    <button class="btn btn-primary start-chat">Start Chatting</button>
                    <button class="btn btn-secondary close-notification">Close</button>
                </div>
            </div>
        `);
        
        $('body').append(notification);
        notification.fadeIn();
        
        // Auto-hide after 10 seconds
        setTimeout(function() {
            notification.fadeOut(function() {
                notification.remove();
            });
        }, 10000);
        
        // Manual close
        notification.find('.close-notification').on('click', function() {
            notification.fadeOut(function() {
                notification.remove();
            });
        });
    }
    
    /**
     * Compatibility calculator
     */
    function initCompatibilityCalculator() {
        $('.compatibility-calculator').each(function() {
            var calculator = $(this);
            var form = calculator.find('form');
            
            form.on('submit', function(e) {
                e.preventDefault();
                
                var formData = form.serialize();
                var submitButton = form.find('button[type="submit"]');
                var originalText = submitButton.text();
                
                submitButton.prop('disabled', true).text('Calculating...');
                
                $.ajax({
                    url: wpmatch_theme.ajax_url,
                    type: 'POST',
                    data: formData + '&action=wpmatch_calculate_compatibility&nonce=' + wpmatch_theme.nonce,
                    success: function(response) {
                        if (response.success) {
                            calculator.find('.compatibility-result').html(response.data.html).show();
                        } else {
                            alert(response.data.message || 'Error calculating compatibility.');
                        }
                        submitButton.prop('disabled', false).text(originalText);
                    },
                    error: function() {
                        submitButton.prop('disabled', false).text(originalText);
                        alert('Error calculating compatibility. Please try again.');
                    }
                });
            });
        });
    }
    
    /**
     * Mobile menu functionality
     */
    function initMobileMenu() {
        // Add mobile menu toggle if it doesn't exist
        if ($('.mobile-menu-toggle').length === 0) {
            $('.site-branding').append('<button class="mobile-menu-toggle" aria-label="Toggle menu">☰</button>');
        }
        
        $('.mobile-menu-toggle').on('click', function() {
            var navigation = $('.main-navigation');
            navigation.toggleClass('mobile-menu-open');
            $(this).attr('aria-expanded', navigation.hasClass('mobile-menu-open'));
        });
        
        // Close mobile menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.main-navigation, .mobile-menu-toggle').length) {
                $('.main-navigation').removeClass('mobile-menu-open');
                $('.mobile-menu-toggle').attr('aria-expanded', false);
            }
        });
    }
    
    /**
     * Smooth scrolling for anchor links
     */
    $('a[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
                return false;
            }
        }
    });
    
})(jQuery);