<?php
/**
 * @file
 * @author Gurvindra
 * Contains \Drupal\amazons\Controller\QusController.
 */
namespace Drupal\questions\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Database;

/**
 * Provides route responses for the Example module.
 */
class QusNodeController extends ControllerBase {
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
   
   public function question_node() {
	  	$element[] = array(
                '#theme' => 'questions_node',
                '#content' => "hello",
        );
        
        // $element[] = array(
                // '#markup' => 'question_node',
        // );
		
	 	return $element;
	}
	
  }

?>