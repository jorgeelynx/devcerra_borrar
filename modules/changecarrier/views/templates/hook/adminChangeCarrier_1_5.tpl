{*
* Change carrier for an existing order
*
* @author UniqueModules.com
* @copyright UniqueModules.com
* The buyer can free use/edit/modify this software in anyway
* The buyer is NOT allowed to redistribute this module in anyway or resell it or redistribute it to third party
*}
<script>
$(function () {
    var tempvalfirst = $("input[name='new_total_shipping_tax_excl']").val();
    var tempvalsecond = $("input[name='new_total_shipping_tax_incl']").val();
    $("input[name='new_total_shipping_tax_excl']").val(tempvalfirst.replace(/[^\d.]/g,""));
    $("input[name='new_total_shipping_tax_incl']").val(tempvalsecond.replace(/[^\d.]/g,""));

    var pretaxvalue = parseFloat($('#new_carrier_id').find('option:selected').attr('id'));
    $('#taxvalue').val(pretaxvalue.toFixed(2));
    $("#new_carrier_id").change(function () {
        var pretaxvalue = parseFloat($('#new_carrier_id').find('option:selected').attr('id'));
        $('#taxvalue').val(pretaxvalue.toFixed(2));
    });
    $('#chkTax').change(function () {
        if ($('#chkTax').is(':checked') == true) {
            $('#taxvalue').val('').prop('disabled', false);
        } else {
            var pretaxvalue = parseFloat($('#new_carrier_id').find('option:selected').attr('id'));
            $('#taxvalue').val(pretaxvalue.toFixed(2));
            $('#taxvalue').prop('disabled', true);
        }
    });

    $("input[name='taxvalue']").focusout(function () {
        var sanitnewvalue = parseFloat($(this).val());
        $(this).val(sanitnewvalue.toFixed(2));
    });
    $("input[name='new_order_carrier_weight']").focusout(function () {
        var sanitnewvalue = parseFloat($(this).val());
        $(this).val(sanitnewvalue.toFixed(3));
    });
    $("input[name='new_total_shipping_tax_incl']").focusout(function () {
        var sanitnewvalue = parseFloat($(this).val());
        $(this).val(sanitnewvalue.toFixed(2));
    });
    $("input[name='new_total_shipping_tax_excl']").focusout(function () {
        var sanitnewvalue = parseFloat($(this).val());
        $(this).val(sanitnewvalue.toFixed(2));
    });
    $("input[name='new_total_shipping_tax_incl']").keyup(function () {
        if ($('#taxvalue').is(':disabled')) {
            var tax = $('#new_carrier_id').find('option:selected').attr('id');
        } else {
            var tax = $('#taxvalue').val();
        }
        if (tax != 0.00) {
            tax = ((tax / 100.00) + 1.00) * 100;
            var result = ($(this).val() * 100) / tax;
            $("input[name='new_total_shipping_tax_excl']").val(result.toFixed(2));

        } else {
            $("input[name='new_total_shipping_tax_excl']").val($(this).val());
        }
    });
    $("input[name='new_total_shipping_tax_excl']").keyup(function () {
        if ($('#taxvalue').is(':disabled')) {
            var tax = $('#new_carrier_id').find('option:selected').attr('id');
        } else {
            var tax = $('#taxvalue').val();
        }
        var tax = (tax / 100.00) + 1.00;
        if (tax != 0.00) {


            var result = $(this).val() * tax;
            $("input[name='new_total_shipping_tax_incl']").val(result.toFixed(2));


        } else {
            $("input[name='new_total_shipping_tax_incl']").val($(this).val());
        }
    });
    $("#submit_changes").click(function () {
        $("#change_carrier_form").submit(function (e) {
            return false;
        });
        $.ajax({
            url: "../modules/changecarrier/adminajax.php",
            type: "POST",
            async: true,
            cache: false,
            dataType: "json",
            data: {
                submit_changes: 1,
                new_carrier_id: $("#new_carrier_id option:selected").val(),
                new_carrier_tax: $("#taxvalue").val(),
                id_order: '{$order->id|escape:'htmlall':'UTF-8'}',
                new_total_shipping_tax_incl: $("input[name='new_total_shipping_tax_incl']").val(),
                new_total_shipping_tax_excl: $("input[name='new_total_shipping_tax_excl']").val(),
                new_order_carrier_weight: $("input[name='new_order_carrier_weight']").val()
            },
            success: function (data) {
                if (data.ok == 1) {
                updateAmounts(data.order);
                $("#change_carrier_form .alert.alert-danger").hide();
                $('#shipping_table tr').children('td').eq(2).text(data.carrier_name);
                $('#shipping_table tr').children('td').eq(3).text(data.weight);
                $('#shipping_table tr').children('td').eq(4).text(data.new_shiiping_cost);
                $("#change_carrier_form .alert-success").fadeIn(200).show();
                $('#change_carrier_form .alert-success').delay(3000).slideUp();
                    
                } else {
                    $("#change_carrier_form .alert-danger").fadeIn(200).show();
                    $('#change_carrier_form .alert-danger').delay(3000).slideUp();
                }
            }
        });
    });
});
</script>
<br />
<br/>
<fieldset>
	<legend><img src="../modules/changecarrier/logo.gif"> {l s='Change Carrier' mod='changecarrier'}</legend>
	<form id="change_carrier_form" action="../modules/changecarrier/adminajax.php" method="post"> <input type="hidden" id="tax" name="tax" value="{$order->carrier_tax_rate|number_format:2|escape:'htmlall':'UTF-8'}" />
		<div class="alert-success" style="display:none;background-color: #DFF0D8;border-color: #D6E9C6;color: #3C763D;padding:20px">
			{l s='Carrier and shipping cost have been succesfully updated' mod='changecarrier'}
		</div>
		<div class="alert-danger" style="display: none;background-color: #FFB8C7;padding:20px">
			{l s='Error during saving changes. Try again later.' mod='changecarrier'}
		</div>
		<div class="row">
			<label class="control-label col-lg-3">{l s='Carrier:' mod='changecarrier'}</label>
			<select id="new_carrier_id" name="new_carrier_id">
			{foreach $carriers as $carrier}
			<option id="{Tax::getCarrierTaxRate($carrier.id_carrier, null)|escape:'htmlall':'UTF-8'}" value="{$carrier.id_carrier|escape:'htmlall':'UTF-8'}" {if $carrier.id_carrier == $order->id_carrier}selected="selected"{/if}>{$carrier.name|escape:'htmlall':'UTF-8'}{if $tax} ({Tax::getCarrierTaxRate($carrier.id_carrier, null)|escape:'htmlall':'UTF-8'})%{/if}</option>
			{/foreach}

			</select>
			<button id="submit_changes" type="submit" class="button" name="submit_changes">
			{l s='Update' mod='changecarrier'} </button>
		</div>
		<div class="row" style="margin-top:10px">
			<label class="control-label col-lg-3">{l s='Shipping cost' mod='changecarrier'}{if $tax_enabled > 0}{l s='(Tax incl.)' mod='changecarrier'}{/if}:</label>
			<div class="col-lg-6">
				<input type="text" name="new_total_shipping_tax_incl" class="form-control fixed-width-sm col-lg-3" value="{$order->total_shipping_tax_incl|number_format:2|escape:'htmlall':'UTF-8'}" />
				<span class="col-lg-3"> {$currency->sign|escape:'html':'UTF-8'}</span>
			</div>
		</div>
		<div class="row" style="{if $tax_enabled < 1}display:none;{/if}margin-top:10px">
			<label class="control-label col-lg-3">{l s='Shipping cost' mod='changecarrier'}{if $tax_enabled > 0}{l s='(Tax excl.)' mod='changecarrier'}{/if}:</label>
			<div class="col-lg-6">
				<input type="text" name="new_total_shipping_tax_excl" class="form-control fixed-width-sm col-lg-3" value="{$order->total_shipping_tax_excl|number_format:2|escape:'htmlall':'UTF-8'}" />
				<span class="col-lg-3"> {$currency->sign|escape:'html':'UTF-8'}</span>
			</div>
		</div>
		<div class="row" style="{if $tax_enabled < 1}display:none;{/if}margin-top:10px">
			<label class="control-label col-lg-3">{l s='Enable custom carrier Tax rate:' mod='changecarrier'}</label>
			<div class="col-lg-6">
				<input type="text" class="form-control fixed-width-sm col-lg-3" name="taxvalue" id="taxvalue" value="" disabled="disabled"/>
				<span class="col-lg-3">% </span>
				<input type="checkbox" class="form-control fixed-width-sm col-lg-3" id="chkTax"/>
			</div>
		</div>
		<div class="row" style="margin-top:10px">
			<label class="control-label col-lg-3">{l s='Shipping weight:' mod='changecarrier'}</label>
			<div class="col-lg-6">
				<input type="text" name="new_order_carrier_weight" class="form-control fixed-width-sm col-lg-3" value="{$order_carrier->weight|number_format:3|escape:'htmlall':'UTF-8'}" />
				<span class="col-lg-3"> {$weight_unit|escape:'htmlall':'UTF-8'}</span>
			</div>
		</div>
	</form>
</fieldset>