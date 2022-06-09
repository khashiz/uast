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
use Joomla\CMS\Helper\AuthenticationHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Document\HtmlDocument $this */

$twofactormethods = AuthenticationHelper::getTwoFactorMethods();
$extraButtons     = AuthenticationHelper::getLoginButtons('form-login');
$app              = Factory::getApplication();
$wa               = $this->getWebAssetManager();

$fullWidth = 1;

// Template path
$templatePath = 'templates/' . $this->template;

// Color Theme
$paramsColorName = $this->params->get('colorName', 'colors_standard');
$assetColorName  = 'theme.' . $paramsColorName;
$wa->registerAndUseStyle($assetColorName, $templatePath . '/css/global/' . $paramsColorName . '.css');

// Use a font scheme if set in the template style options
$paramsFontScheme = $this->params->get('useFontScheme', false);
$fontStyles       = '';

if ($paramsFontScheme)
{
	if (stripos($paramsFontScheme, 'https://') === 0)
	{
		$this->getPreloadManager()->preconnect('https://fonts.googleapis.com/', []);
		$this->getPreloadManager()->preconnect('https://fonts.gstatic.com/', []);
		$this->getPreloadManager()->preload($paramsFontScheme, ['as' => 'style']);
		$wa->registerAndUseStyle('fontscheme.current', $paramsFontScheme, [], ['media' => 'print', 'rel' => 'lazy-stylesheet', 'onload' => 'this.media=\'all\'']);

		if (preg_match_all('/family=([^?:]*):/i', $paramsFontScheme, $matches) > 0)
		{
			$fontStyles = '--cassiopeia-font-family-body: "' . str_replace('+', ' ', $matches[1][0]) . '", sans-serif;
			--cassiopeia-font-family-headings: "' . str_replace('+', ' ', isset($matches[1][1]) ? $matches[1][1] : $matches[1][0]) . '", sans-serif;
			--cassiopeia-font-weight-normal: 400;
			--cassiopeia-font-weight-headings: 700;';
		}
	}
	else
	{
		$wa->registerAndUseStyle('fontscheme.current', $paramsFontScheme, ['version' => 'auto'], ['media' => 'print', 'rel' => 'lazy-stylesheet', 'onload' => 'this.media=\'all\'']);
		$this->getPreloadManager()->preload($wa->getAsset('style', 'fontscheme.current')->getUri() . '?' . $this->getMediaVersion(), ['as' => 'style']);
	}
}



// Logo file or site title param
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');

// Browsers support SVG favicons
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon.svg', '', [], true, 1), 'icon', 'rel', ['type' => 'image/svg+xml']);
$this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'alternate icon', 'rel', ['type' => 'image/vnd.microsoft.icon']);
$this->addHeadLink(HTMLHelper::_('image', 'joomla-favicon-pinned.svg', '', [], true, 1), 'mask-icon', 'rel', ['color' => '#000']);


// Add CSS
JHtml::_('stylesheet', 'uikit-rtl.min.css', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', 'fontawesome.min.css', array('version' => 'auto', 'relative' => true));
JHtml::_('stylesheet', 'rasa.css', array('version' => 'auto', 'relative' => true));

// Add js
JHtml::_('script', 'uikit.min.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', 'uikit-icons.min.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', 'custom.js', array('version' => 'auto', 'relative' => true));
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="metas" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
</head>
<body class="site">
	<div class="uk-container uk-container-small outer">
		<div class="uk-card">
			<div class="header uk-padding-large">
			<?php /* if (!empty($logo)) : ?>
				<h1><?php echo $logo; ?></h1>
			<?php else : ?>
			<?php endif; */ ?>
                <h1 class="uk-text-center font f800 uk-text-primary"><?php echo $sitename; ?></h1>
			<?php if ($app->get('offline_image')) : ?>
				<?php echo HTMLHelper::_('image', $app->get('offline_image'), $sitename, [], false, 0); ?>
			<?php endif; ?>
			<?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') : ?>
				<p class="uk-text-center uk-text-small uk-text-dark font f400"><?php echo $app->get('offline_message'); ?></p>
			<?php elseif ($app->get('display_offline_message', 1) == 2) : ?>
				<p class="uk-text-center uk-text-small uk-text-dark font f500"><?php echo Text::_('JOFFLINE_MESSAGE'); ?></p>
			<?php endif; ?>
			</div>
			<div class="login">
				<jdoc:include type="message" />
				<form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login" class="uk-width-1-1 uk-width-1-2@m uk-margin-auto noBorder">
					<fieldset class="uk-padding-remove uk-margin-remove formContainer uk-form-stacked">
                        <div class="uk-child-width-1-1 uk-grid-small" data-uk-grid>
                            <div>
                                <label class="uk-form-label" for="username"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
                                <input name="username" class="uk-input form-control" id="username" type="text">
                            </div>
                            <div>
                                <label class="uk-form-label" for="password"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
                                <input name="password" class="uk-input form-control" id="password" type="password">
                            </div>
                            <?php if (count($twofactormethods) > 1) : ?>
                                <div>
                                    <label class="uk-form-label" for="secretkey"><?php echo Text::_('JGLOBAL_SECRETKEY'); ?></label>
                                    <input name="secretkey" autocomplete="one-time-code" class="uk-input form-control" id="secretkey" type="text">
                                </div>
                            <?php endif; ?>
                            <?php foreach($extraButtons as $button):
                                $dataAttributeKeys = array_filter(array_keys($button), function ($key) {
                                    return substr($key, 0, 5) == 'data-';
                                });
                                ?>
                                <div class="mod-login__submit form-group">
                                    <button type="button"
                                            class="btn btn-secondary w-100 mt-4 <?php echo $button['class'] ?? '' ?>"
                                    <?php foreach ($dataAttributeKeys as $key): ?>
                                        <?php echo $key ?>="<?php echo $button[$key] ?>"
                                    <?php endforeach; ?>
                                    <?php if ($button['onclick']): ?>
                                        onclick="<?php echo $button['onclick'] ?>"
                                    <?php endif; ?>
                                    title="<?php echo Text::_($button['label']) ?>"
                                    id="<?php echo $button['id'] ?>"
                                    >
                                    <?php if (!empty($button['icon'])): ?>
                                        <span class="<?php echo $button['icon'] ?>"></span>
                                    <?php elseif (!empty($button['image'])): ?>
                                        <?php echo $button['image']; ?>
                                    <?php elseif (!empty($button['svg'])): ?>
                                        <?php echo $button['svg']; ?>
                                    <?php endif; ?>
                                    <?php echo Text::_($button['label']) ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                            <div>
                                <input type="submit" name="Submit" class="uk-button uk-button-large uk-button-primary uk-width-1-1" value="<?php echo Text::_('JLOGIN'); ?>">
                            </div>
                        </div>

						<input type="hidden" name="option" value="com_users">
						<input type="hidden" name="task" value="user.login">
						<input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>">
						<?php echo HTMLHelper::_('form.token'); ?>
					</fieldset>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
