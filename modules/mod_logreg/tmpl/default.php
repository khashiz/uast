<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_logreg
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$session = JFactory::getSession();
$app = JFactory::getApplication();
$callback = JURI::current();
$option = $app->input->getString('option', '');
if($option == "com_logregsms") {
    $callback = $session->get('smsregReferer', '');
}
?>
<div class="mod_logreg">

    <div class="validation-mobile">
        <form action="<?php echo JRoute::_('index.php?option=com_logregsms&task=validation_mobile.step1'); ?>" method="post" name="step1form" id="step1form" onSubmit="return ValidationMobileModuleForm()">
            <div class="form-group">
                <label for="mobilenum">لطفاً شماره موبایل خود را وارد کنید.</label>
                <input type="text" name="mobilenum" class="form-control" id="mobilenum_m" onKeyPress="numberValidate(event)" placeholder="نمونه: 09123456789" autocomplete="off" maxlength="11">
    
            </div>
            <button type="submit" class="btn btn-primary">ثبت و بررسی</button>
            <input type="hidden" name="referer" value="<?php echo $callback; ?>">
        </form>
    </div>
</div>