<?php
/**
 * Copyright (c) 2011 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

\OC_Util::checkAdminUser();

$tmpl = new OCP\Template( 'files_encryption', 'settings' );

$blackList = explode( ',', \OCP\Config::getAppValue( 'files_encryption', 'type_blacklist', '' ) );

$tmpl->assign( 'blacklist', $blackList );
$tmpl->assign( 'encryption_mode', \OC_Appconfig::getValue( 'files_encryption', 'mode', 'none' ) );

\OCP\Util::addscript( 'files_encryption', 'settings' );
\OCP\Util::addscript( 'core', 'multiselect' );

return $tmpl->fetchPage();
