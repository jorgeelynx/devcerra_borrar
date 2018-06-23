{*
    * We offer the best and most useful modules PrestaShop and modifications for your online store.
    *
    * We are experts and professionals in PrestaShop
    *
    * @author    PresTeamShop.com <support@presteamshop.com>
    * @copyright 2011-2017 PresTeamShop
    * @license   see file: LICENSE.txt
    * @category  PrestaShop
    * @category  Module
*}

{block name='step_payment'}

    {*{if $total_order <= 0}
        <span id="free_order" class="alert alert-warning col-xs-12 col-12 text-md-center">{l s='Free Order.' mod='onepagecheckoutps'}</span>
    {else if !$payment_options|count)}
        <p class="alert alert-warning col-xs-12 col-12 text-md-center">{l s='There are no payment methods available.' mod='onepagecheckoutps'}</p>
    {else if !$is_logged and !$is_guest and $payment_need_register and $CONFIGS.OPC_SHOW_BUTTON_REGISTER}
        <p class="alert alert-info col-xs-12 col-12 text-md-center">{l s='You need to enter your data and address, to see payment methods.' mod='onepagecheckoutps'}</p>
    {else}*}
    <div id="payment_method_container">
        {foreach from=$payment_options item="module_options" key="name_module"}
            {foreach from=$module_options item="option"}
                <div class="module_payment_container">
                    <div class="row pts-vcenter" for="{$option.action}">
                        <div class="payment_input col-xs-1 col-1">
                            <input type="radio" id="module_payment_{$option.id_module_payment}_0" name="method_payment" class="payment_radio not_unifrom not_uniform" value="{$name_module}">
                            <input type="hidden" id="url_module_payment_{$option.id_module_payment}" value="{$option.action}">
                        </div>
                        {if !empty($option.logo) and $CONFIGS.OPC_SHOW_IMAGE_PAYMENT}
                            <div class="payment_image col-xs-3 col-3">
                                <img src="{$option.logo}" title="{$option.call_to_action_text}" class="img-thumbnail {$name_module}">
                            </div>
                        {/if}
                        <div class="payment_content {if !empty($option.logo)}col-xs-8 col-8{else}col-xs-11 col-11{/if}">
                            <span>
                                {if isset($option.title_opc)}
                                    {$option.title_opc}
                                {else}
                                    {$option.call_to_action_text}
                                {/if}
                            </span>
                            {if isset($option.description_opc)}
                                <p>
                                    {$option.description_opc}
                                </p>
                            {/if}
                        </div>
                    </div>
                    {if $CONFIGS.OPC_SHOW_DETAIL_PAYMENT}
                        {if $option.additionalInformation}
                            <div id="payment_content_html_{$option.id}" class="payment_content_html hidden">
                                {$option.additionalInformation nofilter}
                            </div>
                        {/if}
                    {/if}
                    <div
                        id="pay-with-{$option.id}-form"
                        class="js-payment-option-form {if $option.id != $selected_payment_option} ps-hidden {/if}"
                    >
                        {if $option.form}
                            {$option.form nofilter}
                        {else}
                            <form id="payment-form" method="POST" action="{$option.action nofilter}">
                                {foreach from=$option.inputs item=input}
                                    <input type="{$input.type}" name="{$input.name}" value="{$input.value}">
                                {/foreach}
                                <button style="display:none" id="pay-with-{$option.id}" type="submit"></button>
                            </form>
                        {/if}
                    </div>
                </div>
            {/foreach}
        {foreachelse}
            <p class="alert alert-danger">
                {l s='Unfortunately, there are no payment method available.' mod='onepagecheckoutps'}
            </p>
        {/foreach}
    </div>

{*    {/if}*}
{/block}