jQuery(document).ready(function($) {
    "use strict";

    var cookieDays = parseInt(aireset_params.cookie_days, 10);
    if (isNaN(cookieDays) || cookieDays < 1) {
        cookieDays = 30;
    }

    $(".aireset-shipping-calc").each(function() {
        var $container = $(this);
        var $postcodeInput = $container.find(".aireset-postcode");
        var $response = $container.find(".aireset-response");
        var autoCalculator = aireset_params.auto_shipping;

        var savedCep = getCookie("savedCep");
        if (savedCep) {
            setFormattedCep(savedCep, $postcodeInput);
        }

        $container.find(".aireset-shipping-calc-button").on("click", function(e) {
            e.preventDefault();

            var rawPostcode = ($postcodeInput.val() || "").replace(/\D/g, "");
            if (rawPostcode.length < 8) {
                $postcodeInput.focus();
                $response
                    .hide()
                    .html('<div class="woocommerce-message woocommerce-error">' + aireset_params.invalid_postcode_message + "</div>")
                    .fadeIn("fast");
                return;
            }

            var detectedVariation = detectProductVariation($container);
            if (!detectedVariation) {
                $response
                    .hide()
                    .html('<div class="woocommerce-message woocommerce-error">' + aireset_params.without_selected_variation_message + "</div>")
                    .fadeIn("fast");
                return;
            }

            var $requestButton = $(this);
            var originalButtonHtml = $requestButton.html();
            var qty = detectQty($container);

            $requestButton.prop("disabled", true).html('<span class="aireset-button-loader"></span>');
            $response.empty();

            $.ajax({
                type: "POST",
                url: aireset_params.ajax_url,
                data: {
                    action: "aireset_ajax_postcode",
                    product: detectedVariation,
                    qty: qty,
                    postcode: rawPostcode,
                    nonce: aireset_params.nonce
                }
            }).done(function(responseHtml) {
                $response.hide().html(responseHtml).fadeIn("fast");
            }).always(function() {
                $requestButton.prop("disabled", false).html(originalButtonHtml);
            });
        });

        $postcodeInput.on("input", function() {
            var digits = ($(this).val() || "").replace(/\D/g, "").substring(0, 8);
            var formattedValue = digits.length > 5 ? digits.substring(0, 5) + "-" + digits.substring(5, 8) : digits;

            $(this).val(formattedValue);
            setCookie("savedCep", digits, cookieDays);
        });

        $postcodeInput.on("keypress", function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                $container.find(".aireset-shipping-calc-button").trigger("click");
                return false;
            }
        });

        $(window).on("load", function() {
            if ($postcodeInput.val() !== "" && autoCalculator === "yes") {
                $container.find(".aireset-shipping-calc-button").trigger("click");
            }
        });
    });

    function detectQty($container) {
        var $qty = $container.closest(".summary").find(".quantity input.qty").first();
        if (!$qty.length) {
            $qty = $(".summary .quantity input.qty").first();
        }
        var qty = parseInt($qty.val(), 10);
        return !isNaN(qty) && qty > 0 ? qty : 1;
    }

    function detectProductVariation($container) {
        var $summary = $container.closest(".summary");
        var variationId = $summary.find('input[name="variation_id"]').val();
        var addToCartValue = $summary.find('*[name="add-to-cart"]').val();

        if (!variationId) {
            variationId = $('form.variations_form input[name="variation_id"]').val();
        }
        if (!addToCartValue) {
            addToCartValue = $('form.cart *[name="add-to-cart"]').first().val();
        }

        if (variationId && Number(variationId) > 0) {
            return variationId;
        }
        if (addToCartValue && Number(addToCartValue) > 0) {
            return addToCartValue;
        }
        return false;
    }

    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(";");
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === " ") {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    }

    function setFormattedCep(postcode, $inputElement) {
        var digits = String(postcode || "").replace(/\D/g, "").substring(0, 8);
        if (digits.length > 5) {
            $inputElement.val(digits.substring(0, 5) + "-" + digits.substring(5, 8));
            return;
        }
        $inputElement.val(digits);
    }
});
