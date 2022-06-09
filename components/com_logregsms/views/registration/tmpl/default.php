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

$email_required = $this->params->get('is_email_required', "1");
$session = JFactory::getSession();
$referer = $session->get('smsregReferer', '');
?>

<div id="logregsms" class="registration-form col-lg-12 col-md-12 col-sm-12 col-xs-12">	
	<form action="<?php echo JRoute::_('index.php?option=com_logregsms&task=registration.step3'); ?>" method="post" name="step2form" id="step2form" onSubmit="return ValidationRegistrationForm()"> 
	
		<div class="form-group">
			<label for="username">نام کاربری * </label>
			<input type="text" name="username" required class="form-control" id="username" value="<?php echo $this->mobile;?>" readonly disabled />
		</div>
		
		<div class="form-group">
			<label for="name">نام * </label>
			<input type="text" name="name" required class="form-control" id="name" placeholder="به فارسی وارد کنید"/>
		</div>
		
		<?php if($email_required == "1" || $email_required == "2") : ?>
			<div class="form-group">
				<label for="email"> نشانی ایمیل <?php echo $email_required == "1" ? "*" : ""; ?> </label>
				<input type="text" id="email" name="email" <?php echo $email_required == "1" ? 'required="required"' : ""; ?> class="form-control" value="<?php echo $email;?>" />
			</div>
		<?php endif; ?>
		
		<?php if(!empty($this->fields)) { ?>
			<?php $js = ""; ?>
			<?php foreach ($this->fields as $key => $value) { ?>
				<?php if($value->fieldname == "mobile" || $value->fieldname == "cellphone") { ?>
					<?php $value->setValue($this->mobile); ?>
					<?php $value->readonly = true; ?>
					<?php $value->hidden = true; ?>
				<?php } ?>
				<?php if ($value->hidden) { ?>
					<div class="form-group" style="display: none;">
						<?php echo $value->input; ?>
					</div>
				<?php } else { ?>
					<div class="form-group">
						<?php echo $value->label; ?>
						<?php echo $value->input; ?>
					</div>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		
		<div class="form-group">
			<button type="submit" name="submform" id="subform" class="btn btn-primary" >
				 ثبت نام 
			</button>  
            <a class="btn btn-warning" href="<?php echo JRoute::_('index.php?option=com_logregsms&task=registration.clear'); ?>">
                 ورود با شماره موبایل جدید 
            </a> 
		</div>
		<input type="hidden" name="referer" value="<?php echo $referer; ?>">
	</form>
</div>