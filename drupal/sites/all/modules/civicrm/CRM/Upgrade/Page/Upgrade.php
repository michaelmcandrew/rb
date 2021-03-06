<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2012
 * $Id$
 *
 */
class CRM_Upgrade_Page_Upgrade extends CRM_Core_Page {
  function preProcess() {
    parent::preProcess();
  }

  function run() {
    // lets get around the time limit issue if possible for upgrades
    if (!ini_get('safe_mode')) {
      set_time_limit(0);
    }

    $upgrade = new CRM_Upgrade_Form();
    list($currentVer, $latestVer) = $upgrade->getUpgradeVersions();

    CRM_Utils_System::setTitle(ts('Upgrade CiviCRM to Version %1',
        array(1 => $latestVer)
      ));

    $template = CRM_Core_Smarty::singleton();
    $template->assign('pageTitle', ts('Upgrade CiviCRM to Version %1',
        array(1 => $latestVer)
      ));
    $template->assign('menuRebuildURL',
      CRM_Utils_System::url('civicrm/menu/rebuild', 'reset=1')
    );
    $template->assign('cancelURL',
      CRM_Utils_System::url('civicrm/dashboard', 'reset=1')
    );

    // $action = CRM_Utils_Request::retrieve( 'action', 'String',  CRM_Core_DAO::$_nullObject, false, 'intro', null );
    $action = CRM_Utils_Array::value('action', $_REQUEST, 'intro');
    switch ($action) {
      case 'intro':
        $this->runIntro();
        break;

      case 'begin':
        $this->runBegin();
        break;

      case 'finish':
        $this->runFinish();
        break;

      default:
        CRM_Core_Error::fatal(ts('Unrecognized upgrade action'));
    }
  }

  /**
   * Display an introductory screen with any pre-upgrade messages
   */
  function runIntro() {
    $upgrade = new CRM_Upgrade_Form();
    $template = CRM_Core_Smarty::singleton();
    list($currentVer, $latestVer) = $upgrade->getUpgradeVersions();

    if ($error = $upgrade->checkUpgradeableVersion($currentVer, $latestVer)) {
      CRM_Core_Error::fatal($error);
    }

    // This could be removed in later rev
    if ($currentVer == '2.1.6') {
      $config = CRM_Core_Config::singleton();
      // also cleanup the templates_c directory
      $config->cleanupCaches();
    } else {
      $config = CRM_Core_Config::singleton();
      // cleanup only the templates_c directory
      $config->cleanup(1, FALSE);
    }
    // end of hack

    $preUpgradeMessage = NULL;
    CRM_Upgrade_Incremental_Legacy::setPreUpgradeMessage($preUpgradeMessage, $currentVer, $latestVer);
    self::setPreUpgradeMessage($preUpgradeMessage, $currentVer, $latestVer);

    $template->assign('currentVersion', $currentVer);
    $template->assign('newVersion', $latestVer);
    $template->assign('upgradeTitle', ts('Upgrade CiviCRM from v %1 To v %2',
        array(1 => $currentVer, 2 => $latestVer)
      ));
    $template->assign('upgraded', FALSE);

    $template->assign('preUpgradeMessage', $preUpgradeMessage);
    // $template->assign( 'message', $postUpgradeMessage );

    $content = $template->fetch('CRM/common/success.tpl');
    echo CRM_Utils_System::theme('page', $content, TRUE, $this->_print, FALSE, TRUE);
  }

  /**
   * Begin the upgrade by building a queue of tasks and redirecting to the queue-runner
   */
  function runBegin() {
    $upgrade = new CRM_Upgrade_Form();
    list($currentVer, $latestVer) = $upgrade->getUpgradeVersions();

    if ($error = $upgrade->checkUpgradeableVersion($currentVer, $latestVer)) {
      CRM_Core_Error::fatal($error);
    }

    // This could be removed in later rev
    if ($currentVer == '2.1.6') {
      $config = CRM_Core_Config::singleton();
      // also cleanup the templates_c directory
      $config->cleanupCaches();
    }
    // end of hack

    $postUpgradeMessage = ts('CiviCRM upgrade was successful.');

    // Persistent message storage across upgrade steps. TODO: Use structured message store
    // Note: In clustered deployments, this file must be accessible by all web-workers.
    $this->set('postUpgradeMessageFile', CRM_Utils_File::tempnam('civicrm-post-upgrade'));
    file_put_contents($this->get('postUpgradeMessageFile'), $postUpgradeMessage);

    $queueRunner = new CRM_Queue_Runner(array(
        'title' => ts('CiviCRM Upgrade Tasks'),
        'queue' => CRM_Upgrade_Form::buildQueue($currentVer, $latestVer, $this->get('postUpgradeMessageFile')),
        'isMinimal' => TRUE,
        'pathPrefix' => 'civicrm/upgrade/queue',
        'onEndUrl' => CRM_Utils_System::url('civicrm/upgrade', 'action=finish', FALSE, NULL, FALSE ),
      ));
    $queueRunner->runAllViaWeb();
    CRM_Core_Error::fatal(ts('Upgrade failed to redirect'));
  }

  /**
   * Display any final messages, clear caches, etc
   */
  function runFinish() {
    $upgrade = new CRM_Upgrade_Form();
    $template = CRM_Core_Smarty::singleton();

    // TODO: Use structured message store
    $postUpgradeMessage = file_get_contents($this->get('postUpgradeMessageFile'));

    // This destroys $session, so do it after ge('postUpgradeMessageFile')
    CRM_Upgrade_Form::doFinish();

    // do a version check - after doFinish() sets the final version
    list($currentVer, $latestVer) = $upgrade->getUpgradeVersions();
    if ($error = $upgrade->checkCurrentVersion($currentVer, $latestVer)) {
      CRM_Core_Error::fatal($error);
    }

    $template->assign('message', $postUpgradeMessage);
    $template->assign('upgraded', TRUE);

    $content = $template->fetch('CRM/common/success.tpl');
    echo CRM_Utils_System::theme('page', $content, TRUE, $this->_print, FALSE, TRUE);
  }

  /**
   * Compute any messages which should be displayed before upgrade
   * by calling the 'setPreUpgradeMessage' on each incremental upgrade
   * object.
   *
   * @param $preUpgradeMessage string, alterable
   */
  static
  function setPreUpgradeMessage(&$preUpgradeMessage, $currentVer, $latestVer) {
    $upgrade = new CRM_Upgrade_Form();

    // Scan through all php files and see if any file is interested in setting pre-upgrade-message
    // based on $currentVer, $latestVer.
    // Please note, at this point upgrade hasn't started executing queries.
    $revisions = $upgrade->getRevisionSequence();
    foreach ($revisions as $rev) {
      if (version_compare($currentVer, $rev) < 0 &&
        version_compare($rev, '3.2.alpha1') > 0
      ) {
        $versionObject = $upgrade->incrementalPhpObject($rev);
        if (is_callable(array(
          $versionObject, 'setPreUpgradeMessage'))) {
          $versionObject->setPreUpgradeMessage($preUpgradeMessage, $rev);
        }
      }
    }
  }
}

