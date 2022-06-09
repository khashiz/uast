<?php
/**
 * @package    logregsms
 * @subpackage C:
 * @author     Mohammad Hosein Mir {@link https://joomina.ir}
 * @author     Created on 22-Feb-2019
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$session = JFactory::getSession(); 
$referer = $session->get('smsregReferer', ''); //die(var_dump($referer));
if($referer){
    $referer = $referer;
}
else{
   $referer = $_SERVER['HTTP_REFERER'];
}
?>
<div id="logregsms" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 validation-mobile">
	<div class="col-lg-4 inside-wall">
		<form action="<?php echo JRoute::_('index.php?option=com_logregsms&task=validation_mobile.step1'); ?>" method="post" name="step1form" id="step1form" onSubmit="return ValidationMobileForm()">
			<div class="form-group">
				<label for="mobilenum">شماره موبایل</label>
				<input type="text" name="mobilenum" autocomplete="off" class="form-control" id="mobilenum" onKeyPress="numberValidate(event)" placeholder="نمونه: 09123456789" maxlength="11">
				<p class="help-block">لطفاً شماره موبایل خود را با 0 وارد کنید</p>
			</div>
			<button type="submit" class="btn btn-primary">ثبت و بررسی</button>
			<input type="hidden" name="referer" value="<?php echo $referer; ?>">
		</form>
	</div>
</div>
