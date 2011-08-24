<?php

$plugin = array(
  'schema' => 'azure_storage',

  // The access permission to use.
  'access' => 'administer site configuration',

  // You can actually define large chunks of the menu system here. Nothing
  // is required here. If you leave out the values, the prefix will default
  // to admin/structure and the item will default to the plugin name.
  'menu' => array(
    'menu prefix' => 'admin/config/media',
    'menu item' => 'azure-storage',
    // Title of the top level menu. Note this should not be translated,
    // as the menu system will translate it.
    'menu title' => 'Azure storage', 
    // Description of the top level menu, which is usually needed for
    // menu items in an administration list. Will be translated
    // by the menu system.
    'menu description' => 'Administer Azure Blob Storage configurations.',
  ),

  'title singular' => t('azure storage'),
  'title singular proper' => t('Azure storage'),
  'title plural' => t('azure storages'),
  'title plural proper' => t('Azure storages'),

  'form' => array(
    'settings' => 'azure_storage_export_ui_form',
    'validate' => 'azure_storage_export_ui_form_validate',
    'submit' => 'azure_storage_export_ui_form_submit',
  ),

);

function azure_storage_export_ui_form(&$form, &$form_state) {
  $form['info']['name']['#description'] = t('The unique ID for this azure storage. Will also be used as the URL scheme for the files.');

  $form['account'] = array(
    '#type' => 'textfield',
    '#title' => t('Account Name'),
    '#description' => t('The name of the Azure Storage account.'),
    '#default_value' => $form_state['item']->account,
    '#required' => TRUE,
  );
  $form['blob_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Blob URL'),
    '#description' => t('The URL attached to the Azure Blob Storage account, generally <em>blob.core.windows.net</em>.'),
    '#default_value' => $form_state['item']->blob_url ? $form_state['item']->blob_url : 'blob.core.windows.net',
    '#field_prefix' => 'http://',
    '#required' => TRUE,
  );
  $form['primary_key'] = array(
    '#type' => 'textfield',
    '#title' => t('Primary access key'),
    '#description' => t('The primary access key attached to this Azure Storage account.'),
    '#default_value' => $form_state['item']->primary_key,
    '#required' => TRUE,
  );
  $form['blob_container'] = array(
    '#type' => 'textfield',
    '#title' => t('Blob container'),
    '#description' => t('The container attached to this Azure Storage account.'),
    '#default_value' => $form_state['item']->blob_container,
    '#required' => TRUE,
  );
  $form['blob_custom_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Blob Custom URL'),
    '#description' => t('The custom URL to use to serve files from this storage. Leave empty to use the default URL.'),
    '#default_value' => $form_state['item']->blob_custom_url,
    '#field_prefix' => 'http://',
  );
}

function azure_storage_export_ui_form_validate(&$form, &$form_state) {
  // Validate the name is valid as a Stream wrapper.
  $name = $form_state['values']['name'];
  if (!preg_match('/^[a-z0-9-_]+$/', $name)) {
    form_error($form['info']['name'], t('Only letters, figures and the underscore and dash signs are allowed in scheme names.'));
  }

  // When creating a new wrapper, validate that it doesn't override an existing wrapper.
  if (empty($form_state['item']->id)) {
    $streams = stream_get_wrappers();
    if (in_array($name, $streams)) {
      form_error($form['info']['name'], t('This wrapper name already exists.'));
    }
  }
}

function azure_storage_export_ui_form_submit(&$form, &$form_state) {
  foreach (array('account', 'blob_url', 'primary_key', 'blob_container') as $key) {
    $form_state['item']->{$key} = $form_state['values'][$key];
  }
}
