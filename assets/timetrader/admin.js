jQuery(document).ready(function($) {

    var add_slide_frame;
    var change_slide_frame;

    jQuery('.timetrader .add-slide').on('click', function(event) {
        event.preventDefault();

        // If the media frame already exists, reopen it.
        if (add_slide_frame) {
            add_slide_frame.open();
            return;
        }

        // Create the media frame.
        add_slide_frame = wp.media.frames.file_frame = wp.media({
            multiple: 'add',
            frame: 'post',
            library: {
                type: 'image'
            }
        });

        // When an image is selected, run a callback.
        add_slide_frame.on('insert', function() {

            jQuery(".timetrader .spinner").show();
            jQuery(".timetrader input[type=submit]").attr('disabled', 'disabled');

            var selection = add_slide_frame.state().get('selection');
            var slide_ids = [];

            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                slide_ids.push(attachment.id);
            });

            var data = {
                action: 'create_image_slide',
                slider_id: timetrader_slider_id,
                selection: slide_ids,
                _wpnonce: timetrader.addslide_nonce
            };

            jQuery.post(timetrader.ajaxurl, data, function(response) {
                jQuery(".timetrader .left table").append(response);
                jQuery(".timetrader .left table").trigger('resizeSlides');
            });
        });

        add_slide_frame.open();

        // Remove the Media Library tab (media_upload_tabs filter is broken in 3.6)
        jQuery(".media-menu a:contains('Media Library')").remove();

    });


    jQuery('.timetrader .change-image').on('click', function(event) {

        event.preventDefault();

        var $this = jQuery(this);
        var slide_from = $this.attr('data-slide-id');

        // Create the media frame.
        change_slide_frame = wp.media.frames.file_frame = wp.media({
            title: timetrader.change_image,
            library: {
                type: 'image'
            },
            button: {
                text: $this.attr('data-button-text') // button text
            }
        });

        // When an image is selected, run a callback.
        change_slide_frame.on('select', function() {

            jQuery(".timetrader .spinner").show();
            jQuery(".timetrader input[type=submit]").attr('disabled', 'disabled');

            var selection = change_slide_frame.state().get('selection');
            var slide_ids = [];

            selection.map(function(attachment) {
                attachment = attachment.toJSON();
                slide_to = attachment.id;
                slide_thumb = attachment.sizes.thumbnail.url;
            });

            var data = {
                action: 'change_slide_image',
                _wpnonce: timetrader.changeslide_nonce,
                slide_from: slide_from,
                slide_to: slide_to
            };

            if (data.slide_from !== data.slide_to) {

                jQuery.post(timetrader.ajaxurl, data, function(response) {

                    response = JSON.parse(response);

                    if (response.status == 'success') {
                        $this.closest('div.thumb').css('background-image', 'url(' + slide_thumb + ')');
                        jQuery(".timetrader .left table").trigger('resizeSlides');
                    } else {
                        alert(response.msg);
                    }

                });

            }

        });

        change_slide_frame.open();

    });

    jQuery("#screen-options-link-wrap").appendTo("#screen-meta-links").show();

    jQuery("#screen-options-switch-view-wrap").appendTo("#screen-meta-links").show();

    // Enable the correct options for this slider type
    var switchType = function(slider) {
        jQuery('.timetrader .option:not(.' + slider + ')').attr('disabled', 'disabled').parents('tr').hide();
        jQuery('.timetrader .option.' + slider).removeAttr('disabled').parents('tr').show();
        jQuery('.timetrader input.radio:not(.' + slider + ')').attr('disabled', 'disabled');
        jQuery('.timetrader input.radio.' + slider).removeAttr('disabled');

        jQuery('.timetrader .showNextWhenChecked:visible').parent().parent().next('tr').hide();
        jQuery('.timetrader .showNextWhenChecked:visible:checked').parent().parent().next('tr').show();

        // make sure that the selected option is available for this slider type
        if (jQuery('.effect option:selected').attr('disabled') === 'disabled') {
            jQuery('.effect option:enabled:first').attr('selected', 'selected');
        }

        // make sure that the selected option is available for this slider type
        if (jQuery('.theme option:selected').attr('disabled') === 'disabled') {
            jQuery('.theme option:enabled:first').attr('selected', 'selected');
        }
    };

    // enable the correct options on page load
    switchType(jQuery(".timetrader .select-slider:checked").attr("rel"));

    var toggleNextRow = function(checkbox) {
        if (checkbox.is(':checked')) {
            checkbox.parent().parent().next("tr").show();
        } else {
            checkbox.parent().parent().next("tr").hide();
        }
    }

    toggleNextRow(jQuery(".timetrader .showNextWhenChecked"));

    jQuery(".timetrader .showNextWhenChecked").on("change", function() {
        toggleNextRow(jQuery(this));
    });

    // mark the slide for resizing when the crop position has changed
    jQuery(".timetrader").on('change', '.left tr.slide .crop_position', function() {
        jQuery(this).closest('tr').data('crop_changed', true);
    });

    // handle slide libary switching
    jQuery(".timetrader .select-slider").on("click", function() {
        switchType(jQuery(this).attr("rel"));
    });

    // return a helper with preserved width of cells
    var timetrader_sortable_helper = function(e, ui) {
        ui.children().each(function() {
            jQuery(this).width(jQuery(this).width());
        });
        return ui;
    };

    // drag and drop slides, update the slide order on drop
    jQuery(".timetrader .left table tbody").sortable({
        helper: timetrader_sortable_helper,
        handle: "td.col-1",
        stop: function() {
            jQuery(".timetrader .left table").trigger("updateSlideOrder");
            jQuery(".timetrader form #ms-save").click();
        }
    });

    // bind an event to the slides table to update the menu order of each slide
    jQuery(".timetrader .left table").live("updateSlideOrder", function(event) {
        jQuery("tr", this).each(function() {
            jQuery("input.menu_order", jQuery(this)).val(jQuery(this).index());
        });
    });

    // bind an event to the slides table to update the menu order of each slide
    jQuery(".timetrader .left table").live("resizeSlides", function(event) {
        var slideshow_width = jQuery("input.width").val();
        var slideshow_height = jQuery("input.height").val();

        jQuery("tr.slide input[name='resize_slide_id']", this).each(function() {
            $this = jQuery(this);

            var thumb_width = $this.attr("data-width");
            var thumb_height = $this.attr("data-height");
            var slide_row = jQuery(this).closest('tr');
            var crop_changed = slide_row.data('crop_changed');

            if (thumb_width != slideshow_width || thumb_height != slideshow_height || crop_changed === true) {
                $this.attr("data-width", slideshow_width);
                $this.attr("data-height", slideshow_height);

                var data = {
                    action: "resize_image_slide",
                    slider_id: window.parent.timetrader_slider_id,
                    slide_id: $this.attr("data-slide_id"),
                    _wpnonce: timetrader.resize_nonce
                };

                jQuery.ajax({
                    type: "POST",
                    data: data,
                    async: false,
                    cache: false,
                    url: timetrader.ajaxurl,
                    success: function(data) {
                        if (crop_changed === true) {
                            slide_row.data('crop_changed', false);
                        }

                        if (console && console.log) {
                            console.log(data);
                        }
                    }
                });
            }
        });
    });

    jQuery(document).ajaxStop(function() {
        jQuery(".timetrader .spinner").hide();
        jQuery(".timetrader input[type=submit]").removeAttr("disabled");
    });


    jQuery(".useWithCaution").on("change", function() {
        if (!this.checked) {
            return alert(timetrader.useWithCaution);
        }
    });

    // helptext tooltips
    jQuery(".tipsy-tooltip").tipsy({
        className: 'msTipsy',
        live: true,
        delayIn: 500,
        html: true,
        gravity: 'e'
    });
    jQuery(".tipsy-tooltip-top").tipsy({
        live: true,
        delayIn: 500,
        html: true,
        gravity: 's'
    });

    // Select input field contents when clicked
    jQuery(".timetrader .shortcode input, .timetrader .shortcode textarea").on('click', function() {
        this.select();
    });

    // return lightbox width
    var getLightboxWidth = function() {
        var width = parseInt(jQuery('input.width').val(), 10);

        if (jQuery('.carouselMode').is(':checked')) {
            width = '75%';
        }

        return width;
    };

    // return lightbox height
    var getLightboxHeight = function() {
        var height = parseInt(jQuery('input.height').val(), 10);
        var thumb_height = parseInt(jQuery('input.thumb_height').val(), 10);

        if (isNaN(height)) {
            height = '70%';
        } else {
            height = height + 50;

            if (!isNaN(thumb_height)) {
                height = height + thumb_height;
            }
        }

        return height;
    };


    // IE10 treats placeholder text as the actual value of a textarea
    // http://stackoverflow.com/questions/13764607/html5-placeholder-attribute-on-textarea-via-jquery-in-ie10
    var fixIE10PlaceholderText = function() {
        jQuery("textarea").each(function() {
            if (jQuery(this).val() == jQuery(this).attr('placeholder')) {
                jQuery(this).val('');
            }
        });
    }

    jQuery(".timetrader .ms-toggle .hndle, .timetrader .ms-toggle .handlediv").on('click', function() {
        jQuery(this).parent().toggleClass('closed');
    });

    jQuery(".timetrader").on('click', 'ul.tabs li', function() {
        var tab = jQuery(this);
        tab.parent().parent().children('.tabs-content').children('div.tab').hide();
        tab.parent().parent().children('.tabs-content').children('div.' + tab.attr('rel')).show();
        tab.siblings().removeClass("selected");
        tab.addClass("selected");
    });


    // show the confirm dialogue
    jQuery(".timetrader").on('click', '.delete-slider', function() {
        return confirm(timetrader.confirm);
    });

    // delete a slide using ajax (avoid losing changes)
    jQuery(".timetrader").on('click', '.delete-slide', function(e) {
        e.preventDefault();

        var link = jQuery(this);

        if (confirm(timetrader.confirm)) {
            jQuery.get(link.attr('href'), function(data) {
                link.closest('tr').fadeOut(400, function() {
                    jQuery(this).remove();
                });
            });
        }
    });

    // AJAX save & preview
    jQuery(".timetrader form").find("input[type=submit]").on("click", function(e) {
        e.preventDefault();

        jQuery(".timetrader .spinner").show();
        jQuery(".timetrader input[type=submit]").attr("disabled", "disabled");

        // update slide order
        jQuery(".timetrader .left table").trigger('updateSlideOrder');

        fixIE10PlaceholderText();

        // get some values from elements on the page:
        var the_form = jQuery(this).parents("form");
        var data = the_form.serialize();
        var url = the_form.attr("action");
        var button = e.target;

        jQuery.ajax({
            type: "POST",
            data: data,
            cache: false,
            url: url,
            success: function(data) {
                var response = jQuery(data);

                jQuery.when(jQuery(".timetrader .left table").trigger("resizeSlides")).done(function() {

                    jQuery("button[data-thumb]", response).each(function() {
                        var $this = jQuery(this);
                        var editor_id = $this.attr("data-editor_id");
                        jQuery("button[data-editor_id=" + editor_id + "]")
                            .attr("data-thumb", $this.attr("data-thumb"))
                            .attr("data-width", $this.attr("data-width"))
                            .attr("data-height", $this.attr("data-height"));
                    });

                    fixIE10PlaceholderText();

                    if (button.id === "ms-preview") {
                        jQuery.colorbox({
                            iframe: true,
                            href: timetrader.iframeurl + "&slider_id=" + jQuery(button).data("slider_id"),
                            transition: "elastic",
                            innerHeight: getLightboxHeight(),
                            innerWidth: getLightboxWidth(),
                            scrolling: false,
                            fastIframe: false
                        });
                    }

                });
            }
        });
    });
});