<?php

/**
 * @file
 * Contains \Drupal\mymodule\Form\FormTest.
 */
namespace Drupal\JSONld\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;

/**
  *  Provides SNTrack Email form
  */
class JSONldForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mymodule_form_test';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $item_id = NULL) {


    // Load data from jsonld
  $jsonld_query = db_select('jsonld', 't')
       ->fields('t', array('jsonld'))
       ->execute()
       ->fetchObject();

  $json_ld = json_decode($jsonld_query->jsonld);


    // Disable caching for the form
    $form['#cache'] = ['max-age' => 0];

    // Do not flatten nested form fields
    $form['#tree'] = TRUE;

    $form['field_container'] = array(
      '#type' => 'container',
      '#weight' => 80,
      '#tree' => TRUE,
      // Set up the wrapper so that AJAX will be able to replace the fieldset.
      '#prefix' => '<div id="js-ajax-elements-wrapper">',
      '#suffix' => '</div>',
    );

    $form['jsonld_name'] = array(
      '#title' => t('Name'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_name,
      // '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_url'] = array(
      '#title' => t('URL'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_url,
      // '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_logo'] = array(
      '#title' => t('Logo Path'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_logo,
      // '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_telephone'] = array(
      '#title' => t('Telephone'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_telephone,
      // '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_foundingdate'] = array(
      '#title' => t('Founding Date'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_foundingdate,
      // '#required' => TRUE,
      // '#default_value' => 'test',
    );

    $form['jsonld_address_street'] = array(
      '#title' => t('Street Address'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_address_street,
      // '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_address_locality'] = array(
      '#title' => t('Locality Address'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_address_locality,
      // '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_address_region'] = array(
      '#title' => t('Region Address'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_address_region,
      // '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_address_postalcode'] = array(
      '#title' => t('Postal Code'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_address_postalcode,
      // '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_address_country'] = array(
      '#title' => t('Country'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_address_country,
      // '#required' => TRUE,
      // '#default_value' => 'test',
    );

    $count = 1;
    while (1){
      $object = 'jsonld_page_' . $count . '_path';
        if (strlen($json_ld->{$object}) < 1) {
          break;
        }
      $count++;
    }
    $count = $count - 1;


    if ($form_state->get('field_deltas') == '') {
      $form_state->set('field_deltas', range(1, $count));
    }




    $field_count = $form_state->get('field_deltas');

    foreach ($field_count as $delta) {

    $path = 'jsonld_page_' . $delta . '_path';
    $service = 'jsonld_page_' . $delta . '_servicetype';
    $category = 'jsonld_page_' . $delta . '_category';

    $default_path = isset($json_ld->{$path}) ? $json_ld->{$path} : '';
    $default_service = isset($json_ld->{$service}) ? $json_ld->{$service} : '';
    $default_category = isset($json_ld->{$category}) ? $json_ld->{$category} : '';




    $form['field_container']['open_' . $delta] = [
        '#type' => 'markup',
        '#markup' => '<hr><strong>Page ' . $delta . '</strong>',
      ];
    $form['field_container']['page_path_' . $delta] = [
        '#type' => 'textfield',
        // '#title' => 'Path',
        '#attributes' => ['placeholder' => $this->t('Path')],
        '#default_value' => $default_path
      ];

    $form['field_container']['page_servicetype_' . $delta] = [
        '#type' => 'textfield',
        // '#title' => 'serviceType',
        '#attributes' => ['placeholder' => $this->t('Service Type')],
        '#default_value' => $default_service
      ];
    $form['field_container']['page_category_' . $delta] = [
        '#type' => 'textfield',
        // '#title' => 'category',
        '#attributes' => ['placeholder' => $this->t('Category')],
        '#default_value' => $default_category
      ];
    $form['field_container']['close_' . $delta] = [
        '#type' => 'markup',
        '#markup' => '<hr>'
      ];

      // $form['field_container'][$delta]['remove_name'] = array(
      //   '#type' => 'submit',
      //   '#value' => t('-'),
      //   '#submit' => array('::mymoduleAjaxExampleAddMoreRemove'),
      //   '#ajax' => array(
      //     'callback' => '::mymoduleAjaxExampleAddMoreRemoveCallback',
      //     'wrapper' => 'js-ajax-elements-wrapper',
      //   ),
      //   '#weight' => -50,
      //   '#attributes' => array(
      //     'class' => array('button-small'),
      //   ),
      //   '#name' => 'remove_name_' . $delta,
      // );
    }

    $form['field_container']['add_name'] = array(
      '#type' => 'submit',
      '#value' => t('Add one more'),
      '#submit' => array('::mymoduleAjaxExampleAddMoreAddOne'),
      '#ajax' => array(
        'callback' => '::mymoduleAjaxExampleAddMoreAddOneCallback',
        'wrapper' => 'js-ajax-elements-wrapper',
      ),
      '#weight' => 100,
    );



    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Submit')
      );


    return $form;
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  function mymoduleAjaxExampleAddMoreRemove(array &$form, FormStateInterface $form_state) {
    // Get the triggering item
    $delta_remove = $form_state->getTriggeringElement()['#parents'][1];

    // Store our form state
    $field_deltas_array = $form_state->get('field_deltas');

    // Find the key of the item we need to remove
    $key_to_remove = array_search($delta_remove, $field_deltas_array);

    // Remove our triggered element
    unset($field_deltas_array[$key_to_remove]);

    // Rebuild the field deltas values
    $form_state->set('field_deltas', $field_deltas_array);

    // Rebuild the form
    $form_state->setRebuild();

    // Return any messages set
    drupal_get_messages();
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return mixed
   */
  function mymoduleAjaxExampleAddMoreRemoveCallback(array &$form, FormStateInterface $form_state) {
    return $form['field_container'];
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  function mymoduleAjaxExampleAddMoreAddOne(array &$form, FormStateInterface $form_state) {

    // Store our form state
    $field_deltas_array = $form_state->get('field_deltas');

    // check to see if there is more than one item in our array
    if (count($field_deltas_array) > 0) {
      // Add a new element to our array and set it to our highest value plus one
      $field_deltas_array[] = max($field_deltas_array) + 1;
    }
    else {
      // Set the new array element to 0
      $field_deltas_array[] = 0;
    }

    // Rebuild the field deltas values
    $form_state->set('field_deltas', $field_deltas_array);

    // Rebuild the form
    $form_state->setRebuild();

    // Return any messages set
    drupal_get_messages();
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return mixed
   */
  function mymoduleAjaxExampleAddMoreAddOneCallback(array &$form, FormStateInterface $form_state) {
    return $form['field_container'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $value['jsonld_name'] = $form_state->getValue('jsonld_name');
    $value['jsonld_url'] = $form_state->getValue('jsonld_url');
    $value['jsonld_logo'] = $form_state->getValue('jsonld_logo');
    $value['jsonld_telephone'] = $form_state->getValue('jsonld_telephone');
    $value['jsonld_foundingdate'] = $form_state->getValue('jsonld_foundingdate');
    $value['jsonld_address_street'] = $form_state->getValue('jsonld_address_street');
    $value['jsonld_address_locality'] = $form_state->getValue('jsonld_address_locality');
    $value['jsonld_address_region'] = $form_state->getValue('jsonld_address_region');
    $value['jsonld_address_postalcode'] = $form_state->getValue('jsonld_address_postalcode');
    $value['jsonld_address_country'] = $form_state->getValue('jsonld_address_country');

    $subs = $form_state->getValue(['field_container']);

    for ($i = 1; $i < 100; $i++) {
       if (strlen($subs['page_path_' . $i]) > 0) {
         $value['jsonld_page_' . $i . '_path'] = $subs['page_path_' . $i];
         $value['jsonld_page_' . $i . '_servicetype'] = $subs['page_servicetype_' . $i];
         $value['jsonld_page_' . $i . '_category'] = $subs['page_category_' . $i];
       }
    }


     $db = \Drupal::database();
     $db->truncate('jsonld')->execute();

     $db->insert('jsonld')
        ->fields([
          'jsonld',
        ])
        ->values(array(
          json_encode($value),
        ))
        ->execute();
  }

}
