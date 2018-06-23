{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!-- MODULE wpintops -->
<div class="blockcategories_footer footer-block col-xs-12 col-sm-4">
    <h4 class="title_block">{l s='Latest post' mod='wpintops'}</h4>
    <div style="width:100%" class="category_footer">
	<div class="list">
            {if isset($posts) AND $posts}
            <ul class="block_content">
                {foreach from=$posts item=post name=wpposts}
                    <li><a title="{$post.post_title|escape:'html':'UTF-8'}" href="{$post.url|escape:'html':'UTF-8'}"{if ($target == 1)} target="_blank"{/if}>{$post.post_title|escape:'html':'UTF-8'}</a></li>			
                {/foreach}
            </ul>
            {/if}
	</div>
    </div>
<br class="clear">
</div>
<!-- /MODULE wpintops -->
