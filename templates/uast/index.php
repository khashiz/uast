<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.cassiopeia
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
JHtml::_('jquery.framework');

/** @var Joomla\CMS\Document\HtmlDocument $this */

$app = Factory::getApplication();
$wa  = $this->getWebAssetManager();

$app  = JFactory::getApplication();
$user = JFactory::getUser();
$params = $app->getTemplate(true)->params;
$menu = $app->getMenu();
$active = $menu->getActive();

$pageparams = $menu->getParams( $active->id );
$pageclass = $pageparams->get( 'pageclass_sfx' );

// Add CSS
if ($this->direction == 'rtl') {
    JHtml::_('stylesheet', 'uikit-rtl.min.css', array('version' => 'auto', 'relative' => true));
} else {
    JHtml::_('stylesheet', 'uikit.min.css', array('version' => 'auto', 'relative' => true));
}
JHtml::_('stylesheet', 'fontawesome.min.css', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', 'brands.min.css', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', 'light.css', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', 'regular.css', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', 'solid.min.css', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', 'uast.css', array('version' => 'auto', 'relative' => true));

// Add js
JHtml::_('script', 'uikit.min.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', 'uikit-icons.min.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', 'custom.js', array('version' => 'auto', 'relative' => true));

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$menu     = $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';
$netparsi = "<a href='https://netparsi.com' class='netparsi' target='_blank' rel='nofollow'>".JTEXT::sprintf('NETPARSI')."</a>";

$this->setMetaData('viewport', 'width=device-width, initial-scale=1');
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="<?php echo $params->get('presetcolor'); ?>">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#000000">
	<jdoc:include type="metas" />
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
</head>
<body class="<?php echo $option . ' view-' . $view . ($layout ? ' layout-' . $layout : ' no-layout') . ($task ? ' task-' . $task : ' no-task') . ($itemid ? ' itemid-' . $itemid : '') . ($pageclass ? ' ' . $pageclass : '') . ($this->direction == 'rtl' ? ' rtl' : ''); ?>">
    <header class="uk-position-relative uk-position-z-index uk-box-shadow-small" id="header">
        <div class="uk-background-primary uk-text-zero bar uk-visible@s">
            <div class="uk-container">
                <div>
                    <div class="uk-child-width-auto uk-flex-between" data-uk-grid>
                        <div class="uk-flex uk-flex-middle">
                            <span class="uk-text-tiny uk-text-secondary font f700 uk-display-inline-block uk-margin-left"><?php echo JText::_('SOCIAL_NETWORKS') ?></span>
                            <ul class="uk-grid-small uk-child-width-auto socials" data-uk-grid>
                                <?php foreach ($params->get('socials') as $item) : ?>
                                    <?php if ($item->icon != '') { ?>
                                        <li><a href="<?php echo $item->link; ?>" title="<?php echo $item->title; ?>" data-uk-tooltip class="uk-flex uk-flex-center uk-flex-middle" target="_blank" id="<?php echo $item->title; ?>"><i class="fab fa-<?php echo $item->icon; ?>"></i></a></li>
                                    <?php } ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <jdoc:include type="modules" name="topbar" style="xhtml" />
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="wrapper">
                <div class="uk-container">
                    <div class="uk-grid-small" data-uk-grid>
                        <div class="uk-width-auto uk-flex uk-flex-middle uk-hidden@s">
                            <span data-uk-toggle="target: #hamMenu;" class="iconWrapper search cursorPointer uk-flex uk-flex-center uk-flex-middle uk-text-white uk-border-circle">
                                <i class="far fa-bars fa-fw uk-animation-fade uk-animation-fast"></i>
                            </span>
                        </div>
                        <div class="uk-width-auto uk-visible@s">
                            <a href="<?php echo JUri::base(); ?>" title="<?php echo $sitename; ?>" class="uk-display-inline-block uk-padding-small uk-padding-remove-horizontal uk-text-secondary logo"><img src="<?php echo JUri::base().'images/sprite.svg#chair'; ?>" width="40" height="80" alt="<?php echo $sitename; ?>" data-uk-svg></a>
                        </div>
                        <div class="uk-width-auto uk-hidden@s">
                            <a href="<?php echo JUri::base(); ?>" title="<?php echo $sitename; ?>" class="uk-display-inline-block uk-padding-small uk-padding-remove-horizontal uk-text-secondary logo"><img src="<?php echo JUri::base().'images/sprite.svg#chair'; ?>" width="30" height="60" alt="<?php echo $sitename; ?>" data-uk-svg></a>
                        </div>
                        <div class="uk-width-expand uk-flex uk-flex-bottom uk-text-secondary">
                            <a href="<?php echo JUri::base(); ?>" title="<?php echo $sitename; ?>" class="uk-flex uk-flex-column uk-padding-small uk-padding-remove-horizontal uk-link-reset">
                                <span class="f700 uk-text-tiny font">مرکز آموزش علمی کاربردی</span>
                                <span class="f900 uk-text-small font">انجمن صنفی مبلمان و دکوراسیون استان تهران</span>
                            </a>
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-middle uk-visible@s">
                            <?php if(JFactory::getUser()->id) { ?>
                                <jdoc:include type="modules" name="user" style="none" />
                            <?php } else { ?>
                                <jdoc:include type="modules" name="user" style="none" />
                            <?php } ?>
                        </div>
                        <div class="uk-width-auto uk-flex uk-flex-middle">
                            <span class="iconWrapper search cursorPointer uk-flex uk-flex-center uk-flex-middle uk-text-white uk-border-circle" data-uk-toggle="target: .searchToggle; animation: uk-animation-fade">
                                <i class="far fa-search fa-fw searchToggle uk-animation-fade uk-animation-fast"></i>
                                <i class="fas fa-times fa-fw searchToggle uk-animation-fade uk-animation-fast" hidden></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-background-white uk-visible@s" data-uk-sticky="start: 100%; animation: uk-animation-slide-top">
            <div class="navWrapper">
                <div class="uk-container">
                    <jdoc:include type="modules" name="nav" style="html5" />
                </div>
            </div>
        </div>
        <jdoc:include type="modules" name="search" style="html5" />
	</header>
    <?php if ($this->countModules('breadcrumbs', true)) : ?>
        <div class="uk-padding-small uk-padding-remove-horizontal uk-background-muted">
            <div class="uk-position-relative">
                <div class="uk-container">
                    <jdoc:include type="modules" name="breadcrumbs" style="html5" />
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($pageparams->get('show_page_heading', 0)) { ?><?php } ?>
    <?php if ($this->countModules('mainslider', true) || $this->countModules('mainbanners', true)) { ?>
        <div class="uk-padding uk-padding-remove-horizontal uk-background-muted">
            <div class="uk-container">
                <div data-uk-grid>
                    <div class="uk-width-1-1 uk-width-expand@s"><jdoc:include type="modules" name="mainslider" style="html5" /></div>
                    <div class="uk-width-1-1 uk-width-2-5@s"><jdoc:include type="modules" name="mainbanners" style="html5" /></div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($this->countModules('topout', true)) : ?>
        <jdoc:include type="modules" name="topout" style="html5" />
    <?php endif; ?>
    <main class="uk-padding uk-padding-remove-horizontal">
        <?php if ($this->countModules('topin', true)) : ?>
            <jdoc:include type="modules" name="topin" style="html5" />
        <?php endif; ?>
        <div class="uk-container">
            <div class="uk-grid-divider" data-uk-grid>
                <?php if ($this->countModules('sidestart', true)) : ?>
                    <aside class="uk-width-1-1 uk-width-1-4@m uk-visible@m">
                        <div data-uk-sticky="offset: 92; bottom: true;">
                            <div class="uk-child-width-1-1" data-uk-grid><jdoc:include type="modules" name="sidestart" style="none" /></div>
                        </div>
                    </aside>
                <?php endif; ?>
                <div class="uk-width-1-1 uk-width-expand@m">
                    <jdoc:include type="message" />
                    <article>
                        <jdoc:include type="component" />
                    </article>
                </div>
                <?php if ($this->countModules('sideend', true)) : ?>
                    <aside class="uk-width-1-1 uk-width-1-4@s uk-visible@s">
                        <div data-uk-sticky="offset: <?php echo $pageclass == 'home' ? 158 : 102; ?>; end: true;">
                            <div class="uk-child-width-1-1" data-uk-grid><jdoc:include type="modules" name="sideend" style="none" /></div>
                        </div>
                    </aside>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <?php if ($this->countModules('bottomout', true)) : ?>
        <jdoc:include type="modules" name="bottomout" style="html5" />
    <?php endif; ?>
    <footer>
        <div class="uk-position-relative modules">
            <div class="uk-padding uk-padding-remove-horizontal">
                <div class="uk-container">
                    <div class="uk-flex-between uk-child-width-expand" data-uk-grid>
                        <div class="uk-width-1-1 uk-width-1-4@s contact">
                            <h6 class="uk-text-secondary bordered"><?php echo JText::_('CONTACT_INFO'); ?></h6>
                            <ul class="uk-grid-small" data-uk-grid>
                                <li class="uk-text-small uk-flex uk-flex-middle uk-width-1-1"><i class="fas fa-map-signs fa-fw fa-lg uk-margin-left uk-text-primary"></i><a href="#" class="uk-text-secondary font f500"><?php echo $params->get('address'); ?></a></li>
                                <li class="uk-text-small uk-flex uk-flex-middle uk-width-1-2 uk-width-1-1@m"><i class="fas fa-phone fa-flip-horizontal fa-fw fa-lg uk-margin-left uk-text-primary"></i><a href="tel:<?php echo $params->get('phone'); ?>" class="uk-text-white font f500 ltr"><?php echo $params->get('phone'); ?></a></li>
                                <li class="uk-text-small uk-flex uk-flex-middle uk-width-1-2 uk-width-1-1@m"><i class="fas fa-fax fa-fw fa-lg uk-margin-left uk-text-primary"></i><a href="#" class="uk-text-white font f500 ltr"><?php echo $params->get('fax'); ?></a></li>
                                <li class="uk-text-small uk-flex uk-flex-middle uk-width-1-2 uk-width-1-1@m"><i class="fas fa-envelope-open-text fa-fw fa-lg uk-margin-left uk-text-primary"></i><a href="mailto:<?php echo $params->get('email'); ?>" class="uk-text-white font f500 ltr"><?php echo $params->get('email'); ?></a></li>
                            </ul>
                        </div>
                        <jdoc:include type="modules" name="footer" style="html5" />
                        <div class="uk-width-1-1 uk-width-1-4@s uk-position-relative">
                            <h6 class="uk-text-secondary bordered"><?php echo JText::_('SOCIAL_NETWORKS'); ?></h6>
                            <ul class="uk-grid-small uk-child-width-auto uk-margin-medium-top socials uk-flex-between" data-uk-grid>
                                <?php foreach ($params->get('socials') as $item) : ?>
                                    <?php if ($item->icon != '') { ?>
                                        <li><a href="<?php echo $item->link; ?>" title="<?php echo $item->title; ?>" data-uk-tooltip class="uk-flex uk-flex-center uk-flex-middle" target="_blank" id="<?php echo $item->title; ?>"><i class="fab fa-<?php echo $item->icon; ?>"></i></a></li>
                                    <?php } ?>
                                <?php endforeach; ?>
                            </ul>
                            <a href="#header" data-uk-scroll class="uk-position-bottom-left font f700 uk-text-secondary uk-text-tiny backToTop uk-visible@s">
                                <i class="fas fa-caret-circle-up uk-text-primary"></i>
                                <span><?php echo JText::_('BACK_TO_TOP'); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-text-center uk-text-right@m  uk-position-relative copyright">
            <div class="uk-container">
                <div class="uk-padding-small uk-padding-remove-horizontal wrapper">
                    <div class="uk-grid-row-small uk-grid-column-medium" data-uk-grid>
                        <div class="uk-width-1-1 uk-width-expand@m">
                            <p class="uk-margin-remove uk-text-small font f500"><?php echo JText::sprintf('COPYRIGHT', $sitename); ?></p>
                        </div>
                        <div class="uk-width-1-1 uk-width-auto@m">
                            <p class="uk-margin-remove uk-text-small font f500"><?php echo JText::sprintf('DEVELOPER', $netparsi); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <div id="hamMenu" data-uk-offcanvas="overlay: true">
        <div class="uk-offcanvas-bar uk-card uk-card-default uk-padding-remove bgWhite">
            <div class="uk-flex uk-flex-column uk-height-1-1">
                <div class="uk-width-expand">
                    <div class="offcanvasTop uk-box-shadow-small uk-position-relative uk-flex-stretch">
                        <div class="uk-grid-collapse uk-height-1-1" data-uk-grid>
                            <div class="logo uk-flex uk-flex-center uk-flex-column uk-padding-small">
                                <span class="uk-display-block font f500 uk-text-tiny uk-text-secondary">مرکز آموزش علمی کاربردی</span>
                                <span class="uk-display-block font f900 uk-text-tiny uk-text-secondary">انجمن صنفی مبلمان و دکوراسیون استان تهران</span>
                            </div>
                        </div>
                    </div>
                    <div class="uk-padding-small"><jdoc:include type="modules" name="offcanvas" style="xhtml" /></div>
                </div>
            </div>
        </div>
    </div>
	<jdoc:include type="modules" name="debug" style="none" />
    <?php /* ?>
    <?php if ($pageclass == "home") { ?>
    <script type="text/javascript">
        jQuery(document).ready(function (){
            UIkit.modal("#newYear").show();
        });
    </script>
    <?php } ?>
    <div id="newYear" data-uk-modal>
        <div class="uk-modal-dialog uk-box-shadow-medium uk-border-rounded uk-overflow-hidden">
            <div class="uk-modal-header uk-padding-small">
                <h5 class="uk-modal-title uk-text-center font uk-text-bold uk-text-primary">سال نو مبارک</h5>
            </div>
            <div class="uk-modal-body uk-padding-remove">
                <style>.r1_iframe_embed {position: relative; overflow: hidden; width: 100%; height: auto; padding-top: 56.25%; } .r1_iframe_embed iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }</style><div class="r1_iframe_embed"><iframe src="https://player.arvancloud.com/index.html?config=https://tavanresan.arvanvod.com/EWe2pMbwN0/D7b8MJzVMJ/origin_config.json" style="border:0 #ffffff none;" name="نوروز ۱۴۰۱" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowFullScreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" data-uk-video></iframe></div>
            </div>
        </div>
    </div>
    <?php */ ?>

</body>
</html>











