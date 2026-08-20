/**
 * assets/js/main.js — Think & Tinker Core JavaScript
 * AJAX wrapper, toasts, modals, dropdowns, tabs, utilities.
 * Loaded on every page. jQuery 3.x required (CDN in header.php).
 */
'use strict';

const TT = window.TT || {};

// ============================================================
// CONFIG
// ============================================================
TT.config = {
    apiBase: '',       // Set in header template: '/api' or '/hub/api' depending on context
    csrfToken: '',     // Set in header template from PHP
    pollInterval: 30000, // 30s notification polling
};

// ============================================================
// AJAX WRAPPER
// ============================================================
/**
 * Make an API call with automatic CSRF, JSON parsing, and error handling.
 *
 * @param {string} controller - Controller filename, e.g. 'AuthController.php'
 * @param {string|object} data - POST data (string action or object with action + fields)
 * @param {object} options - Optional: { method, onSuccess, onError, loader }
 * @returns {Promise<object>}
 *
 * Usage:
 *   TT.api('AuthController.php', { action: 'login', email, password })
 *     .then(r => { ... })
 *     .catch(e => { ... });
 *
 *   // Shorthand for action-only:
 *   TT.api('DashboardController.php', 'get_dashboard')
 */
TT.api = function(controller, data, options = {}) {
    // If data is a string, treat it as action name
    if (typeof data === 'string') {
        data = { action: data };
    }

    const url = TT.config.apiBase + '/' + controller;
    const method = options.method || 'POST';

    // Handle FormData (file uploads) BEFORE touching . _token — FormData
    // is not a plain object, so assigning data._token would never reach PHP.
    let ajaxOptions;
    if (data instanceof FormData) {
        if (!data.has('_token')) {
            data.append('_token', TT.config.csrfToken);
        }
        if (!data.has('action') && options.action) {
            data.append('action', options.action);
        }
        ajaxOptions = {
            url: url,
            method: method,
            data: data,
            processData: false,
            contentType: false,
            dataType: 'json',
        };
    } else {
        if (typeof data === 'object' && data && !data._token) {
            data._token = TT.config.csrfToken;
        }
        ajaxOptions = {
            url: url,
            method: method,
            data: data,
            dataType: 'json',
        };
    }

    // Show loader if specified
    if (options.loader) {
        $(options.loader).addClass('loading');
    }

    return $.ajax(ajaxOptions)
        .done(function(response) {
            if (options.loader) {
                $(options.loader).removeClass('loading');
            }
            if (response.success === false && response.message) {
                if (!options.silent) {
                    TT.toast(response.message, 'error');
                }
            }
            if (options.onSuccess) {
                options.onSuccess(response);
            }
        })
        .fail(function(xhr) {
            if (options.loader) {
                $(options.loader).removeClass('loading');
            }

            let msg = 'Something went wrong. Please try again.';

            if (xhr.status === 401) {
                msg = 'Your session has expired. Redirecting to login...';
                setTimeout(() => window.location.reload(), 2000);
            } else if (xhr.status === 403) {
                msg = 'Access denied.';
            } else if (xhr.status === 0) {
                msg = 'Network error. Please check your connection.';
            }

            try {
                const resp = JSON.parse(xhr.responseText);
                if (resp.message) msg = resp.message;
            } catch(e) {}

            if (!options.silent) {
                TT.toast(msg, 'error');
            }
            if (options.onError) {
                options.onError(xhr, msg);
            }
        });
};

/**
 * GET request shorthand (for fetching data).
 */
TT.get = function(controller, params = {}) {
    const qs = $.param(params);
    const url = TT.config.apiBase + '/' + controller + (qs ? '?' + qs : '');
    return $.getJSON(url);
};

// ============================================================
// TOAST NOTIFICATIONS
// ============================================================
TT.toast = function(message, type = 'info', duration = 4000) {
    let $container = $('.toast-container');
    if (!$container.length) {
        $container = $('<div class="toast-container"></div>').appendTo('body');
    }

    const icons = {
        success: '✓',
        error: '✕',
        warning: '⚠',
        info: 'ℹ',
    };

    const $toast = $(`
        <div class="toast toast-${type}">
            <span class="toast-icon">${icons[type] || icons.info}</span>
            <span class="toast-msg">${TT.escHtml(message)}</span>
            <span class="toast-close">&times;</span>
        </div>
    `);

    $container.append($toast);

    // Close on click
    $toast.find('.toast-close').on('click', function() {
        $toast.addClass('removing');
        setTimeout(() => $toast.remove(), 250);
    });

    // Auto-dismiss
    if (duration > 0) {
        setTimeout(function() {
            if ($toast.parent().length) {
                $toast.addClass('removing');
                setTimeout(() => $toast.remove(), 250);
            }
        }, duration);
    }

    return $toast;
};

// ============================================================
// MODALS
// ============================================================
TT.modal = {
    open: function(selector) {
        const $overlay = $(selector);
        $overlay.addClass('active');
        $('body').css('overflow', 'hidden');
    },
    close: function(selector) {
        const $overlay = selector ? $(selector) : $('.modal-overlay.active');
        $overlay.removeClass('active');
        $('body').css('overflow', '');
    },
    confirm: function(message, onConfirm, title = 'Confirm') {
        const id = 'modal-confirm-' + Date.now();
        const html = `
            <div class="modal-overlay" id="${id}">
                <div class="modal" style="max-width: 420px;">
                    <div class="modal-header">
                        <h3>${TT.escHtml(title)}</h3>
                        <button type="button" class="modal-close" data-close aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>${TT.escHtml(message)}</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-ghost" data-close>Cancel</button>
                        <button class="btn btn-primary" data-confirm>Confirm</button>
                    </div>
                </div>
            </div>
        `;
        $('body').append(html);
        const $modal = $('#' + id);
        TT.modal.open('#' + id);

        $modal.find('[data-close]').on('click', function() {
            TT.modal.close('#' + id);
            setTimeout(() => $modal.remove(), 300);
        });
        $modal.find('[data-confirm]').on('click', function() {
            TT.modal.close('#' + id);
            setTimeout(() => $modal.remove(), 300);
            if (onConfirm) onConfirm();
        });
    }
};

// ============================================================
// TABS
// ============================================================
TT.initTabs = function(container) {
    const $container = $(container || document);
    $container.find('.tabs .tab').on('click', function() {
        const target = $(this).data('tab');
        const $parent = $(this).closest('.tabs');

        $parent.find('.tab').removeClass('active');
        $(this).addClass('active');

        $parent.nextAll('.tab-content').removeClass('active');
        $('#' + target).addClass('active');
    });
};

// ============================================================
// DROPDOWNS
// ============================================================
TT.initDropdowns = function() {
    $(document).on('click', '[data-dropdown]', function(e) {
        e.stopPropagation();
        const $dd = $(this).closest('.dropdown');
        const wasOpen = $dd.hasClass('open');
        $('.dropdown').removeClass('open'); // close all
        if (!wasOpen) $dd.addClass('open');
    });

    $(document).on('click', function() {
        $('.dropdown').removeClass('open');
    });
};

// ============================================================
// FORM HELPERS
// ============================================================
/**
 * Serialize a form to an object.
 */
TT.formData = function(formSelector) {
    const data = {};
    $(formSelector).serializeArray().forEach(function(item) {
        data[item.name] = item.value;
    });
    return data;
};

/**
 * Submit a form via AJAX.
 */
TT.submitForm = function(formSelector, controller, options = {}) {
    const $form = $(formSelector);
    const $btn = $form.find('[type="submit"]');

    // Disable button
    $btn.prop('disabled', true).data('original-text', $btn.text()).text('Saving...');

    // Check for file inputs
    let data;
    if ($form.find('input[type="file"]').length) {
        data = new FormData($form[0]);
    } else {
        data = TT.formData(formSelector);
    }

    return TT.api(controller, data, options)
        .always(function() {
            $btn.prop('disabled', false).text($btn.data('original-text'));
        });
};

/**
 * Clear all form fields.
 */
TT.clearForm = function(formSelector) {
    $(formSelector)[0].reset();
    $(formSelector).find('.form-error').remove();
    $(formSelector).find('.error').removeClass('error');
};

// ============================================================
// PAGINATION BUILDER
// ============================================================
TT.buildPagination = function($container, currentPage, totalPages, onPageClick) {
    $container.empty();
    if (totalPages <= 1) return;

    const addBtn = (label, page, active = false, disabled = false) => {
        const $btn = $(`<span class="page-btn${active ? ' active' : ''}" data-page="${page}">${label}</span>`);
        if (disabled) $btn.prop('disabled', true).css('opacity', '0.3');
        else $btn.on('click', () => onPageClick(page));
        $container.append($btn);
    };

    addBtn('‹', currentPage - 1, false, currentPage <= 1);

    for (let i = 1; i <= totalPages; i++) {
        if (totalPages > 7 && i > 2 && i < totalPages - 1 && Math.abs(i - currentPage) > 1) {
            if (i === 3 || i === totalPages - 2) addBtn('…', i, false, true);
            continue;
        }
        addBtn(i, i, i === currentPage);
    }

    addBtn('›', currentPage + 1, false, currentPage >= totalPages);
};

// ============================================================
// UTILITIES
// ============================================================
/**
 * Escape HTML to prevent XSS.
 */
TT.escHtml = function(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;').replace(/'/g,'&#39;');
};

/**
 * Format number as Naira.
 */
TT.formatNaira = function(amount) {
    amount = parseFloat(amount) || 0;
    if (amount % 1 === 0) {
        return '₦' + amount.toLocaleString('en-NG');
    }
    return '₦' + amount.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

/**
 * Time ago from a date string.
 */
TT.timeAgo = function(dateStr) {
    const now = new Date();
    const then = new Date(dateStr);
    const diff = Math.floor((now - then) / 1000);

    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
    return then.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
};

/**
 * Debounce helper.
 */
TT.debounce = function(fn, delay = 300) {
    let timer;
    return function(...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
};

/**
 * Copy text to clipboard.
 */
TT.copyText = function(text) {
    navigator.clipboard.writeText(text).then(() => {
        TT.toast('Copied to clipboard', 'success', 2000);
    });
};

/**
 * Get initials from a name (for avatars).
 */
TT.initials = function(name) {
    if (!name) return '?';
    return name.split(' ').map(w => w[0]).join('').toUpperCase().substring(0, 2);
};

// ============================================================
// INIT ON READY
// ============================================================
$(function() {
    TT.initDropdowns();
    TT.initTabs();

    // Close modal on overlay click
    $(document).on('click', '.modal-overlay', function(e) {
        if (e.target === this) TT.modal.close(this);
    });

    // Close modal on close button
    $(document).on('click', '.modal-close, [data-modal-close]', function() {
        TT.modal.close($(this).closest('.modal-overlay'));
    });

    // Close modal on Escape
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') TT.modal.close();
    });

    // Auto-submit search on input (debounced)
    $(document).on('input', '.search-bar input[data-search]', TT.debounce(function() {
        const callback = $(this).data('search');
        if (window[callback]) window[callback]($(this).val());
    }));

    // Button loading state helper
    $(document).on('click', '.btn[data-loading-text]', function() {
        const $btn = $(this);
        if ($btn.data('loading')) return;
        $btn.data('loading', true).data('original-text', $btn.html())
            .html('<span class="spinner spinner-sm" style="border-top-color:#FFF;"></span>');
    });
});

window.TT = TT;
