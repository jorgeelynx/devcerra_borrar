{*
 * Orderfiles Prestashop module
 *
 * @author    Wiktor Koźmiński
 * @copyright 2017-2017 Wiktor Koźmiński
*}

{extends file='customer/page.tpl'}

{block name='page_title'}{l s='My order files' mod='orderfiles'}{/block}

{block name='page_content'}

{if isset($uploadsuccess) && $uploadsuccess}
    <div class="alert alert-success">
        <p>{l s='File was successfully sent to shop server' mod='orderfiles'}</p>
    </div>
{/if}

{if isset($removesuccess) && $removesuccess}
    <div class="alert alert-success">
        <p>{l s='File was successfully removed from shop server' mod='orderfiles'}</p>
    </div>
{/if}

<h6>{l s='Here are the files attached to your orders.' mod='orderfiles'}</h6>
{if $orders && count($orders)}
<table class="table table-striped table-bordered table-labeled hidden-sm-down">
  <thead class="thead-default">
    <tr>
      <th>{l s='Order id' mod='orderfiles'}</th>
      <th>{l s='Date' mod='orderfiles'}</th>
      <th>{l s='Payment' mod='orderfiles'}</th>
      <th>{l s='Files' mod='orderfiles'}</th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$orders item=order}
    <tr>
      <td>#{$order.id_order|intval}</td>
      <td>{$order.date_add}</td>
      <td>{$order.payment|escape:'html':'UTF-8'}</td>
      <td>
          {if isset($order.attached_files) && count($order.attached_files)}
              {foreach from=$order.attached_files item=file name=filesLoop}
              {if not $file->isVisibleToCustomer}{continue}{/if}
              <div class="box" style="margin-bottom: 5px; padding: 5px 15px;">
              <p>
                  <strong>{$file->name|escape:'htmlall':'UTF-8'}</strong>
                  <span class="text-muted">{$file->crTimestamp|date_format:"%d-%m-%Y %H:%M:%S"|escape:'htmlall':'UTF-8'}</span>
              </p>
              <p>{$file->desc|escape:'htmlall':'UTF-8'}</p>
              <p>
                  <a target="_blank" href="{url entity=module name=orderfiles controller=CustomerFiles params=['get_file_id' => $file->getId()] }"><i class="icon icon-download"></i> {l s='Download' mod='orderfiles'}</a>
                  {if $file->isEditableByCustomer}
                  | <a onclick="return confirm('{l s='Are you sure you want to delete this file?' mod='orderfiles'}')" href="{url entity=module name=orderfiles controller=CustomerFiles params=['remove_file_id' => $file->getId()] }"><i class="icon icon-trash"></i> {l s='Delete' mod='orderfiles'}</a>
                  {/if}
              </p>
              </div>
              {/foreach}
          {else}
          {l s='No files attached to this order' mod='orderfiles'}
          {/if}
      </td>
    </tr>
  {/foreach}
  </tbody>
</table>
{else}
<p class="alert alert-warning">{l s='You have not placed any orders.' mod='orderfiles'}</p>
{/if}

{* File upload form *}
{if $orders && count($orders)}
    <div class="col-lg-12">
        <h1 class="page-subheading">{l s='Attach new file to your order' mod='orderfiles'}</h1>
        <p class="info-title">{l s='Please use form below to attach new file to one of your orders' mod='orderfiles'}</p>

        <form method="post" action="{url entity=module name=orderfiles controller=CustomerFiles}" enctype="multipart/form-data" class="form-horizontal">

            <div class="form-group row">
                <label class="col-lg-3 control-label">{l s='Attach to order:' mod='orderfiles'}: </label>
                <div class="col-lg-6">
                  <select name="file_order_id" class="form-control form-control-select">
                  {foreach from=$orders item=order}
                    <option value="{$order.id_order|intval}">{l s='Order' mod='orderfiles'} #{$order.id_order} {l s='placed at' mod='orderfiles'} {dateFormat date=$order.date_add full=0}</option>
                  {/foreach}
                  </select>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-3 control-label">{l s='File' mod='orderfiles'}: </label>
                <div class="col-lg-6">
                  <div style="display: inline-block;border: 1px dashed #aaa;padding: 4px;">
                    <input class="form-control" type="file" name="orderfile">
                  </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-lg-3 control-label">{l s='File description (optional)' mod='orderfiles'}: </label>
                <div class="col-lg-6">
                  <textarea name="file_desc" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary form-control-submit pull-xs-right">
                        <span><i class="icon-plus-sign"></i> {l s='Add file' mod='orderfiles'}</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
{/if}
{/block}