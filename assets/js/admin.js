/**
 * SafeFonts Free Admin JavaScript
 */

(function($) {
    'use strict';

    let SafeFontsAdmin = {
        init: function() {
            this.initUploadForm();
            this.initFontDeletion();
            this.initTabs();
        },

        initUploadForm: function() {
            // Handle form submission (works with both click and Enter key)
            $('#safefonts-upload-form').on('submit', function(e) {
                e.preventDefault();
                SafeFontsAdmin.handleFileUpload(this);
            });

            // Ensure Enter key works on submit button
            $('#safefonts-upload-submit').on('keypress', function(e) {
                if (e.which === 13 || e.keyCode === 13) { // Enter key
                    e.preventDefault();
                    $(this).closest('form').trigger('submit');
                }
            });
        },

        initFontDeletion: function() {
            $(document).on('click', '.safefonts-delete-font', function(e) {
                e.preventDefault();

                if (!confirm(safefontsAjax.strings.confirm_delete)) {
                    return;
                }

                const $button = $(this);
                const fontId = $button.data('font-id');
                const nonce = $button.data('nonce');

                SafeFontsAdmin.deleteFont(fontId, nonce, $button);
            });
        },

        initTabs: function() {
            $('.safefonts-nav-tab').on('click', function(e) {
                e.preventDefault();

                const $tab = $(this);
                const target = $tab.attr('href');

                // Update active tab
                $('.safefonts-nav-tab').removeClass('safefonts-nav-tab-active');
                $tab.addClass('safefonts-nav-tab-active');

                // Show target content
                $('.safefonts-tab-content').hide();
                $(target).show();
            });
        },

        handleFileUpload: function(form) {
            const $form = $(form);
            const $progress = $('#safefonts-upload-progress');
            const $result = $('#safefonts-upload-result');
            const $progressFill = $('.safefonts-progress-fill');
            const $progressText = $('.safefonts-progress-text');

            // Validate required fields
            const fontFamily = $form.find('#font_family').val().trim();
            if (!fontFamily) {
                alert(safefontsAjax.strings.font_family_required);
                return;
            }

            // Show progress
            $progress.show();
            $result.empty();
            $progressFill.css('width', '0%');
            $progressText.text(safefontsAjax.strings.uploading);

            // Prepare form data
            const formData = new FormData(form);
            formData.append('action', 'safefonts_upload_font');

            // Upload with progress
            $.ajax({
                url: safefontsAjax.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percentComplete = (e.loaded / e.total) * 100;
                            $progressFill.css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    $progressFill.css('width', '100%');
                    $progressText.text(safefontsAjax.strings.processing);

                    setTimeout(function() {
                        $progress.hide();
                        SafeFontsAdmin.displayUploadResult(response);

                        if (response.success) {
                            // Reset form
                            $form[0].reset();

                            // Reload page to show new fonts
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    $progress.hide();
                    SafeFontsAdmin.displayUploadResult({
                        success: false,
                        data: safefontsAjax.strings.upload_error + ': ' + error
                    });
                }
            });
        },

        displayUploadResult: function(response) {
            const $result = $('#safefonts-upload-result');
            let html = '';

            if (response.success) {
                html += '<div class="notice notice-success"><p><strong>' +
                       response.data.message + '</strong></p></div>';
            } else {
                html += '<div class="notice notice-error"><p><strong>' +
                       safefontsAjax.strings.upload_error + '</strong></p>';
                html += '<p>' + response.data + '</p></div>';
            }

            $result.html(html);
        },

        deleteFont: function(fontId, nonce, $button) {
            const $fontItem = $button.closest('.safefonts-font-item');
            $fontItem.addClass('safefonts-loading');

            $.ajax({
                url: safefontsAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'safefonts_delete_font',
                    font_id: fontId,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        $fontItem.fadeOut(300, function() {
                            $(this).remove();

                            // Check if this was the last font in the family
                            const $family = $fontItem.closest('.safefonts-font-family');
                            if ($family.find('.safefonts-font-item').length === 0) {
                                $family.fadeOut(300, function() {
                                    $(this).remove();
                                });
                            }
                        });
                    } else {
                        $fontItem.removeClass('safefonts-loading');
                        alert(safefontsAjax.strings.delete_error + ': ' + response.data);
                    }
                },
                error: function() {
                    $fontItem.removeClass('safefonts-loading');
                    alert(safefontsAjax.strings.delete_error);
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        SafeFontsAdmin.init();
    });

    // Make SafeFontsAdmin available globally
    window.SafeFontsAdmin = SafeFontsAdmin;

})(jQuery);