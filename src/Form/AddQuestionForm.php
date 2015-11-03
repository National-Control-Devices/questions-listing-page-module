<?php
/**
 * @file 
 * Contains \Drupal\questions\Form\AddQuestionForm.
 */
 
namespace Drupal\questions\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Session\AccountInterface;

/**
 * My Form.
 */
class AddQuestionForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'add_question_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
		$account = \Drupal::currentUser();
		if ($account->id()) {
			$form['title'] = array( 
				'#type' => 'textfield', 
				'#title' => t('Question Title'),
			);
			$form['details'] = array( 
				'#type' => 'textarea', 
				'#title' => t('Question Details'), 
			);
			  
			$form['actions'] = array(
				'#type' => 'actions'
			); 
			$form['actions']['submit'] = array( 
				'#type' => 'submit', 
				'#value' => t('Save'), 
			); 
			$form['#attached']['library'][]='questions/questions-validation';
		}else{
			global $base_url;
			$html = "<p>You are not logged in, please login to add a new question.</p><a href='". $base_url ."/user/login/'>Login</a> | <a href='". $base_url ."/user/register/'>Register</a>";
			$form = array(
		    	'#markup' => $html,
		    );
		}
		return $form; 
	} 
	
	/**
   * {@inheritdoc}
   */
	 public function validateForm(array &$form, FormStateInterface $form_state) {
		if (strlen($form_state->getValue('title')) == '') {
		   $form_state->setErrorByName('title', $this->t('Please enter title.'));
		}
		if (strlen($form_state->getValue('details')) == '') {
		  $form_state->setErrorByName('details', $this->t('Please enter details.'));
		}
	  }
	
	
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$config = $this->config('questions.settings');	
		$title = $form_state->getValue('title'); 
		$details = $form_state->getValue('details'); 
		$content_type = $config->get('content_type');
		if(!isset($content_type) || empty($content_type)){
			drupal_set_message(t('Please select the content type from settings.'), 'error');
			return;
		}
		$new_page_values = array();
		$new_page_values['type'] = $content_type;
		$new_page_values['title'] = $title;
		$new_page_values['body'] = $details;
		
		$new_page = entity_create('node', $new_page_values);
		echo $entity_added = $new_page->save();
		if( $entity_added > 0 ){
			drupal_set_message("Question added");
		}else{
			drupal_set_message(t("Failed to add the question"), 'error');
		}
		return;
	}
}
?>

