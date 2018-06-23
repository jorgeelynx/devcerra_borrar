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

{extends file='page.tpl'}

{block name='page_title'}
    {l s='Card payment' mod='creditcardofflinepayment'}
{/block}

{block name='page_content'}
    <div class="card-payment" id="payment_form">
        <form action="{$link->getModuleLink('creditcardofflinepayment', 'validation', [], true)|escape:'htmlall':'UTF-8'}" method="post" id="credit-form" name="credit-form" class="std box">

            <input type="hidden" name="id_currency" value="{$id_currency|escape:'htmlall':'UTF-8'}"/>
            <input type="hidden" name="validate" value="{$validate|escape:'htmlall':'UTF-8'}" />

            <div class="payment_form_container">
                {if isset($validation_errors) && $validation_errors|@count > 0}
                    <div class="alert error alert-danger" id="errorDiv">
                        {l s='There are error(s)' mod='creditcardofflinepayment'}:
                        <ol>
                            {foreach from=$validation_errors item=error}
                            <li>{$error|escape:'htmlall':'UTF-8'}</li>
                            {/foreach}
                        </ol>
                    </div>
                {/if}

                {if $CCOFFLINE_DISPLAYISSUERS}
                    <div class="form-group card-issuers">
                        <h6>{l s='We accept:' mod='creditcardofflinepayment'}</h6>
                        <div>
                            {foreach from=$issuers item=issuer}
                                {if $issuer.authorized}
                                    <img src="{$cc_path|escape:'htmlall':'UTF-8'}views/img/{$issuer.imgName|escape:'htmlall':'UTF-8'}" title="{$issuer.name|escape:'htmlall':'UTF-8'}" alt="{$issuer.name|escape:'htmlall':'UTF-8'}"/>
                                {/if}
                            {/foreach}
                        </div>
                    </div>
                {/if}

                <div class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-sm-6{else}col-xs-12{/if}">
                    {if $CCOFFLINE_REQUIREISSUERNAME}
                    <div class="{if $CCOFFLINE_REQUIREDISSUERNAME}required{/if} form-group row">
                        <label for="card[cardholder_name]" class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-4{else}col-md-3{/if} form-control-label {if $CCOFFLINE_REQUIREDISSUERNAME}required{/if}">
                            {l s='Card holder name' mod='creditcardofflinepayment'}
                        </label>
                        <div class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-8{else}col-md-6{/if}">
                            <input type="text" class="{if $CCOFFLINE_REQUIREDISSUERNAME}is_required{/if} validate form-control" data-validate="isName" maxlength="150" name="card[cardholder_name]" value="{if isset($card.cardholder_name)}{$card.cardholder_name|escape:'htmlall':'UTF-8'}{else}{$customer['firstname']|escape:'htmlall':'UTF-8'} {$customer['lastname']|escape:'htmlall':'UTF-8'}{/if}" autocomplete="name"/>
                        </div>
                    </div>
                    {/if}

                    {if $CCOFFLINE_REQUIRECED}
                    <div class="{if $CCOFFLINE_REQUIREDCED}required{/if} form-group row">
                        <label for="card[cardholder_passport]" class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-4{else}col-md-3{/if} form-control-label {if $CCOFFLINE_REQUIREDCED}required{/if}">
                            {l s='ID Card/Passport' mod='creditcardofflinepayment'}
                        </label>
                        <div class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-8{else}col-md-6{/if}">
                            <input type="text" class="{if $CCOFFLINE_REQUIREDCED}is_required{/if} validate form-control" data-validate="isDniLite" name="card[cardholder_passport]" value="{if isset($card.cardholder_passport)}{$card.cardholder_passport|escape:'htmlall':'UTF-8'}{/if}" maxlength="20"/>
                        </div>
                    </div>
                    {/if}

                    {if $CCOFFLINE_REQUIREADDRESS}
                    <div class="{if $CCOFFLINE_REQUIREDADDRESS}required{/if} form-group row">
                        <label for="card[cardholder_address]" class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-4{else}col-md-3{/if} form-control-label {if $CCOFFLINE_REQUIREDADDRESS}required{/if}">
                            {l s='Address' mod='creditcardofflinepayment'}
                        </label>
                        <div class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-8{else}col-md-6{/if}">
                            <input type="text" class="{if $CCOFFLINE_REQUIREDADDRESS}is_required{/if} validate form-control" data-validate="isAddress" name="card[cardholder_address]" value="{if isset($card.cardholder_address)}{$card.cardholder_address|escape:'htmlall':'UTF-8'}{/if}" maxlength="255" autocomplete="street-address"/>
                        </div>
                    </div>
                    {/if}

                    {if $CCOFFLINE_REQUIREZIPCODE}
                    <div class="{if $CCOFFLINE_REQUIREDZIPCODE}required{/if} form-group row">
                        <label for="card[cardholder_zipcode]" class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-4{else}col-md-3{/if} form-control-label {if $CCOFFLINE_REQUIREDZIPCODE}required{/if}">
                            {l s='Zip code' mod='creditcardofflinepayment'}
                        </label>
                        <div class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-8{else}col-md-6{/if}">
                            <input type="text" class="{if $CCOFFLINE_REQUIREDZIPCODE}is_required{/if} validate form-control" data-validate="isMessage" name="card[cardholder_zipcode]" value="{if isset($card.cardholder_zipcode)}{$card.cardholder_zipcode|escape:'htmlall':'UTF-8'}{/if}" autocomplete="postal-code" />
                        </div>
                    </div>
                    {/if}

                    {if $CCOFFLINE_REQUIRECITY}
                    <div class="{if $CCOFFLINE_REQUIREDCITY}required{/if} form-group row">
                        <label for="card[cardholder_city]" class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-4{else}col-md-3{/if} form-control-label {if $CCOFFLINE_REQUIREDCITY}required{/if}">
                            {l s='City' mod='creditcardofflinepayment'}
                        </label>
                        <div class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-8{else}col-md-6{/if}">
                            <input type="text" class="{if $CCOFFLINE_REQUIREDCITY}is_required{/if} validate form-control" data-validate="isAddress" name="card[cardholder_city]" value="{if isset($card.cardholder_city)}{$card.cardholder_city|escape:'htmlall':'UTF-8'}{/if}" autocomplete="city" />
                        </div>
                    </div>
                    {/if}

                    {if $CCOFFLINE_REQUIRESTATE}
                    <div class="{if $CCOFFLINE_REQUIREDSTATE}required{/if} form-group row">
                        <label for="card[cardholder_state]" class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-4{else}col-md-3{/if} form-control-label {if $CCOFFLINE_REQUIREDSTATE}required{/if}">
                            {l s='State' mod='creditcardofflinepayment'}
                        </label>
                        <div class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-8{else}col-md-6{/if}">
                            <input type="text" class="{if $CCOFFLINE_REQUIREDSTATE}is_required{/if} validate form-control" data-validate="isAddress" name="card[cardholder_state]" value="{if isset($card.cardholder_state)}{$card.cardholder_state|escape:'htmlall':'UTF-8'}{/if}" autocomplete="state" />
                        </div>
                    </div>
                    {/if}

                    {if $CCOFFLINE_REQUIRECOUNTRY}
                    <div class="{if $CCOFFLINE_REQUIREDCOUNTRY}required{/if} form-group row">
                        <label for="card[cardholder_country]" class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-4{else}col-md-3{/if} form-control-label {if $CCOFFLINE_REQUIREDCOUNTRY}required{/if}">
                            {l s='Country' mod='creditcardofflinepayment'}
                        </label>
                        <div class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-8{else}col-md-6{/if}">
                            <input type="text" class="{if $CCOFFLINE_REQUIREDCOUNTRY}is_required{/if} validate form-control" data-validate="isAddress" name="card[cardholder_country]" value="{if isset($card.cardholder_country)}{$card.cardholder_country|escape:'htmlall':'UTF-8'}{/if}" autocomplete="country" />
                        </div>
                    </div>
                    {/if}

                    {if $CCOFFLINE_REQUIRECARDNUMBER}
                    <div class="{if $CCOFFLINE_REQUIREDCARDNUMBER}required{/if} form-group row">
                        <label for="card[card_number]" class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-4{else}col-md-3{/if} form-control-label {if $CCOFFLINE_REQUIREDCARDNUMBER}required{/if}">
                            {l s='Card number' mod='creditcardofflinepayment'}
                        </label>
                        <div class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-8{else}col-md-6{/if}">
                            <input type="text" class="{if $CCOFFLINE_REQUIREDCARDNUMBER}is_required{/if} validate form-control" name="card[card_number]" id="cardNumber" value="{if isset($card.card_number)}{$card.card_number|escape:'htmlall':'UTF-8'}{/if}" autocomplete="cc-number" />
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
                    <div class="{if $CCOFFLINE_REQUIREDCVV}required{/if} form-group row">
                        <label for="card[card_cvv]" class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-4{else}col-md-3{/if} form-control-label {if $CCOFFLINE_REQUIREDCVV}required{/if}">
                            {l s='CVC (card security code)' mod='creditcardofflinepayment'}
                        </label>
                        <div class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-8{else}col-md-6{/if}">
                            <div class="input-group">
                                <input type="text" class="{if $CCOFFLINE_REQUIREDCVV}is_required{/if} validate form-control" data-validate="isNumber" name="card[card_cvv]" value="{if isset($card.card_cvv)}{$card.card_cvv|escape:'htmlall':'UTF-8'}{/if}" maxlength="4" autocomplete="cc-csc" />
                                <span class="input-group-btn hover-tipso-tooltip" data-tipso="<img src='{$cc_path|escape:'htmlall':'UTF-8'}views/img/cvc-cards.png' />">
                                    <button class="btn" type="button">{l s='?' mod='creditcardofflinepayment'}</button>
                                </span>
                            </div>
                        </div>
                    </div>
                    {/if}

                    {if $CCOFFLINE_REQUIREISSUER}
                    <div class="{if $CCOFFLINE_REQUIREDISSUER}required{/if} form-group row">
                        <label for="card[card_brand]" class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-4{else}col-md-3{/if} form-control-label {if $CCOFFLINE_REQUIREDISSUER}required{/if}">
                            {l s='Card issuer' mod='creditcardofflinepayment'}
                        </label>
                        <div class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-8{else}col-md-6{/if}">
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
                    <div class="{if $CCOFFLINE_REQUIREDPIN}required{/if} form-group row">
                        <label for="card[card_pin]" class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-4{else}col-md-3{/if} form-control-label {if $CCOFFLINE_REQUIREDPIN}required{/if}">
                            {l s='PIN card code' mod='creditcardofflinepayment'}
                        </label>
                        <div class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-8{else}col-md-6{/if}">
                            <input type="password" class="{if $CCOFFLINE_REQUIREDPIN}is_required{/if} validate form-control" data-validate="isNumber" name="card[card_pin]" value="{if isset($card.card_pin)}{$card.card_pin|escape:'htmlall':'UTF-8'}{/if}" maxlength="4"/>
                        </div>
                    </div>
                    {/if}

                    {if $CCOFFLINE_REQUIREEXP}
                    <div class="{if $CCOFFLINE_REQUIREDEXP}required{/if} form-group row">
                        <label for="card[card_expiry_month]" class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-4{else}col-md-3{/if} form-control-label {if $CCOFFLINE_REQUIREDEXP}required{/if}">
                            {l s='Card expiry date' mod='creditcardofflinepayment'}
                        </label>
                        <div class="{if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}col-md-8{else}col-md-9{/if}">
                            <div class="col-xs-4 col-md-3 month-input">
                                {if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}
                                    <input type="text" class="validate form-control" data-validate="isNumber" name="card[card_expiry_month]" value="{if isset($card.card_expiry_month)}{$card.card_expiry_month|escape:'htmlall':'UTF-8'}{/if}" placeholder="{l s='MM' mod='creditcardofflinepayment'}" />
                                {else}
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
                                {/if}
                            </div>

                            <div class="col-xs-4 col-md-3 year-input">
                                {if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}
                                    <input type="text" class="validate form-control" data-validate="isNumber" name="card[card_expiry_year]" value="{if isset($card.card_expiry_year)}{$card.card_expiry_year|escape:'htmlall':'UTF-8'}{/if}" placeholder="{l s='YY' mod='creditcardofflinepayment'}" />
                                {else}
                                    <select name="card[card_expiry_year]" class="form-control" autocomplete="cc-exp-year">
                                        <option value="-" {if isset($card.card_expiry_year)}{if $card.card_expiry_year == "" || $card.card_expiry_year == "-"} selected="selected"{/if}{/if}>{l s='YY' mod='creditcardofflinepayment'}</option>
                                        {*Function to get a select with the current year and X more *}
                                        {for $i=$smarty.now|date_format:"%y" to ($smarty.now|date_format:"%y")+($CCOFFLINE_YEARS -1)}
                                            <option value="{$i|escape:'htmlall':'UTF-8'}" {if isset($card.card_expiry_year)}{if $card.card_expiry_year == $i}selected="selected"{/if}{/if}>{$i|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
                                        {/for}
                                    </select>
                                {/if}
                            </div>

                        </div>
                    </div>
                    {/if}
                </div>

                {if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}
                    <div class="col-sm-6">
                        <div class="card-wrapper"></div>
                    </div>
                {/if}
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-xs-12">
                    <p class="info-title">
                        <span><strong>{l s='Confirm your order of %s by clicking the button "Confirm my order".' sprintf=[$total] mod='creditcardofflinepayment'}</strong></span>
                    </p>
                </div>
            </div>
        </form>

        <div class="clearfix"></div>

        <div class="cart_navigation">
            <a href="{$link->getPageLink('order.php', true, NULL, "step=3")|escape:'htmlall':'UTF-8'}" class="btn btn-secondary btn-default button button-small button_large hideOnSubmit"><span class="visible-sm visible-md visible-lg hidden-sm-down"><i class="icon-chevron-left"></i>{l s='Other payment methods' mod='creditcardofflinepayment'}</span><span class="visible-xs hidden-xs-up"><i class="icon-chevron-left"></i>{l s='Back' mod='creditcardofflinepayment'}</span></a>

            <button type="button" class="btn btn-primary btn-default button button-medium hideOnSubmit pay-button pull-xs-right" onclick="processing();"><span>{l s='Confirm my order' mod='creditcardofflinepayment'}</span></button>
        </div>

        <p id="card-processing" class="hidden" hidden>
            <em>{l s='Processing...' mod='creditcardofflinepayment'}</em>&nbsp;&nbsp;&nbsp;<img src="{$cc_path|escape:'htmlall':'UTF-8'}views/img/loadingAnimation.gif" width="208" height="13" vertical-align="middle" />
        </p>
    </div>
    <script type="text/javascript">
    //< ![CDATA[
        var cc_path = "{$cc_path|escape:'htmlall':'UTF-8'}";
        var card_number = "{if isset($card.card_number)}{$card.card_number|escape:'htmlall':'UTF-8'}{/if}";
        var cardholder_name = "{if isset($card.cardholder_name)}{$card.cardholder_name|escape:'htmlall':'UTF-8'}{else}{$customer['firstname']|escape:'htmlall':'UTF-8'} {$customer['lastname']|escape:'htmlall':'UTF-8'}{/if}";
        var card_cvv = "{if isset($card.card_cvv)}{$card.card_cvv|escape:'htmlall':'UTF-8'}{/if}";
        var CCOFFLINE_PAYMENT_STYLE = {if (isset($CCOFFLINE_PAYMENT_STYLE) && $CCOFFLINE_PAYMENT_STYLE)}1{else}0{/if};
    //]]>
    </script>
{/block}
