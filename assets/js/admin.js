/**
 * SafeFonts Free Admin JavaScript
 */

(function($) {
    'use strict';

    let SafeFontsAdmin = {
        init: function() {
            this.initUploadForm();
            this.initFontDeletion();
            this.initBulkActions();
            this.initTabs();
            this.initCssRegeneration();
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

        initBulkActions: function() {
            // Select All checkbox
            $('#safefonts-select-all').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.safefonts-font-select').prop('checked', isChecked);
                SafeFontsAdmin.updateBulkDeleteButton();
            });

            // Individual checkbox change
            $(document).on('change', '.safefonts-font-select', function() {
                SafeFontsAdmin.updateBulkDeleteButton();

                // Update "Select All" state
                const totalCheckboxes = $('.safefonts-font-select').length;
                const checkedCheckboxes = $('.safefonts-font-select:checked').length;
                $('#safefonts-select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
            });

            // Delete selected button
            $('#safefonts-delete-selected').on('click', function(e) {
                e.preventDefault();
                SafeFontsAdmin.handleBulkDelete();
            });
        },

        updateBulkDeleteButton: function() {
            const selectedCount = $('.safefonts-font-select:checked').length;
            const $button = $('#safefonts-delete-selected');

            if (selectedCount > 0) {
                $button.prop('disabled', false);
                $button.text('Delete Selected (' + selectedCount + ')');
            } else {
                $button.prop('disabled', true);
                $button.text('Delete Selected');
            }
        },

        handleBulkDelete: function() {
            const selectedFonts = [];
            $('.safefonts-font-select:checked').each(function() {
                selectedFonts.push($(this).val());
            });

            if (selectedFonts.length === 0) {
                return;
            }

            const confirmMsg = selectedFonts.length === 1
                ? 'Are you sure you want to delete this font?'
                : 'Are you sure you want to delete ' + selectedFonts.length + ' fonts?';

            if (!confirm(confirmMsg)) {
                return;
            }

            const $button = $('#safefonts-delete-selected');
            const nonce = $button.data('nonce');

            $button.prop('disabled', true).text('Deleting...');

            $.ajax({
                url: safefontsAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'safefonts_bulk_delete_fonts',
                    font_ids: selectedFonts,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Remove deleted fonts from UI
                        selectedFonts.forEach(function(fontId) {
                            const $checkbox = $('.safefonts-font-select[value="' + fontId + '"]');
                            const $fontItem = $checkbox.closest('.safefonts-font-item');

                            $fontItem.fadeOut(300, function() {
                                $(this).remove();

                                // Check if family is now empty
                                const $family = $fontItem.closest('.safefonts-font-family');
                                if ($family.find('.safefonts-font-item').length === 0) {
                                    $family.fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                }
                            });
                        });

                        // Reset select all
                        $('#safefonts-select-all').prop('checked', false);

                        // Show success message
                        alert(response.data.message);

                        // Reload page after short delay
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        alert('Error: ' + response.data);
                        $button.prop('disabled', false);
                        SafeFontsAdmin.updateBulkDeleteButton();
                    }
                },
                error: function() {
                    alert('Failed to delete fonts.');
                    $button.prop('disabled', false);
                    SafeFontsAdmin.updateBulkDeleteButton();
                }
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

        initCssRegeneration: function() {
            $('#safefonts-regenerate-css').on('click', function(e) {
                e.preventDefault();
                SafeFontsAdmin.handleCssRegeneration($(this));
            });
        },

        handleCssRegeneration: function($button) {
            const nonce = $button.data('nonce');
            const $result = $('#safefonts-regenerate-css-result');

            // Disable button and show loading state
            $button.prop('disabled', true);
            $button.text(safefontsAjax.strings.regenerating);
            $result.empty();

            $.ajax({
                url: safefontsAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'regenerate_safefonts_css',
                    nonce: nonce
                },
                success: function(response) {
                    $button.prop('disabled', false);
                    $button.text('Regenerate CSS');

                    if (response.success) {
                        $result.html('<div class="notice notice-success inline"><p>' + response.data + '</p></div>');

                        // Clear success message after 5 seconds
                        setTimeout(function() {
                            $result.fadeOut(300, function() {
                                $(this).empty().show();
                            });
                        }, 5000);
                    } else {
                        $result.html('<div class="notice notice-error inline"><p>' + safefontsAjax.strings.regenerate_error + ': ' + response.data + '</p></div>');
                    }
                },
                error: function() {
                    $button.prop('disabled', false);
                    $button.text('Regenerate CSS');
                    $result.html('<div class="notice notice-error inline"><p>' + safefontsAjax.strings.regenerate_error + '</p></div>');
                }
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