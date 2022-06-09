<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JHtml::_('jquery.framework');

$app  = JFactory::getApplication();
$user = JFactory::getUser();
// Getting params from template
$params = $app->getTemplate(true)->params;
$menu = $app->getMenu();
$active = $menu->getActive();
$pageparams = $menu->getParams( $active->id );
$pageclass = $pageparams->get( 'pageclass_sfx' );
// Detecting Active Variables
$itemid   = $app->input->getCmd('Itemid', '');


?>
<div class="uk-grid-divider uk-flex-center" data-uk-grid>
    <div class="uk-width-1-1 uk-width-2-3@m"><?php echo RSFormProHelper::displayForm($this->formId); ?></div>
    <?php if ($this->formId == 3) { ?>
        <div class="uk-width-1-1 uk-width-expand@m">
        <div>
            <div>
                <div class="uk-child-width-1-1" data-uk-grid>
                    <div>
                        <h3 class="bordered uk-h5 uk-text-right font f700"><?php echo JText::sprintf('CONTACT_INFO'); ?></h3>
                        <div class="uk-grid-column-small uk-grid-row-medium" data-uk-grid>
                            <?php if (!empty($params->get('address'))) { ?>
                                <div class="uk-width-1-1">
                                    <div>
                                        <div class="uk-grid-small contactFields" data-uk-grid>
                                            <div class="uk-width-auto uk-text-primary uk-flex uk-flex-middle"><i class="fas fa-fw fa-2x fa-map-signs"></i></div>
                                            <div class="uk-width-expand uk-text-dark uk-flex uk-flex-middle uk-text-zero">
                                                <span class="uk-display-block">
                                                    <span class="uk-text-tiny font uk-text-muted uk-display-block"><?php echo JText::_('ADDRESS').' :'; ?></span>
                                                    <span class="uk-text-small value font f700 uk-text-secondary"><?php echo $params->get('address'); ?></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($params->get('phone'))) { ?>
                                <div class="uk-width-1-2">
                                    <div>
                                        <div class="uk-grid-small contactFields" data-uk-grid>
                                            <div class="uk-width-auto uk-text-primary uk-flex uk-flex-middle"><i class="fas fa-fw fa-2x fa-phone fa-flip-horizontal"></i></div>
                                            <div class="uk-width-expand uk-text-dark uk-flex uk-flex-middle uk-text-zero">
                                                <span class="uk-display-block">
                                                    <span class="uk-text-tiny font uk-text-muted uk-display-block"><?php echo JText::_('PHONE').' :'; ?></span>
                                                    <span class="uk-text-small value font f700 uk-text-secondary ltr uk-display-inline-block"><?php $array = preg_split('/\n|\r\n/', $params->get('phone')); echo fnum($array[0]); ?></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($params->get('fax'))) { ?>
                                <div class="uk-width-1-2">
                                    <div>
                                        <div class="uk-grid-small contactFields" data-uk-grid>
                                            <div class="uk-width-auto uk-text-primary uk-flex uk-flex-middle"><i class="fas fa-fw fa-2x fa-phone fa-flip-horizontal"></i></div>
                                            <div class="uk-width-expand uk-text-dark uk-flex uk-flex-middle uk-text-zero">
                                                <span class="uk-display-block">
                                                    <span class="uk-text-tiny font uk-text-muted uk-display-block"><?php echo JText::_('FAX').' :'; ?></span>
                                                    <span class="uk-text-small value font f700 uk-text-secondary ltr uk-display-inline-block"><?php $array = preg_split('/\n|\r\n/', $params->get('fax')); echo fnum($array[0]); ?></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if (!empty($params->get('lat')) && !empty($params->get('lng'))) { ?>
                        <div class="uk-hidden@m">
                            <h3 class="bordered uk-h5 uk-text-right font f700"><?php echo JText::sprintf('PATHFINDER'); ?></h3>
                            <div>
                                <div class="uk-grid-small uk-child-width-1-2" data-uk-grid>
                                    <div><a href="https://waze.com/ul?ll=<?php echo $params->get('lat'); ?>,<?php echo $params->get('lng'); ?>&navigate=yes" class="uk-width-1-1 uk-padding-small uk-button uk-button-default uk-border-rounded uk-box-shadow-small" target="_blank" rel="noreferrer"><img src="<?php echo JURI::base().'images/waze-logo.svg' ?>" width="100" alt=""></a></div>
                                    <div><a href="http://maps.google.com/maps?daddr=<?php echo $params->get('lat'); ?>,<?php echo $params->get('lng'); ?>" class="uk-width-1-1 uk-padding-small uk-button uk-button-default uk-border-rounded uk-box-shadow-small" target="_blank" rel="noreferrer"><img src="<?php echo JURI::base().'images/google-maps-logo.svg'; ?>" width="100" alt=""></a></div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="uk-visible@s" data-uk-lightbox>
                        <h3 class="bordered uk-h5 uk-text-right font f700"><?php echo JText::sprintf('OUR_LOCATION'); ?></h3>
                        <a class="uk-button uk-button-default uk-border-rounded uk-width-1-1 uk-button-large uk-box-shadow-small font uk-flex uk-flex-center uk-flex-middle" href="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3236.5126655528593!2d51.445603815553056!3d35.78734563175184!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3f8e05cb80ed9cf7%3A0xd3443023583777ec!2sTavan%20Ressan!5e0!3m2!1sen!2s!4v1643272136825!5m2!1sen!2s" data-caption="<?php echo JText::sprintf('SHOW_ON_MAP'); ?>" data-type="iframe"><i class="fas fa-map-signs uk-margin-small-left"></i><?php echo JText::sprintf('SHOW_ON_MAP'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>