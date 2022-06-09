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

date_default_timezone_set('Iran');
$session = JFactory::getSession();
$code_session = $session->get('smsregCode', '');
$mobile = $session->get('smsregMobile', '');
$referer = $session->get('smsregReferer', '');
$helper = new LRSHelper();
$valmob_Itemid = $helper::getOneMenu('validation_mobile');

if(empty($code_session)){
    $helper::$_app->enqueueMessage('همه session ها منقضی شده اند', 'error');
    $app->redirect(JRoute::_('index.php?option=com_logregsms&view=validation_mobile&Itemid='.$valmob_Itemid));
}

$params = $helper::getParams(); 
$resend = (int)$params->get('resend', ''); 
$resend_second = $resend*60; 

$confirm = LRSHelper::getConfirm('', $code_session, -1); 
$time = $confirm->time;
$current = time();
$resend_time = strtotime($time) + $resend_second;


$holding = $resend_time - $current ;

if($current > $resend_time){
    $can_send_display = 'style="display:block"';
    $holding_display = 'style="display:none"';
}
else{
    $can_send_display = 'style="display:none"';
    $holding_display = 'style="display:block"';	
} 
?>
<div id="logregsms" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 validation-code">
	<div class="col-lg-4 inside-wall">
		<form action="<?php echo JRoute::_('index.php?option=com_logregsms&task=validation_code.step2'); ?>" method="post" name="step2form" id="step2form" onSubmit="return ValidationCodeForm()">
			<div class="form-group">
				<label for="codenum">کد تاییدیه</label>
				<input type="text" autocomplete="off" name="codenum" class="form-control" id="codenum" onKeyPress="numberValidate(event)" placeholder="">
				<p class="help-block">لطفا کد ارسال شده به شماره موبایل خود را در قسمت بالا وارد کنید</p>
			</div> 
            <div class="buttons_row">

                <button type="submit" class="btn btn-primary">بررسی کد تاییدیه</button>

                <div id="can_send" <?php echo $can_send_display?> >
                    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_logregsms&view=validation_code&task=validation_code.sendCode'); ?>">ارسال مجدد کد</a>
                </div>

                <div id="holding" <?php echo $holding_display?> >
                    <div>ارسال مجدد کد</div>
                    <div id="timer_div"></div>
                </div>
            </div>
            <input type="hidden" name="referer" value="<?php echo $referer; ?>">
		</form>
	</div>
</div> 
<script>
var timeleft = <?php echo $holding;?>;
var elem = document.getElementById('timer_div');
var timerId = setInterval(countdown, 1000);

function countdown() {
    if (timeleft == -1) {  
        clearTimeout(timerId);
        doSomething();
    } else {
        elem.innerHTML = timeleft + ' ثانیه باقیمانده';
        timeleft--;
    }
}

function doSomething() {
	document.getElementById('can_send').style.display="block";
	document.getElementById('holding').style.display="none";
}
</script>
<style>
.buttons_row > * {
    display: inline-block;
    float: right;
    margin-left: 10px;
}
#holding > * {
    float: right;
    margin-left: 10px;
    padding: 5px;
}
#timer_div {
    color: #dc3545;
}
</style>

