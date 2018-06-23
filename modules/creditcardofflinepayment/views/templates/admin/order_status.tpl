{**
* Credit Card Offline Payment Module.
*
* Allows currently running stores to accept card information through their online store.
* Card number is verified and the information is stored masked at database.
* It can then be decrypted, together with the information received by email,
* at a later time for charges through an existing gateway (ie. a creditcard machine).
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

<table cellpadding="0" cellspacing="0" class="table">
	<thead>
		<tr>
			<th class="center">{l s='Enabled' mod='creditcardofflinepayment'}</th>
			<th>{l s='Status name' mod='creditcardofflinepayment'}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$states item=state}
		<tr>
			<td class="center">
				<input type="checkbox" name="CCOFFLINE_DATA_OS_REM[]" {if isset($CCOFFLINE_DATA_OS_REM) && in_array($state.id_order_state, $CCOFFLINE_DATA_OS_REM)}checked{/if} value="{$state.id_order_state|escape:'htmlall':'UTF-8'}"/>
			</td>
			<td>
				<span class="label color_field" style="background-color: {$state.color|escape:'htmlall':'UTF-8'}; color:{if Tools::getBrightness($state.color|escape:'htmlall':'UTF-8') < 128}white{else}#383838{/if}; padding: 0.15em 0.4em">{$state.name|escape:'htmlall':'UTF-8'}</span>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>