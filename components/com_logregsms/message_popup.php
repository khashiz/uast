<?php
/**
 * @package    logregsms
 * @subpackage C:
 * @author     Mohammad Hosein Miri {@link https://joomina.ir}
 * @author     Created on 22-Feb-2019
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

?>

<div id="MessageP" class="remodal" data-remodal-id="MessageP">
  <div class="remodal_header">
		<span class="lnr lnr-map-marker"></span>
		<h3 id="popup_header"></h3>
		<button data-remodal-action="close" class="remodal-close"></button>
	</div>
	<div class="remodal_body">

	  <!-- body text -->
	  <p class="message_text"></p>
		
	  <button style="display: none;" data-remodal-action="confirm" id="messageNormalConfirmBtn" class="remodal-confirm"></button>
	  <button data-remodal-action="close" class="remodal-cancel">بستن</button>
	</div>
</div>