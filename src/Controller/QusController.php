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
class QusController extends ControllerBase {
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
   
   public function questions_list() {
	  	
		//Function to get the time ago
		function time_elapsed_string($ptime)
		{
		    $etime = time() - $ptime;
		
		    if ($etime < 1)
		    {
		        return '0 seconds';
		    }
		
		    $a = array( 365 * 24 * 60 * 60  =>  'year',
		                 30 * 24 * 60 * 60  =>  'month',
		                      24 * 60 * 60  =>  'day',
		                           60 * 60  =>  'hour',
		                                60  =>  'minute',
		                                 1  =>  'second'
		                );
		    $a_plural = array( 'year'   => 'years',
		                       'month'  => 'months',
		                       'day'    => 'days',
		                       'hour'   => 'hours',
		                       'minute' => 'minutes',
		                       'second' => 'seconds'
		                );
		
		    foreach ($a as $secs => $str)
		    {
		        $d = $etime / $secs;
		        if ($d >= 1)
		        {
		            $r = round($d);
		            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
		        }
		    }
		}

	  	$config = $this->config('questions.settings');
		global $base_url;
		$account = \Drupal::currentUser();
		$content_array = array();
		if ($account->id()) {
			$content_array["add_url"] = $base_url ."/node/add/question/";
		 }
		
		//$questions_count_query = db_query("SELECT `nid` FROM `node` where `type`='". $config->get('content_type') . "'")->fetchAll();
		//$que_count = count($questions_count_query);
		
		$questions_count_query = db_query("SELECT node.nid
		FROM node
		JOIN node_field_data
		ON node.nid=node_field_data.nid
		WHERE node.type='". $config->get('content_type') . "'
		AND node_field_data.status > 0
		")->fetchAll();
		$que_count = count($questions_count_query);
		
		
		// $to is the Number of itmes in single page
		$to = 10;
		
		if(isset($_GET['page_no']) && $_GET['page_no'] > 0){
			$from = $to*($_GET['page_no'] - 1);
		}else{
			$from = 0;
		}
		
		//$questions_query = db_query("SELECT `nid` FROM `node` WHERE `type`='". $config->get('content_type') ."' ORDER BY `nid` DESC LIMIT ". $from .",". $to);
		//$questions_query = db_query("SELECT `nid` FROM `node` WHERE `type`='". $config->get('content_type') ."' ORDER BY `nid` DESC");
		$questions_query = db_query("SELECT node.nid
		FROM node
		JOIN node_field_data
		ON node.nid=node_field_data.nid
		WHERE node.type='". $config->get('content_type') . "' 
		AND node_field_data.status > 0
		ORDER BY `nid` DESC LIMIT ". $from .",". $to);
		
		$all_data = $questions_query->fetchAll();
		if(count($all_data) > 0){
			foreach($all_data as $key=>$value){
				$node =  node_load($value->nid, $reset = FALSE);
				$status = $node->status->value;
				if($status > 0){	
					$question_title = $node->title->value;
					$user_id = $node->uid->target_id;
					$submitted_date = date('Y-m-d H:i:s', $node->created->value);
					$user = user_load($node->uid->target_id, $reset = FALSE);
					$user_name = $user->name->value;
					$submitted_time_ago =  time_elapsed_string(strtotime($submitted_date));
					
					$ans_cout_query = db_query("SELECT `cid` FROM `comment_field_data` where `pid` IS NULL AND `entity_id`='". $value->nid ."' AND `status`='1'");
					$ans_cout = 0;
					foreach($ans_cout_query as $key1=>$value1){
						$ans_cout++;
					}		
					$content_array["list"][] = array("vote"=>0,"answer"=>$ans_cout,"nodeurl"=>$base_url. "/node/". $value->nid,"nodetitle"=>$question_title,"answeredbyurl"=>$base_url. "/user/". $user_id,"answeredbyname"=>$user_name,"time"=>$submitted_time_ago);
				}
			}
		}else{
			return drupal_set_message(t("Page not found."), 'error');
		}
		$round = round($que_count/$to);
		$actual = $que_count/$to;
		if($actual > $round){
			$loop_count = $round + 1;
		}else{
			$loop_count = $round;
		}
		for($i=1;$i<=$loop_count;$i++){
			$pages[] = array("title"=>$i,"url"=>$base_url ."/questions/?page_no=". $i);
		}
		$content_array["pages"] = $pages;
		$prev = $_GET['page_no'] - 1;
		$next = $_GET['page_no'] + 1;
	
		$content_array["prev"] = $base_url ."/questions/?page_no=". ($prev);
		$content_array["next"] = $base_url ."/questions/?page_no=". ($next);
		$content_array["current"] = $_GET['page_no'];
		$content_array["page_count"] = $loop_count;
		// echo "<pre>";
		// print_r($content_array);
		// exit;
		
		
		$element['#attached']['library'][]='questions/questions-validation';
 		$element[] = array(
                '#theme' => 'questions_list',
                '#content' => $content_array,
        );
	 	return $element;
	}
	
  }

?>