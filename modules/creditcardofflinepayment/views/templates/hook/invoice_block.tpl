{**
* Credit card offline payment
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2017 idnovate.com
*  @license   See above
*}

{literal}
<script type="text/javascript">
	$(document).ready(function(){
		//Function to delete card data from order
		$('#deleteInfo').click(function(event) {
			event.preventDefault();
			$.ajax({
				type: 'POST',
				url: "{/literal}{$link->getAdminLink('AdminCreditCardOfflinePayment')|escape:'htmlall':'UTF-8'}"{literal},
				async: true,
				cache: false,
				dataType : "json",
				data: '{/literal}ajax=true&id_order=' + '{$id_order|escape:"htmlall":"UTF-8"}'+'&action=delete&token='+'{$token|escape:"htmlall":"UTF-8"}{literal}',
				success: function(response) {
					if (!response.hasError) {
						$('#invoice_block').fadeOut();
					} else {
						alert('ERROR: unable to delete the info: '+response.error);
					}
				},
				error: function(response) {
					alert('ERROR: unable to delete the info');
				}
			})
		})
	});
</script>
{/literal}

<div class="row" id="invoice_block">
	<div class="col-xs-12">
		<div class="panel">
			<div class="panel-heading">
				<img src="{$cc_path|escape:'htmlall':'UTF-8'}logo.gif"/> <span>{l s='Card payment information' mod='creditcardofflinepayment'}</span>
				<div style="float:right;">
					<img src="../img/admin/disabled.gif" alt="{l s='Delete' mod='creditcardofflinepayment'}">
					<a id="deleteInfo" href="{$link->getAdminLink('AdminCreditCardOfflinePayment')|escape:'htmlall':'UTF-8'}?id_order={$id_order|escape:"htmlall":"UTF-8"}&action=delete&token={$token|escape:"htmlall":"UTF-8"}"><span>{l s='Delete info' mod='creditcardofflinepayment'}</span></a>
				</div>
			</div>
			<div class="tab-content">
				<div>
					{if isset($cardholder_name) and $cardholder_name <> ''}
						{l s='Card holder name' mod='creditcardofflinepayment'}: <b>{$cardholder_name|escape:'htmlall':'UTF-8'}</b></br>
					{/if}
					{if isset($cardholder_passport) and $cardholder_passport <> ''}
						{l s='ID Card/Passport' mod='creditcardofflinepayment'}: <b>{$cardholder_passport|escape:'htmlall':'UTF-8'}</b></br>
					{/if}
					{if isset($cardholder_address) and $cardholder_address <> ''}
						{l s='Address' mod='creditcardofflinepayment'}: <b>{$cardholder_address|escape:'htmlall':'UTF-8'}</b></br>
					{/if}
					{if isset($cardholder_zipcode) and $cardholder_zipcode <> ''}
						{l s='Zip Code' mod='creditcardofflinepayment'}: <b>{$cardholder_zipcode|escape:'htmlall':'UTF-8'}</b></br>
					{/if}
					{if isset($cardholder_city) and $cardholder_city <> ''}
						{l s='City' mod='creditcardofflinepayment'}: <b>{$cardholder_city|escape:'htmlall':'UTF-8'}</b></br>
					{/if}
					{if isset($cardholder_state) and $cardholder_state <> ''}
						{l s='State' mod='creditcardofflinepayment'}: <b>{$cardholder_state|escape:'htmlall':'UTF-8'}</b></br>
					{/if}
					{if isset($cardholder_country) and $cardholder_country <> ''}
						{l s='Country' mod='creditcardofflinepayment'}: <b>{$cardholder_country|escape:'htmlall':'UTF-8'}</b></br>
					{/if}
					{if isset($card_number) and $card_number <> ''}
						{l s='Card number' mod='creditcardofflinepayment'}: <b>{$card_number|escape:'htmlall':'UTF-8'}</b></br>
					{/if}
					{if isset($card_cvv) and $card_cvv <> ''}
						{l s='CVV' mod='creditcardofflinepayment'}: <b>{$card_cvv|escape:'htmlall':'UTF-8'}</b></br>
					{/if}
					{if isset($card_brand) and $card_brand <> ''}
						{l s='Card brand' mod='creditcardofflinepayment'}: <b>{$card_brand|escape:'htmlall':'UTF-8'}</b></br>
					{/if}
					{if isset($card_expiry_month) and $card_expiry_month <> ''}
						{l s='Expiry Month/Year' mod='creditcardofflinepayment'}: <b>{$card_expiry_month|escape:'htmlall':'UTF-8'}/{if isset($card_expiry_year)}{$card_expiry_year|escape:'htmlall':'UTF-8'}{/if}</b></br>
					{/if}
					{if isset($card_pin) and $card_pin <> '' and $card_pin > 0}
						{l s='Card pin' mod='creditcardofflinepayment'}: <b>{$card_pin|escape:'htmlall':'UTF-8'}</b></br>
					{/if}
					{if isset($amount) and $amount <> ''}
						{l s='Amount' mod='creditcardofflinepayment'}: <b>{$amount|escape:'htmlall':'UTF-8'}</b></br>
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>