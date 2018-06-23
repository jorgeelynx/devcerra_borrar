{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
  <div id="hook-display-before-carrier">
    {$hookDisplayBeforeCarrier nofilter}
  </div>

  <div class="delivery-options-list">
    {if $delivery_options|count}
      <form
        class="clearfix"
        id="js-delivery"
        data-url-update="{url entity='order' params=['ajax' => 1, 'action' => 'selectDeliveryOption']}"
        method="post"
      >
        <div class="form-fields">
          {block name='delivery_options'}
            <div class="delivery-options">
              {foreach from=$delivery_options item=carrier key=carrier_id}
                  <div class="row delivery-option small-gutters align-items-center">
                    <div class="col col-auto">
                      <span class="custom-radio">
                        <input type="radio" name="delivery_option[{$id_address}]" id="delivery_option_{$carrier.id}" value="{$carrier_id}"{if $delivery_option == $carrier_id} checked{/if}>
                        <span></span>
                      </span>
                    </div>

                      {if $carrier.logo}
                          <div class="col col-auto carrier-logo">
                              <label for="delivery_option_{$carrier.id}"><img src="{$carrier.logo}" alt="{$carrier.name}" class="img-fluid" /></label>
                          </div>
                      {/if}

                      <div class="col col-auto carrier-name"><label for="delivery_option_{$carrier.id}">{$carrier.name} <br /> <span class="text-muted carrier-delay">{$carrier.delay}</span></label></div>
                      <div class="col text-right carrier-price pull-right">{$carrier.price}</div>


                  </div>
                  <div class="row carrier-extra-content"{if $delivery_option != $carrier_id} style="display:none;"{/if}>
                    {$carrier.extraContent nofilter}
                  </div>
              {/foreach}
            </div>
          {/block}
          <div class="order-options">
              <div id="delivery">
                  <label for="delivery_message">{l s='If you would like to add a comment about your order, please write it in the field below.' d='Shop.Theme.Checkout'}</label>
                  <textarea rows="2" cols="120" id="delivery_message" class="form-control" name="delivery_message">{if isset($delivery_message)}{$delivery_message}{/if}</textarea>
              </div>

              {if $recyclablePackAllowed}
                  <span class="custom-checkbox">
                <input type="checkbox" id="input_recyclable" name="recyclable" value="1" {if $recyclable} checked {/if}>
                <span><i class="fa fa-check checkbox-checked" aria-hidden="true"></i></span>
                <label for="input_recyclable">{l s='I would like to receive my order in recycled packaging.' d='Shop.Theme.Checkout'}</label>
              </span>
              {/if}


              {if $gift.allowed}
                  <div class="mt-3">
                  <span class="custom-checkbox">
                <input class="js-gift-checkbox" id="input_gift" name="gift" type="checkbox" value="1" {if $gift.isGift}checked="checked"{/if}>
                <span><i class="fa fa-check checkbox-checked" aria-hidden="true"></i></span>
                <label for="input_gift">{$gift.label}</label >
              </span>

                  <div id="gift" class="collapse{if $gift.isGift} in show{/if} mt-2">
                      <label for="gift_message" class="text-muted">{l s='If you\'d like, you can add a note to the gift:' d='Shop.Theme.Checkout'}</label>
                      <textarea rows="2" cols="120" id="gift_message" class="form-control" name="gift_message">{$gift.message}</textarea>
                  </div>
                  </div>
              {/if}

          </div>
        </div>
        <button type="submit" class="continue btn btn-primary btn-block btn-lg mt-3" name="confirmDeliveryOption" value="1">
          {l s='Continue' d='Shop.Theme.Actions'}
        </button>
      </form>
    {else}
      <p class="alert alert-danger">{l s='Unfortunately, there are no carriers available for your delivery address.' d='Shop.Theme.Checkout'}</p>
    {/if}
  </div>

  <div id="hook-display-after-carrier">
    {$hookDisplayAfterCarrier nofilter}
  </div>

  <div id="extra_carrier"></div>
{/block}
