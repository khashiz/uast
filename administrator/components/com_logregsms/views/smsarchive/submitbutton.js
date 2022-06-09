/**
 * @package    logregsms
 * @subpackage JavaScript
 * @author     Mohammad Hosein Mir {@link https://joomina.ir}
 * @author     Created on 22-Feb-2019
 * @license    GNU/GPL
 */


Joomla.submitbutton = function (task) {
    if (task == '')
    {
        return false;
    }

    var isValid = true;

    var action = task.split('.');

    if (action[1] != 'cancel' && action[1] != 'close')
    {
        var forms = $$('form.form-validate');

        for (var i = 0; i < forms.length; i++)
        {
            if (!document.formvalidator.isValid(forms[i]))
            {
                isValid = false;

                break;
            }
        }
    }

    if (isValid)
    {
        Joomla.submitform(task, document.id('smsarchive-form'));

        return true;
    }

    alert(Joomla.JText._('COM_LOGREGSMS_LOGREGSMS_ERROR_UNACCEPTABLE'));

    return false;
};
