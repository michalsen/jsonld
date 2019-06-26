<?php
/**
  * @file
  * Contains \Drupal\JSONld\Form\JSONldForm
  */

/**
 * Add items AJAX lifted from https://gist.github.com/leymannx/72d41cf0baa4dee62d6ddc89bc7c7a5a
 *
 * An issue remains as if there are multiple sub pages populated, they won't show on page load.
 * Clicking the Add Page shows the content.
 *
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


  protected $number = 1;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'add_another_item';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#tree'] = TRUE;

    // Load data from jsonld
    $json_ld_load = \Drupal::state()->get('jsonld');
    $json_ld = json_decode($json_ld_load);

    // Count Sub Pages
    $count = 1;
    while (1){
      $object = 'jsonld_page_' . $count . '_path';
        if (strlen($json_ld->{$object}) < 1) {
          break;
        }
      $count++;
    }
    $count = $count - 1;

    // Form Elements

    $form['jsonld_name'] = array(
      '#title' => t('Name'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_name,
      '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_url'] = array(
      '#title' => t('URL'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_url,
      '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_logo'] = array(
      '#title' => t('Logo Path'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_logo,
      '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_telephone'] = array(
      '#title' => t('Telephone'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_telephone,
      '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_foundingdate'] = array(
      '#title' => t('Founding Date'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_foundingdate,
      '#required' => TRUE,
      // '#default_value' => 'test',
    );

    $form['jsonld_address_street'] = array(
      '#title' => t('Street Address'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_address_street,
      '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_address_locality'] = array(
      '#title' => t('Locality Address'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_address_locality,
      '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_address_region'] = array(
      '#title' => t('Region Address'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_address_region,
      '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_address_postalcode'] = array(
      '#title' => t('Postal Code'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_address_postalcode,
      '#required' => TRUE,
      // '#default_value' => 'test',
    );
    $form['jsonld_address_country'] = array(
      '#title' => t('Country'),
      '#type' => 'textfield',
      '#default_value' => $json_ld->jsonld_address_country,
      '#required' => TRUE,
      // '#default_value' => 'test',
    );


    // Sub Page Elementes

    $form['container'] = [
      '#type'       => 'container',
      '#attributes' => ['id' => 'my-container'], // CHECK THIS ID
    ];


    // Show Sub Pages w/content
    // if ($count > 1) {
    //   $this->number = $count;
    // }

    for ($i = 1; $i <= $this->number; $i++) {

    $path = 'jsonld_page_' . $i . '_path';
    $service = 'jsonld_page_' . $i . '_servicetype';
    $category = 'jsonld_page_' . $i . '_category';

    $default_path = isset($json_ld->{$path}) ? $json_ld->{$path} : '';
    $default_service = isset($json_ld->{$service}) ? $json_ld->{$service} : '';
    $default_category = isset($json_ld->{$category}) ? $json_ld->{$category} : '';

    $form['container']['open_' . $i] = [
        '#type' => 'markup',
        '#markup' => '<hr><strong>Page ' . $i . '</strong>',
      ];

    $form['container']['page_path_' . $i] = [
        '#type' => 'textfield',
        // '#title' => 'Path',
        '#attributes' => ['placeholder' => $this->t('Path')],
        '#default_value' => $default_path
      ];

    $form['container']['page_servicetype_' . $i] = [
        '#type' => 'textfield',
        // '#title' => 'serviceType',
        '#attributes' => ['placeholder' => $this->t('Service Type')],
        '#default_value' => $default_service
      ];
    $form['container']['page_category_' . $i] = [
        '#type' => 'textfield',
        // '#title' => 'category',
        '#attributes' => ['placeholder' => $this->t('Category')],
        '#default_value' => $default_category
      ];

    $form['container']['close_' . $i] = [
        '#type' => 'markup',
        '#markup' => '<hr>'
      ];


    }

    // Disable caching on this form.
    $form_state->setCached(FALSE);

    $form['container']['actions'] = [
      '#type' => 'actions',
    ];


    $form['container']['actions']['add_item'] = [
      '#type'   => 'submit',
      '#value'  => $this->t('Add Another Page'),
      '#submit' => ['::jsonld_add_item'],
      '#ajax'   => [
        'callback' => '::jsonld_ajax_callback',
        'wrapper'  => 'my-container', // CHECK THIS ID
      ],
    ];

    if ($this->number > 1) {

      $form['container']['actions']['remove_item'] = [
        '#type'                    => 'submit',
        '#value'                   => $this->t('Remove Latest Page'),
        '#submit'                  => ['::jsonld_remove_item'],
        '#limit_validation_errors' => [],
        '#ajax'                    => [
          'callback' => '::jsonld_ajax_callback',
          'wrapper'  => 'my-container',
        ],
      ];
    }

    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Submit')
      );


    return $form;
  }

  /**
   * Implements callback for Ajax event on color selection.
   *
   * @param array $form
   *   From render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Current state of form.
   *
   * @return array
   *   Color selection section of the form.
   */
  public function jsonld_ajax_callback($form, $form_state) {
    return $form['container'];
  }

  public function jsonld_add_item(array &$form, FormStateInterface $form_state) {

    $this->number++;
    $form_state->setRebuild();
  }

  public function jsonld_remove_item(array &$form, FormStateInterface $form_state) {

    if ($this->number > 1) {
      $this->number--;
    }
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

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

    $subs = $form_state->getValue(['container']);

    for ($i = 1; $i < 100; $i++) {
       if (strlen($subs['page_path_' . $i]) > 0) {
         $value['jsonld_page_' . $i . '_path'] = $subs['page_path_' . $i];
         $value['jsonld_page_' . $i . '_servicetype'] = $subs['page_servicetype_' . $i];
         $value['jsonld_page_' . $i . '_category'] = $subs['page_category_' . $i];
       }
    }

    \Drupal::state()->set('jsonld', json_encode($value));

  }

}


