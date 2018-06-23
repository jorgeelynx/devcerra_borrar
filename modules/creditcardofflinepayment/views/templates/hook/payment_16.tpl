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

<div class="row">
	<div class="col-xs-12{if isset($CCOFFLINE_PAYMENT_SIZE)} {$CCOFFLINE_PAYMENT_SIZE|escape:'htmlall':'UTF-8'}{/if}">
		<div class="ccpayment">
			<p class="payment_module cred">
				<a href="{if isset($displayed_in_checkout) && $displayed_in_checkout}javascript:showForm(){else}{$link->getModuleLink('creditcardofflinepayment', 'payment', [], true)|escape:'htmlall':'UTF-8'}?opc=1{/if}" title="{l s='Pay with card' mod='creditcardofflinepayment'}" class="creditcard">
					{l s='Pay with card' mod='creditcardofflinepayment'}
				</a>
				{if isset($displayed_in_checkout) && $displayed_in_checkout}
					<div id="ccpayment_form" style="display:none">
						{include file="../front/payment_execution_16.tpl"}
					</div>
				{/if}
			</p>
		</div>
	</div>
</div>
<script type="text/javascript">
	function showForm() {
		$('#ccpayment_form').toggle();
		$.uniform.update("select.form-control");
	}

	var CCOFFLINE_PAYMENT_STYLE = {$CCOFFLINE_PAYMENT_STYLE|escape:'htmlall':'UTF-8'};
	var cc_path = "{$cc_path|escape:'htmlall':'UTF-8'}";
	var card_number = "{if isset($card.card_number)}{$card.card_number|escape:'htmlall':'UTF-8'}{/if}";
	var cardholder_name = "{if isset($card.cardholder_name)}{$card.cardholder_name|escape:'htmlall':'UTF-8'}{else}{$cookie->customer_firstname|escape:'htmlall':'UTF-8'} {$cookie->customer_lastname|escape:'htmlall':'UTF-8'}{/if}";
	var card_cvv = "{if isset($card.card_cvv)}{$card.card_cvv|escape:'htmlall':'UTF-8'}{/if}";
</script>