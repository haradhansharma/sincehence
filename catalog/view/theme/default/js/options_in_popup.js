/*
 * Options in Pop-up
 * OpenCart Version: 2.x-3.x
 * Author: MagDevel (support@magdevel.com)
 * Homepage: https://magdevel.com/
 */

(function() {
  'use strict';

  var OptionsInPopup = function(options) {
    if (!(this instanceof OptionsInPopup)) return new OptionsInPopup(options);

    var defaults = {
      select_first_values: 0,
      live_price_update: 0,
      base_path: 'extension/module/options_in_popup',
    };

    options = options || {};

    for (var i in defaults) {
      if (typeof options[i] === 'undefined') {
        options[i] = defaults[i];
      }
    }

    var o = options;

    this.OpenPopup = function(product_id) {
      return openPopup(product_id);
    };

    function updateMiniCart(json_total) {
      $("#cart").load("index.php?route=common/cart/info #cart > *");
    }

    function successAlert(json_success) {
      $(".alert, .text-danger").remove();
      $(".alert-success").remove();
      $("#content").parent().before(
        '<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json_success +
        '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
      );
      $("html, body").animate({
        scrollTop: 0
      }, "slow");
    }

    function openPopup(product_id) {
      $.magnificPopup.open({
        items: {
          src: "index.php?route=" + o.base_path + "/index&product_id=" + product_id,
          type: "ajax"
        },
        preloader: false,
        showCloseBtn: false,
        removalDelay: 300,
        mainClass: "oip-mfp-zoom-in",
        callbacks: {
          ajaxContentAdded: function() {
            initPopup();
          },
        },
      });
    }

    function initPopup() {
      var minimum = parseInt($('input[name="oip_minimum"]').val());

      $("#oip-minus-qty").click(function() {
        var qty = parseInt($("#oip-input-qty").val());
        if (isNaN(qty) || qty <= minimum) {
          qty = minimum;
        } else {
          qty--;
        }
        $("#oip-input-qty").val(qty);
        updatePrice();
      });

      $("#oip-plus-qty").click(function() {
        var qty = parseInt($("#oip-input-qty").val());
        if (isNaN(qty) || qty < minimum) {
          qty = minimum;
        } else {
          qty++;
        }
        $("#oip-input-qty").val(qty);
        updatePrice();
      });

      // Update prices if the minimum quantity is > 1
      if ($('#oip-product [name=\'quantity\']').val() > 1) {
        updatePrice();
      }

      // Update prices when entering quantities manually
      $("#oip-product").on('keyup', '[name="quantity"]', function() {
        if (!isNaN(this.value) && this.value > 0) {
          updatePrice();
        }
      });

      // Update prices when selecting options
      $('#oip-product input[type="checkbox"], #oip-product input[type="radio"], #oip-product select').on('change', function() {
        updatePrice();
      });

      // Autoselect first option values
      if (o.select_first_values) {
        autoSelectFirstValues();
      }

      // Close Button
      $("#oip-btn-close").click(function() {
        closePopup();
      });

      // Add to Cart Button
      $("#oip-button-cart").click(function() {
        addToCartFromPopup();
      });

      // Initialize Datetimepicker
      if (typeof $().datetimepicker === 'function') {
        $("#oip-product .date").datetimepicker({
          pickTime: false
        });
        $("#oip-product .time").datetimepicker({
          pickDate: false
        });
        $("#oip-product .datetime").datetimepicker({
          pickDate: true,
          pickTime: true
        });
      }

      // Initialize File Upload Option
      $("#oip-product button[id^='button-upload']").on("click", function() {
        var node = this;
        $("#form-upload").remove();
        $("body").prepend(
          '<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>'
        );
        $("#form-upload input[name='file']").trigger("click");

        var timer;

        if (typeof timer != "undefined") {
          clearInterval(timer);
        }

        timer = setInterval(function() {
          if ($("#form-upload input[name='file']").val() != "") {
            clearInterval(timer);
            $.ajax({
              url: "index.php?route=tool/upload",
              type: "post",
              dataType: "json",
              data: new FormData($("#form-upload")[0]),
              cache: false,
              contentType: false,
              processData: false,
              beforeSend: function() {
                $(node).button("loading");
              },
              complete: function() {
                $(node).button("reset");
              },
              success: function(json) {
                $(node).parent().removeClass("has-error")
                $(node).siblings(".text-danger").remove();
                if (json.error) {
                  $(node).parent().find("input").after('<div class="text-danger">' + json.error + "</div>");
                }
                if (json.success) {
                  alert(json.success);
                  $(node).parent().find("input").val(json.code);
                }
              }
            });
          }
        }, 500);
      });

      // Get recurring description
      $("#oip-product select[name='recurring_id']").change(function() {
        $.ajax({
          url: "index.php?route=product/product/getRecurringDescription",
          type: "post",
          data: $("#oip-product input[name='product_id'], #oip-product input[name='quantity'], #oip-product select[name='recurring_id']"),
          dataType: 'json',
          beforeSend: function() {
            $("#oip-recurring-description").html('');
            $("#oip-product .text-danger").parent().removeClass("has-error");
            $("#oip-product .text-danger").remove();
          },
          success: function(json) {
            if (json.success) {
              $("#oip-recurring-description").html(json.success);
            }
          }
        });
      });
    }

    function autoSelectFirstValues() {
      $('#oip-product select[name^="option"] option[value=""]').remove();

      var last_name = '';

      $('#oip-product input[type="radio"][name^="option"]').each(function() {
        if ($(this).attr('name') != last_name) $(this).prop('checked', true);
        last_name = $(this).attr('name');
      });

      setTimeout(function() {
        updatePrice();
      }, 300);
    }

    function closePopup() {
      var oipMagnificPopup = $.magnificPopup.instance;
      oipMagnificPopup.close();
    }

    function addToCart(product_id, quantity) {
      quantity = quantity || 1;
      $.ajax({
        url: "index.php?route=checkout/cart/add",
        type: "post",
        data: "product_id=" + product_id + "&quantity=" + quantity + "&oip=1",
        dataType: "json",
        success: function(json) {
          if (json.redirect) {
            openPopup(product_id);
          }
          if (json.success) {
            updateMiniCart(json.total);
            successAlert(json.success);
          }
        }
      });
    }

    function addToCartFromPopup() {
      var product_id = parseInt($('#oip-product input[name="product_id"]').val());
      $.ajax({
        url: "index.php?route=checkout/cart/add",
        type: "post",
        data: $("#oip-product input[type='text'], #oip-product input[type='hidden'], #oip-product input[type='radio']:checked, #oip-product input[type='checkbox']:checked, #oip-product select, #oip-product textarea"),
        dataType: "json",
        beforeSend: function() {
          $("#oip-button-cart").button("loading");
        },
        complete: function() {
          $("#oip-button-cart").button("reset");
        },
        success: function(json) {
          $("#oip-product .text-danger").remove();
          $("#oip-product .form-group").removeClass("has-error");
          if (json.error) {
            if (json.error.option) {
              for (var i in json.error.option) {
                var element = $("#oip-product #input-option" + i.replace("_", "-"));
                if (element.parent().hasClass("input-group")) {
                  element.parent().after('<div class="text-danger">' + json.error.option[i] + "</div>");
                } else {
                  element.after('<div class="text-danger">' + json.error.option[i] + "</div>");
                }
              }
            }
            if (json.error.recurring) {
              $("#oip-product select[name='recurring_id']").after(
                '<div class="text-danger">' + json.error.recurring + "</div>"
              );
            }
            $(".text-danger").parent().addClass("has-error");
          }
          if (json.success) {
            closePopup();
            updateMiniCart(json.total);

            // Advanced Pop-up Cart
            if (window.apcData && window.apcData.open_when_added && window.apc) {
              if (typeof apc.OpenPopupCart === 'function') {
                setTimeout(function() {
                  apc.OpenPopupCart("autoclose");
                }, 300);
              }
              if (typeof apc.ReplaceButton === "function") {
                apc.ReplaceButton(product_id);
              }
              return;
            }

            // Add to Cart Button Change
            if (window.abcData) {
              if (window.abcData.replace_button_cp && typeof abcReplaceButton === "function") {
                abcReplaceButton(product_id);
              }
              if (window.abcData.show_notification && typeof abcNotify === "function") {
                setTimeout(function() {
                  abcNotify(json.success, 'success');
                }, 300);
                return;
              }
            }

            setTimeout(function() {
              successAlert(json.success);
            }, 300);
          }
        }
      });
    }

    var priceRequest = null;

    function updatePrice() {
      if (priceRequest) {
        priceRequest.abort();
      }
      if (o.live_price_update) {
        priceRequest = $.ajax({
          url: "index.php?route=" + o.base_path + "/updatePrice",
          type: "post",
          data: $("#oip-product [type='checkbox']:checked, #oip-product [type='radio']:checked, #oip-product select, #oip-product [name='product_id'], #oip-product [name='quantity']"),
          dataType: "json",
          success: function(json) {
            $(".js-oip-price").text(json.price);
            $(".js-oip-special").text(json.special);
            $(".js-oip-ex-tax").text(json.ex_tax);
            $(".js-oip-points").text(json.points);
            $(".js-oip-total-price").text(json.total_price);
            $(".js-oip-total-special").text(json.total_special);
            $(".js-oip-total-ex-tax").text(json.total_ex_tax);
            priceRequest = null;
          },
          error: function() {
            priceRequest = null;
          }
        });
      }
    }

    // Replace Add to Cart Function
    var replaceAddToCartFunction = true;

    if (window.apcData && window.apcData.open_when_added) {
      replaceAddToCartFunction = false;
    }

    if (window.abcData) {
      replaceAddToCartFunction = false;
    }

    if (replaceAddToCartFunction) {
      window.cart.add = function(product_id, quantity) {
        return addToCart(product_id, quantity);
      };
    }
  };

  $(document).ready(function() {
    var oipData = window.oipData || {};
    window.oip = new OptionsInPopup(oipData);
  });
})();
