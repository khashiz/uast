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


/**
 * logregsms Table class.
 *
 * @package    logregsms
 * @subpackage Components
 */
class logregsmsTableSmsArchive extends JTable
{
    /**
     * Constructor.
     *
     * @param JDatabaseDriver &$db Database connector object
     */
    public function __construct(& $db)
    {
        parent::__construct('#__logregsms_smsarchives', 'id', $db);
    }
}
