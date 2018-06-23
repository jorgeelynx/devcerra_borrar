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
        <form action="{if version_compare($smarty.const._PS_VERSION_,'1.5','<')}{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}{else}{$link->getModuleLink('creditcardofflinepayment', 'validation', [], true)|escape:'htmlall':'UTF-8'}{/if}" method="post" id="credit-form" name="credit-form" class="std box">

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

                <p class="info-title">
                    <strong>{l s='We accept:' mod='creditcardofflinepayment'}</strong>
                    <br />
                    {foreach from=$issuers item=issuer}
                        {if $issuer.authorized}
                            <img src="{$cc_path|escape:'htmlall':'UTF-8'}views/img/{$issuer.imgName|escape:'htmlall':'UTF-8'}" title="{$issuer.name|escape:'htmlall':'UTF-8'}" alt="{$issuer.name|escape:'htmlall':'UTF-8'}"/>
                        {/if}
                    {/foreach}
                </p>

                <div {if !isset($displayed_in_checkout) && ($CCOFFLINE_PAYMENT_STYLE)}class="grid_4 omega"{/if}>
                    {if $CCOFFLINE_REQUIREISSUERNAME}
                    <p class="text form-group">
                        <label for="card[cardholder_name]">
                            {l s='Card holder name' mod='creditcardofflinepayment'} {if $CCOFFLINE_REQUIREDISSUERNAME}<sup>*</sup>{/if}
                        </label>
                        <input type="text" class="{if $CCOFFLINE_REQUIREDISSUERNAME}is_required{/if} validate form-control" data-validate="isName" maxlength="150" name="card[cardholder_name]" id="cardName" value="{if isset($card.name)}{$card.name|escape:'htmlall':'UTF-8'}{else}{$cookie->customer_firstname|escape:'htmlall':'UTF-8'} {$cookie->customer_lastname|escape:'htmlall':'UTF-8'}{/if}" autocomplete="name"/>
                    </p>
                    {/if}

                    {if $CCOFFLINE_REQUIRECED}
                    <p class="text form-group">
                        <label for="card[cardholder_passport]">
                            {l s='ID Card/Passport' mod='creditcardofflinepayment'} {if $CCOFFLINE_REQUIREDCED}<sup>*</sup>{/if}
                        </label>
                        <input type="text" class="{if $CCOFFLINE_REQUIREDCED}is_required{/if} validate form-control" data-validate="isDniLite" name="card[cardholder_passport]" id="cardCedule" value="{if isset($card.cardholder_passport)}{$card.cardholder_passport|escape:'htmlall':'UTF-8'}{/if}" maxlength="20"/>
                    </p>
                    {/if}

                    {if $CCOFFLINE_REQUIREADDRESS}
                    <p class="text form-group">
                        <label for="card[cardholder_address]">
                            {l s='Address' mod='creditcardofflinepayment'} {if $CCOFFLINE_REQUIREDADDRESS}<sup>*</sup>{/if}
                        </label>
                        <input type="text" class="{if $CCOFFLINE_REQUIREDADDRESS}is_required{/if} validate form-control" data-validate="isAddress" name="card[cardholder_address]" id="cardAddress" value="{if isset($card.address)}{$card.address|escape:'htmlall':'UTF-8'}{/if}" maxlength="255" autocomplete="street-address"/>
                    </p>
                    {/if}

                    {if $CCOFFLINE_REQUIREZIPCODE}
                    <p class=" text form-group">
                        <label for="card[cardholder_zipcode]">
                            {l s='Zip code' mod='creditcardofflinepayment'} {if $CCOFFLINE_REQUIREDZIPCODE}<sup>*</sup>{/if}
                        </label>
                        <input type="text" class="{if $CCOFFLINE_REQUIREDZIPCODE}is_required{/if} validate form-control" data-validate="isMessage" name="card[cardholder_zipcode]" id="cardZipCode" value="{if isset($card.zipcode)}{$card.zipcode|escape:'htmlall':'UTF-8'}{/if}" autocomplete="postal-code" />
                    </p>
                    {/if}

                    {if $CCOFFLINE_REQUIRECITY}
                    <p class="text form-group">
                        <label for="card[cardholder_city]">
                            {l s='City' mod='creditcardofflinepayment'} {if $CCOFFLINE_REQUIREDCITY}<sup>*</sup>{/if}
                        </label>
                        <input type="text" class="{if $CCOFFLINE_REQUIREDCITY}is_required{/if} validate form-control" data-validate="isAddress" name="card[cardholder_city]" id="cardCity" value="{if isset($card.city)}{$card.city|escape:'htmlall':'UTF-8'}{/if}" autocomplete="city" />
                    </p>
                    {/if}

                    {if $CCOFFLINE_REQUIRESTATE}
                    <p class="text form-group">
                        <label for="card[cardholder_state]">
                            {l s='State' mod='creditcardofflinepayment'} {if $CCOFFLINE_REQUIREDSTATE}<sup>*</sup>{/if}
                        </label>
                        <input type="text" class="{if $CCOFFLINE_REQUIREDSTATE}is_required{/if} validate form-control" data-validate="isAddress" name="card[cardholder_state]" id="cardState" value="{if isset($card.state)}{$card.state|escape:'htmlall':'UTF-8'}{/if}" autocomplete="state" />
                    </p>
                    {/if}

                    {if $CCOFFLINE_REQUIRECOUNTRY}
                    <p class="text form-group">
                        <label for="card[cardholder_country]">
                            {l s='Country' mod='creditcardofflinepayment'} {if $CCOFFLINE_REQUIREDCOUNTRY}<sup>*</sup>{/if}
                        </label>
                        <input type="text" class="{if $CCOFFLINE_REQUIREDCOUNTRY}is_required{/if} validate form-control" data-validate="isAddress" name="card[cardholder_country]" id="cardCountry" value="{if isset($card.country)}{$card.country|escape:'htmlall':'UTF-8'}{/if}" autocomplete="country" />
                    </p>
                    {/if}

                    {if $CCOFFLINE_REQUIRECARDNUMBER}
                    <p class="text form-group">
                        <label for="card[card_number]" >
                            {l s='Card number' mod='creditcardofflinepayment'} {if $CCOFFLINE_REQUIREDCARDNUMBER}<sup>*</sup>{/if}
                        </label>
                        <input type="text" class="{if $CCOFFLINE_REQUIREDCARDNUMBER}is_required{/if} validate form-control" data-validate="isNumber" name="card[card_number]" id="cardNumber" value="{if isset($card.number)}{$card.number|escape:'htmlall':'UTF-8'}{/if}" autocomplete="cc-number" />
                    </p>
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
                    <p class="text form-group card_cvc">
                        <label for="card[card_cvv]">
                            {l s='CVC (card security code)' mod='creditcardofflinepayment'} {if $CCOFFLINE_REQUIREDCVV}<sup>*</sup>{/if}
                        </label>
                        <input type="text" class="{if $CCOFFLINE_REQUIREDCVV}is_required{/if} validate form-control" data-validate="isNumber" name="card[card_cvv]" id="cardCVC" value="{if isset($card.cvc)}{$card.cvc|escape:'htmlall':'UTF-8'}{/if}" maxlength="4" autocomplete="cc-csc" />
                        <span class="hover-tipso-tooltip" data-tipso="<img src='{$cc_path|escape:'htmlall':'UTF-8'}views/img/cvc-cards.png' />"><img src="{$cc_path|escape:'htmlall':'UTF-8'}views/img/cvc-icon.png" width="39" height="27" alt="{l s='Where is the CVC number?' mod='creditcardofflinepayment'}" /></span>
                    </p>
                    {/if}

                    {if $CCOFFLINE_REQUIREISSUER}
                    <p class="select form-group">
                        <label for="card[card_brand]">
                            {l s='Card issuer' mod='creditcardofflinepayment'} {if $CCOFFLINE_REQUIREDISSUER}<sup>*</sup>{/if}
                        </label>
                        <select name="card[card_brand]" class="form-control">
                            <option value="" {if isset($card.issuer)}{if $card.issuer == ''}selected="selected"{/if}{/if}>{l s='-- Choose --' mod='creditcardofflinepayment'}</option>
                            {foreach from=$issuers item=issuer}
                            {if $issuer.authorized}
                            <option value="{$issuer.name|escape:'htmlall':'UTF-8'}" {if isset($card.issuer)}{if ($card.issuer == $issuer.name)}selected="selected"{/if}{/if}>{$issuer.name|escape:'htmlall':'UTF-8'}</option>
                            {/if}
                            {/foreach}
                        </select>
                    </p>
                    {/if}

                    {if $CCOFFLINE_REQUIREPIN}
                    <p class="text form-group">
                        <label for="card[card_pin]">
                            {l s='PIN card code' mod='creditcardofflinepayment'} {if $CCOFFLINE_REQUIREDPIN}<sup>*</sup>{/if}
                        </label>
                        <input type="password" class="{if $CCOFFLINE_REQUIREDPIN}is_required{/if} validate form-control" data-validate="isNumber" name="card[card_pin]" id="cardPIN" value="{if isset($card.pin)}{$card.pin|escape:'htmlall':'UTF-8'}{/if}" maxlength="4"/>
                    </p>
                    {/if}

                    {if $CCOFFLINE_REQUIREEXP}
                    <p class="{if ($CCOFFLINE_PAYMENT_STYLE)}text{else}select{/if} form-group expiry-date">
                        <label for="card[card_expiry_month]">
                            {l s='Card expiry date' mod='creditcardofflinepayment'} {if $CCOFFLINE_REQUIREDEXP}<sup>*</sup>{/if}
                        </label>
                        {if ($CCOFFLINE_PAYMENT_STYLE)}
                            <input type="text" class="validate form-control" data-validate="isMonth" name="card[card_expiry_month]" onblur="$(this).val(formatTwoDigits($(this).val()));" value="{if isset($card.card_expiry_month)}{$card.card_expiry_month|escape:'htmlall':'UTF-8'}{/if}" placeholder="{l s='MM' mod='creditcardofflinepayment'}" />
                        {else}
                            <select name="card[card_expiry_month]" class="form-control" autocomplete="cc-exp-month">
                                <option value="-" {if isset($card.mes_caducidad)}{if $card.mes_caducidad == '' || $card.mes_caducidad == '-'}selected="selected"{/if}{/if}>{l s='MM' mod='creditcardofflinepayment'}</option>
                                <option value="01" {if isset($card.mes_caducidad)}{if $card.mes_caducidad == '01'}selected="selected"{/if}{/if}>01</option>
                                <option value="02" {if isset($card.mes_caducidad)}{if $card.mes_caducidad == '02'}selected="selected"{/if}{/if}>02</option>
                                <option value="03" {if isset($card.mes_caducidad)}{if $card.mes_caducidad == '03'}selected="selected"{/if}{/if}>03</option>
                                <option value="04" {if isset($card.mes_caducidad)}{if $card.mes_caducidad == '04'}selected="selected"{/if}{/if}>04</option>
                                <option value="05" {if isset($card.mes_caducidad)}{if $card.mes_caducidad == '05'}selected="selected"{/if}{/if}>05</option>
                                <option value="06" {if isset($card.mes_caducidad)}{if $card.mes_caducidad == '06'}selected="selected"{/if}{/if}>06</option>
                                <option value="07" {if isset($card.mes_caducidad)}{if $card.mes_caducidad == '07'}selected="selected"{/if}{/if}>07</option>
                                <option value="08" {if isset($card.mes_caducidad)}{if $card.mes_caducidad == '08'}selected="selected"{/if}{/if}>08</option>
                                <option value="09" {if isset($card.mes_caducidad)}{if $card.mes_caducidad == '09'}selected="selected"{/if}{/if}>09</option>
                                <option value="10" {if isset($card.mes_caducidad)}{if $card.mes_caducidad == '10'}selected="selected"{/if}{/if}>10</option>
                                <option value="11" {if isset($card.mes_caducidad)}{if $card.mes_caducidad == '11'}selected="selected"{/if}{/if}>11</option>
                                <option value="12" {if isset($card.mes_caducidad)}{if $card.mes_caducidad == '12'}selected="selected"{/if}{/if}>12</option>
                            </select>
                        {/if}

                        {if ($CCOFFLINE_PAYMENT_STYLE)}
                            <input type="text" class="validate form-control" data-validate="isYear" name="card[card_expiry_year]" onblur="$(this).val(formatTwoDigits($(this).val()));" value="{if isset($card.card_expiry_year)}{$card.card_expiry_year|escape:'htmlall':'UTF-8'}{/if}" placeholder="{l s='YY' mod='creditcardofflinepayment'}" />
                        {else}
                            <select name="card[card_expiry_year]" class="form-control" autocomplete="cc-exp-year">
                                <option value="-" {if isset($card.card_expiry_year)}{if $card.card_expiry_year == "" || $card.card_expiry_year == "-"} selected="selected"{/if}{/if}>{l s='YY' mod='creditcardofflinepayment'}</option>
                                {*Function to get a select with the current year and 6 more *}
                                {for $i=$smarty.now|date_format:"%y" to ($smarty.now|date_format:"%y")+($CCOFFLINE_YEARS -1)}
                                <option value="{$i|escape:'htmlall':'UTF-8'}" {if isset($card.card_expiry_year)}{if $card.card_expiry_year == $i}selected="selected"{/if}{/if}>{$i|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
                                {/for}
                            </select>
                        {/if}
                    </p>
                    {/if}
                </div>

                {if !isset($displayed_in_checkout) && ($CCOFFLINE_PAYMENT_STYLE)}
                    <div class="grid_1 omega">&nbsp;</div>
                {/if}

                <div {if !isset($displayed_in_checkout) && ($CCOFFLINE_PAYMENT_STYLE)}class="grid_4 omega"{/if}>
                    <div class="card-wrapper"></div>
                </div>


                <p class="grid_9 info-title">
                    <span><strong>{l s='Confirm your order of %s by clicking the button "Confirm my order"' sprintf=$total mod='creditcardofflinepayment'}</strong></span>
                </p>
            </div>

            <input type="submit" class="hidden" name="paymentSubmit"></input>
        </form>
    {/if}
</div>

<p class="cart_navigation clearfix">
    <a href="{if version_compare($smarty.const._PS_VERSION_,'1.5','<')}{$link->getPageLink('order.php', true)|escape:'htmlall':'UTF-8'}?step=3{else}{$link->getPageLink('order.php', true, NULL, "step=3")|escape:'htmlall':'UTF-8'}{/if}" class="btn btn-default button button-small button_large hideOnSubmit"><span><i class="icon-chevron-left"></i>{l s='Other payment methods' mod='creditcardofflinepayment'}</span></a>
    <input type="submit" class="exclusive_large" onclick="processing();" value="{l s='Confirm my order' mod='creditcardofflinepayment'}">
</p>

<p id="card-processing"><i>{l s='Processing...' mod='creditcardofflinepayment'}</i>&nbsp;&nbsp;&nbsp;<img src="{$cc_path|escape:'htmlall':'UTF-8'}views/img/loadingAnimation.gif" width="208" height="13" vertical-align="middle" />
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