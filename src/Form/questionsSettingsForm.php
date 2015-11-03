<?php
/**
 * @file
 * Contains \Drupal\questions\hellquestionsSettingsFormoSettingsForm
 */
namespace Drupal\questions\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure hello settings for this site.
 */
class questionsSettingsForm extends ConfigFormBase {
  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'questions_admin_settings';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'questions.settings',
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('questions.settings');
	//echo "<pre>";
	// print_r(node_type_get_types());
	// exit;
	
	foreach (node_type_get_types() as $key => $value) {
	  	$options[$key] = $value->label();
	}	
	$form['qus_content_type'] = array(
	      '#type' => 'select',
	      '#default_value' => array( $config->get('content_type') ),
	      '#options' => $options,
	      '#title' => $this->t('Select content type'),
	    );
    return parent::buildForm($form, $form_state);
  }

  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('questions.settings');
	$config->set('content_type', trim($form_state->getValue('qus_content_type')))->save();
	parent::submitForm($form, $form_state);
  }
}
?>