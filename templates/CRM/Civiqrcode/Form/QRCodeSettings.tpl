{* HEADER *}

<div class="crm-form-block crm-search-form-block">
  {* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}
  {foreach from=$elementNames item=elementName}
    <div class="crm-section">
      <div class="label">{$form.$elementName.label}</div>
      <div class="content">
      {$form.$elementName.html}
      <br>
      <small>{$helpText.$elementName}</small>
      </div>
      <div class="clear"></div>
    </div>
  {/foreach}
  
  {* FOOTER *}
  <div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
  <a href="{$viewLink}" class="button"> View QrCode Tokens </a>
  </div>
</div>


