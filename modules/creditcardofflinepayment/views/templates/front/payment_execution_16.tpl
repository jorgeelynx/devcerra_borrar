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

{if !isset($displayed_in_checkout)}
    {capture name=path}{l s='Card payment' mod='creditcardofflinepayment'}{/capture}
    {if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
    {/if}

    <h2>{l s='Order summary' mod='creditcardofflinepayment'}</h2>

    {assign var='current_step' value='payment'}
    {include file="$tpl_dir./order-steps.tpl"}
{/if}

<div id="payment_form">
    {if $nbProducts <= 0}
        <p class="warning">{l s='Your shopping cart is empty.' mod='creditcardofflinepayment'}</p>
    {else}
        {if !isset($displayed_in_checkout)}<h3 class="page-subheading">{l s='Card payment' mod='creditcardofflinepayment'}</h3>{/if}

        <form action="{$link->getModuleLink('creditcardofflinepayment', 'validation', [], true)|escape:'htmlall':'UTF-8'}" method="post" id="credit-form" name="credit-form" class="std box">

            <input type="hidden" name="id_currency" value="{$id_currency|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="validate" value="{$validate|escape:'htmlall':'UTF-8'}" />

            <div class="payment_form_container">
                {if isset($validation_errors) && $validation_errors|@count > 0}
                <div class="alert error alert-danger" id="errorDiv">
                    {l s='There are errors' mod='creditcardofflinepayment'}:
                    <ol>
                        {foreach from=$validation_errors item=error}
                        <li>{$error|escape:'htmlall':'UTF-8'}</li>
                        {/foreach}
                    </ol>
                </div>
                {/if}

                <div class="row">
                    {if $CCOFFLINE_DISPLAYISSUERS}
                        <div class="col-xs-12">
                            <p class="info-title">
                            {l s='We accept:' mod='creditcardofflinepayment'}
                            <br />
                            {foreach from=$issuers item=issuer}
                                {if $issuer.authorized}
                                    <img src="{$cc_path|escape:'htmlall':'UTF-8'}views/img/{$issuer.imgName|escape:'htmlall':'UTF-8'}" title="{$issuer.name|escape:'htmlall':'UTF-8'}" alt="{$issuer.name|escape:'htmlall':'UTF-8'}"/>
                                {/if}
                            {/foreach}
                            </p>
                        </div>
                    {/if}

                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-lg-8">
                                {if $CCOFFLINE_REQUIREISSUERNAME}
                                <div class="{if $CCOFFLINE_REQUIREDISSUERNAME}required{/if} text form-group">
                                    <label for="card[cardholder_name]" {if $CCOFFLINE_REQUIREDISSUERNAME}class="required"{/if}>
                                        {l s='Card holder name' mod='creditcardofflinepayment'}
                                    </label>
                                    <div class="{if ($CCOFFLINE_DISPLAYICONS)}input-group{else}form-group{/if}">
                                        {if ($CCOFFLINE_DISPLAYICONS)}<span class="input-group-addon"><i class="icon-user"></i></span>{/if}
                                        <input type="text" class="{if $CCOFFLINE_REQUIREDISSUERNAME}is_required{/if} validate form-control" data-validate="isName" maxlength="150" name="card[cardholder_name]" value="{if isset($card.cardholder_name)}{$card.cardholder_name|escape:'htmlall':'UTF-8'}{else}{$cookie->customer_firstname|escape:'htmlall':'UTF-8'} {$cookie->customer_lastname|escape:'htmlall':'UTF-8'}{/if}" autocomplete="name"/>
                                    </div>
                                </div>
                                {/if}

                                {if $CCOFFLINE_REQUIRECED}
                                <div class="{if $CCOFFLINE_REQUIREDCED}required{/if} text form-group">
                                    <label for="card[cardholder_passport]" {if $CCOFFLINE_REQUIREDCED}class="required"{/if}>
                                        {l s='ID Card/Passport' mod='creditcardofflinepayment'}
                                    </label>
                                    <div class="{if ($CCOFFLINE_DISPLAYICONS)}input-group{else}form-group{/if}">
                                        {if ($CCOFFLINE_DISPLAYICONS)}<span class="input-group-addon"><i class="icon-barcode"></i></span>{/if}
                                        <input type="text" class="{if $CCOFFLINE_REQUIREDCED}is_required{/if} validate form-control" data-validate="isDniLite" name="card[cardholder_passport]" value="{if isset($card.cardholder_passport)}{$card.cardholder_passport|escape:'htmlall':'UTF-8'}{/if}" maxlength="20"/>
                                    </div>
                                </div>
                                {/if}

                                {if $CCOFFLINE_REQUIREADDRESS}
                                <div class="{if $CCOFFLINE_REQUIREDADDRESS}required{/if} text form-group">
                                    <label for="card[cardholder_address]" {if $CCOFFLINE_REQUIREDADDRESS}class="required"{/if}>
                                        {l s='Address' mod='creditcardofflinepayment'}
                                    </label>
                                    <div class="{if ($CCOFFLINE_DISPLAYICONS)}input-group{else}form-group{/if}">
                                        {if ($CCOFFLINE_DISPLAYICONS)}<span class="input-group-addon"><i class="icon-home"></i></span>{/if}
                                        <input type="text" class="{if $CCOFFLINE_REQUIREDADDRESS}is_required{/if} validate form-control" data-validate="isAddress" name="card[cardholder_address]" value="{if isset($card.cardholder_address)}{$card.cardholder_address|escape:'htmlall':'UTF-8'}{/if}" maxlength="255" autocomplete="street-address"/>
                                    </div>
                                </div>
                                {/if}

                                {if $CCOFFLINE_REQUIREZIPCODE}
                                <div class="{if $CCOFFLINE_REQUIREDZIPCODE}required{/if} text form-group">
                                    <label for="card[cardholder_zipcode]" {if $CCOFFLINE_REQUIREDZIPCODE}class="required"{/if}>
                                        {l s='Zip code' mod='creditcardofflinepayment'}
                                    </label>
                                    <div class="{if ($CCOFFLINE_DISPLAYICONS)}input-group{else}form-group{/if}">
                                        {if ($CCOFFLINE_DISPLAYICONS)}<span class="input-group-addon"><i class="icon-location-arrow"></i></span>{/if}
                                        <input type="text" class="{if $CCOFFLINE_REQUIREDZIPCODE}is_required{/if} validate form-control" data-validate="isMessage" name="card[cardholder_zipcode]" value="{if isset($card.cardholder_zipcode)}{$card.cardholder_zipcode|escape:'htmlall':'UTF-8'}{/if}" autocomplete="postal-code" />
                                    </div>
                                </div>
                                {/if}

                                {if $CCOFFLINE_REQUIRECITY}
                                <div class="{if $CCOFFLINE_REQUIREDCITY}required{/if} text form-group">
                                    <label for="card[cardholder_city]" {if $CCOFFLINE_REQUIREDCITY}class="required"{/if}>
                                        {l s='City' mod='creditcardofflinepayment'}
                                    </label>
                                    <div class="{if ($CCOFFLINE_DISPLAYICONS)}input-group{else}form-group{/if}">
                                        {if ($CCOFFLINE_DISPLAYICONS)}<span class="input-group-addon"><i class="icon-location-arrow"></i></span>{/if}
                                        <input type="text" class="{if $CCOFFLINE_REQUIREDCITY}is_required{/if} validate form-control" data-validate="isAddress" name="card[cardholder_city]" value="{if isset($card.cardholder_city)}{$card.cardholder_city|escape:'htmlall':'UTF-8'}{/if}" autocomplete="city" />
                                    </div>
                                </div>
                                {/if}

                                {if $CCOFFLINE_REQUIRESTATE}
                                <div class="{if $CCOFFLINE_REQUIREDSTATE}required{/if} text form-group">
                                    <label for="card[cardholder_state]" {if $CCOFFLINE_REQUIREDSTATE}class="required"{/if}>
                                        {l s='State' mod='creditcardofflinepayment'}
                                    </label>
                                    <div class="{if ($CCOFFLINE_DISPLAYICONS)}input-group{else}form-group{/if}">
                                        {if ($CCOFFLINE_DISPLAYICONS)}<span class="input-group-addon"><i class="icon-location-arrow"></i></span>{/if}
                                        <input type="text" class="{if $CCOFFLINE_REQUIREDSTATE}is_required{/if} validate form-control" data-validate="isAddress" name="card[cardholder_state]" value="{if isset($card.cardholder_state)}{$card.cardholder_state|escape:'htmlall':'UTF-8'}{/if}" autocomplete="state" />
                                    </div>
                                </div>
                                {/if}

                                {if $CCOFFLINE_REQUIRECOUNTRY}
                                <div class="{if $CCOFFLINE_REQUIREDCOUNTRY}required{/if} text form-group">
                                    <label for="card[cardholder_country]" {if $CCOFFLINE_REQUIREDCOUNTRY}class="required"{/if}>
                                        {l s='Country' mod='creditcardofflinepayment'}
                                    </label>
                                    <div class="{if ($CCOFFLINE_DISPLAYICONS)}input-group{else}form-group{/if}">
                                        {if ($CCOFFLINE_DISPLAYICONS)}<span class="input-group-addon"><i class="icon-location-arrow"></i></span>{/if}
                                        <input type="text" class="{if $CCOFFLINE_REQUIREDCOUNTRY}is_required{/if} validate form-control" data-validate="isAddress" name="card[cardholder_country]" value="{if isset($card.cardholder_country)}{$card.cardholder_country|escape:'htmlall':'UTF-8'}{/if}" autocomplete="country" />
                                    </div>
                                </div>
                                {/if}

                                {if $CCOFFLINE_REQUIRECARDNUMBER}
                                <div class="{if $CCOFFLINE_REQUIREDCARDNUMBER}required{/if} text form-group">
                                    <label for="card[card_number]" {if $CCOFFLINE_REQUIREDCARDNUMBER}class="required"{/if}>
                                        {l s='Card number' mod='creditcardofflinepayment'}
                                    </label>
                                    <div class="{if ($CCOFFLINE_DISPLAYICONS)}input-group{else}form-group{/if}">
                                        {if ($CCOFFLINE_DISPLAYICONS)}<span class="input-group-addon"><i class="icon-credit-card"></i></span>{/if}
                                        <input type="text" class="{if $CCOFFLINE_REQUIREDCARDNUMBER}is_required{/if} validate form-control" data-validate="isCardNumber" name="card[card_number]" id="cardNumber" value="{if isset($card.card_number)}{$card.card_number|escape:'htmlall':'UTF-8'}{/if}" autocomplete="cc-number" />
                                    </div>
                                </div>
                                <script type="javascript">
                                    var card_number = document.getElementById('cardNumber'),
                                        cleanCardNumber;

                                    cleanCardNumber= function(e) {
                                        e.preventDefault();
                                        var pastedText = '';
                                        if (window.clipboardData && window.clipboardData.getData) { // IE
                                            pastedText = window.clipboardData.getData('Text');
                                          } else if (e.clipboardData && e.clipboardData.getData) {
                                            pastedText = e.clipboardData.getData('text/plain');
                                          }
                                        this.value = pastedText.replace(/\D/g, '');
                                    };

                                    card_number.onpaste = cleanCardNumber;
                                </script>
                                {/if}

                                {if $CCOFFLINE_REQUIRECVV}
                                <div class="{if $CCOFFLINE_REQUIREDCVV}required{/if} text form-group">
                                    <label for="card[card_cvv]" {if $CCOFFLINE_REQUIREDCVV}class="required"{/if}>
                                        {l s='CVC (card security code)' mod='creditcardofflinepayment'}
                                    </label>
                                    <div class="input-group form-group">
                                        {if ($CCOFFLINE_DISPLAYICONS)}<span class="input-group-addon"><i class="icon-lock"></i></span>{/if}
                                        <input type="text" class="{if $CCOFFLINE_REQUIREDCVV}is_required{/if} validate form-control" data-validate="isNumber" name="card[card_cvv]" value="{if isset($card.card_cvv)}{$card.card_cvv|escape:'htmlall':'UTF-8'}{/if}" maxlength="4" autocomplete="cc-csc" />
                                        <span class="input-group-addon hover-tipso-tooltip" data-tipso="<img src='{$cc_path|escape:'htmlall':'UTF-8'}views/img/cvc-cards.png' />">
                                            {l s='?' mod='creditcardofflinepayment'}
                                        </span>
                                    </div>
                                </div>
                                {/if}

                                {if $CCOFFLINE_REQUIREISSUER}
                                <div class="{if $CCOFFLINE_REQUIREDISSUER}required{/if} form-group">
                                    <label for="card[card_brand]" {if $CCOFFLINE_REQUIREDISSUER}class="required"{/if}>
                                        {l s='Card issuer' mod='creditcardofflinepayment'}
                                    </label>
                                    <div class="{if ($CCOFFLINE_DISPLAYICONS)}input-group{else}form-group{/if}">
                                        {if ($CCOFFLINE_DISPLAYICONS)}<span class="input-group-addon"><i class="icon-tag"></i></span>{/if}
                                        <select name="card[card_brand]" class="form-control">
                                            <option value="" {if isset($card.card_brand)}{if $card.card_brand == ''}selected="selected"{/if}{/if}>{l s='-- Choose --' mod='creditcardofflinepayment'}</option>
                                            {foreach from=$issuers item=issuer}
                                            {if $issuer.authorized}
                                            <option value="{$issuer.name|escape:'htmlall':'UTF-8'}" {if isset($card.card_brand)}{if ($card.card_brand == $issuer.name)}selected="selected"{/if}{/if}>{$issuer.name|escape:'htmlall':'UTF-8'}</option>
                                            {/if}
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                                {/if}

                                {if $CCOFFLINE_REQUIREPIN}
                                <div class="{if $CCOFFLINE_REQUIREDPIN}required{/if} text form-group">
                                    <label for="card[card_pin]" {if $CCOFFLINE_REQUIREDPIN}class="required"{/if}>
                                        {l s='PIN card code' mod='creditcardofflinepayment'}
                                    </label>
                                    <div class="{if ($CCOFFLINE_DISPLAYICONS)}input-group{else}form-group{/if}">
                                        {if ($CCOFFLINE_DISPLAYICONS)}<span class="input-group-addon"><i class="icon-lock"></i></span>{/if}
                                        <input type="password" class="{if $CCOFFLINE_REQUIREDPIN}is_required{/if} validate form-control" data-validate="isNumber" name="card[card_pin]" value="{if isset($card.card_pin)}{$card.card_pin|escape:'htmlall':'UTF-8'}{/if}" maxlength="4"/>
                                    </div>
                                </div>
                                {/if}

                                {if $CCOFFLINE_REQUIREEXP}
                                <div class="{if $CCOFFLINE_REQUIREDEXP}required{/if} {if ($CCOFFLINE_PAYMENT_STYLE)}text{else}select{/if} form-group expiry-date">
                                    <label for="card[card_expiry_month]" {if $CCOFFLINE_REQUIREDEXP}class="required"{/if}>
                                        {l s='Card expiry date' mod='creditcardofflinepayment'}
                                    </label>
                                    <div class="row {if ($CCOFFLINE_DISPLAYICONS)}input-group{/if}">

                                        {if ($CCOFFLINE_PAYMENT_STYLE)}
                                            <div class="{if ($CCOFFLINE_DISPLAYICONS)}col-xs-4 col-md-3{else}col-xs-4 col-md-4{/if} form-group month-input">
                                                {if ($CCOFFLINE_DISPLAYICONS)}<span class="input-group-addon"><i class="icon-calendar"></i></span>{/if}
                                                <input type="text" class="validate form-control" data-validate="isMonth" name="card[card_expiry_month]" onblur="$(this).val(formatTwoDigits($(this).val()));" value="{if isset($card.card_expiry_month)}{$card.card_expiry_month|escape:'htmlall':'UTF-8'}{/if}" placeholder="{l s='MM' mod='creditcardofflinepayment'}" />
                                            </div>
                                        {else}
                                            <div class="{if ($CCOFFLINE_DISPLAYICONS)}col-xs-5 col-md-3{else}col-xs-4 col-md-3{/if} nopadding month-input">
                                                {if ($CCOFFLINE_DISPLAYICONS)}<span class="input-group-addon"><i class="icon-calendar"></i></span>{/if}
                                                <select name="card[card_expiry_month]" class="form-control" autocomplete="cc-exp-month">
                                                    <option value="-" {if isset($card.card_expiry_month)}{if $card.card_expiry_month == '' || $card.card_expiry_month == '-'}selected="selected"{/if}{/if}>{l s='MM' mod='creditcardofflinepayment'}</option>
                                                    <option value="01" {if isset($card.card_expiry_month)}{if $card.card_expiry_month == '01'}selected="selected"{/if}{/if}>01</option>
                                                    <option value="02" {if isset($card.card_expiry_month)}{if $card.card_expiry_month == '02'}selected="selected"{/if}{/if}>02</option>
                                                    <option value="03" {if isset($card.card_expiry_month)}{if $card.card_expiry_month == '03'}selected="selected"{/if}{/if}>03</option>
                                                    <option value="04" {if isset($card.card_expiry_month)}{if $card.card_expiry_month == '04'}selected="selected"{/if}{/if}>04</option>
                                                    <option value="05" {if isset($card.card_expiry_month)}{if $card.card_expiry_month == '05'}selected="selected"{/if}{/if}>05</option>
                                                    <option value="06" {if isset($card.card_expiry_month)}{if $card.card_expiry_month == '06'}selected="selected"{/if}{/if}>06</option>
                                                    <option value="07" {if isset($card.card_expiry_month)}{if $card.card_expiry_month == '07'}selected="selected"{/if}{/if}>07</option>
                                                    <option value="08" {if isset($card.card_expiry_month)}{if $card.card_expiry_month == '08'}selected="selected"{/if}{/if}>08</option>
                                                    <option value="09" {if isset($card.card_expiry_month)}{if $card.card_expiry_month == '09'}selected="selected"{/if}{/if}>09</option>
                                                    <option value="10" {if isset($card.card_expiry_month)}{if $card.card_expiry_month == '10'}selected="selected"{/if}{/if}>10</option>
                                                    <option value="11" {if isset($card.card_expiry_month)}{if $card.card_expiry_month == '11'}selected="selected"{/if}{/if}>11</option>
                                                    <option value="12" {if isset($card.card_expiry_month)}{if $card.card_expiry_month == '12'}selected="selected"{/if}{/if}>12</option>
                                                </select>
                                            </div>
                                        {/if}

                                        {if ($CCOFFLINE_PAYMENT_STYLE)}
                                            <div class="{if ($CCOFFLINE_DISPLAYICONS)}col-xs-3 col-md-2 display-icons{else}col-xs-4 col-md-4{/if} year-input form-group">
                                                <input type="text" class="validate form-control" data-validate="isYear" name="card[card_expiry_year]" onblur="$(this).val(formatTwoDigits($(this).val()));" value="{if isset($card.card_expiry_year)}{$card.card_expiry_year|escape:'htmlall':'UTF-8'}{/if}" placeholder="{l s='YY' mod='creditcardofflinepayment'}" />
                                            </div>
                                        {else}
                                            <div class="{if ($CCOFFLINE_DISPLAYICONS)}col-xs-3 col-md-2 display-icons{else}col-xs-4 col-md-3{/if} form-group nopadding year-input">
                                                <select name="card[card_expiry_year]" class="form-control" autocomplete="cc-exp-year">
                                                    <option value="-" {if isset($card.card_expiry_year)}{if $card.card_expiry_year == "" || $card.card_expiry_year == "-"} selected="selected"{/if}{/if}>{l s='YY' mod='creditcardofflinepayment'}</option>
                                                    {*Function to get a select with the current year and X more *}
                                                    {for $i=$smarty.now|date_format:"%y" to ($smarty.now|date_format:"%y")+($CCOFFLINE_YEARS -1)}
                                                        <option value="{$i|escape:'htmlall':'UTF-8'}" {if isset($card.card_expiry_year)}{if $card.card_expiry_year == $i}selected="selected"{/if}{/if}>{$i|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
                                                    {/for}
                                                </select>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                                {/if}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="card-wrapper"></div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-xs-12">
                        <p>
                            <span><strong>{l s='Confirm your order of [1]%1$s[/1] by clicking the button "Confirm my order"' mod='creditcardofflinepayment' sprintf={convertPriceWithCurrency price=$total currency=$currency} tags=['<span class="price">']}</strong></span>
                        </p>
                    </div>
                </div>
            </div>

            <input type="submit" class="hidden" name="paymentSubmit"></input>
        </form>
    {/if}
</div>


{if (isset($OPC_SHOW_POPUP_PAYMENT) && $OPC_SHOW_POPUP_PAYMENT)}
    <p class="cart_navigation clearfix" id="cart_navigation">
        <a class="button-exclusive btn btn-default" href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
            <span class="visible-sm visible-md visible-lg"><i class="icon-chevron-left"></i>{l s='Other payment methods' mod='creditcardofflinepayment'}</span>
            <span class="visible-xs"><i class="icon-chevron-left"></i>{l s='Back' mod='creditcardofflinepayment'}</span></a>
        </a>
        <button class="button btn btn-default button-medium" type="submit" onclick="processing();">
            <span>{l s='Confirm my order' mod='creditcardofflinepayment'}<i class="icon-chevron-right right"></i></span>
        </button>
    </p>
{else}
    <div class="{if isset($displayed_in_checkout)}col-xs-12{/if} cart_navigation clearfix">
        <div class="row">
            <div class="col-xs-6">
                <a href="{$link->getPageLink('order.php', true, NULL, "step=3")|escape:'htmlall':'UTF-8'}" class="btn btn-default button button-small button_large hideOnSubmit"><span class="visible-sm visible-md visible-lg"><i class="icon-chevron-left"></i>{l s='Other payment methods' mod='creditcardofflinepayment'}</span><span class="visible-xs"><i class="icon-chevron-left"></i>{l s='Back' mod='creditcardofflinepayment'}</span></a>
            </div>
            <div class="col-xs-6">
                <button type="button" class="btn btn-default button button-medium hideOnSubmit pay-button" onclick="processing();"><span>{l s='Confirm my order' mod='creditcardofflinepayment'}</span></button>
            </div>
        </div>
    </div>
{/if}

<p id="card-processing">
    <em>{l s='Processing...' mod='creditcardofflinepayment'}</em>&nbsp;&nbsp;&nbsp;<img src="{$cc_path|escape:'htmlall':'UTF-8'}views/img/loadingAnimation.gif" width="208" height="13" vertical-align="middle" />
</p>

<script type="text/javascript">
//< ![CDATA[
    var cc_path = "{$cc_path|escape:'htmlall':'UTF-8'}";
    var card_number = "{if isset($card.card_number)}{$card.card_number|escape:'htmlall':'UTF-8'}{/if}";
    var cardholder_name = "{if isset($card.cardholder_name)}{$card.cardholder_name|escape:'htmlall':'UTF-8'}{else}{$cookie->customer_firstname|escape:'htmlall':'UTF-8'} {$cookie->customer_lastname|escape:'htmlall':'UTF-8'}{/if}";
    var card_cvv = "{if isset($card.card_cvv)}{$card.card_cvv|escape:'htmlall':'UTF-8'}{/if}";
    var CCOFFLINE_PAYMENT_STYLE = "{if isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE}1{else}0{/if}";
//]]>
</script>

