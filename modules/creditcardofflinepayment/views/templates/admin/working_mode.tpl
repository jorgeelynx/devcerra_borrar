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

<style>
	#working_mode_by_mail,
	#working_mode_in_database {
		display: none;
	}
</style>

<div id="working_mode_by_mail">
	<div class="bootstrap">
		<div class="alert alert-info">
			<p>{l s='How you will see the card info:' mod='creditcardofflinepayment'}</p>
			<p><img src="{$this_path|escape:'htmlall':'UTF-8'}views/img/working_mode_by_mail_{$available_iso_code|escape:'htmlall':'UTF-8'}.png" /></p>
		</div>
	</div>
</div>

<div id="working_mode_in_database">
	<div class="bootstrap">
		<div class="alert alert-info">
			<p>{l s='How you will see the card info:' mod='creditcardofflinepayment'}</p>
			<p><img src="{$this_path|escape:'htmlall':'UTF-8'}views/img/working_mode_in_database_{$available_iso_code|escape:'htmlall':'UTF-8'}.png" /></p>
		</div>
	</div>

	<div class="alert alert-warning">
		<p>{l s='CAUTION! The information will be stored at database. To accomplish with PCI/DSS your server must be secure. Please ask your hosting provider. You are responsible of any act.' mod='creditcardofflinepayment'}<br /><strong>{l s='Module will work correctly anyway.' mod='creditcardofflinepayment'}</strong></p>
	</div>
</div>

