<?php


namespace DreamProduction\Composer;

use Composer\Composer;
use Composer\Util\ProcessExecutor;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\EventDispatcher\Event;
use Composer\IO\IOInterface;
use Composer\Util\Filesystem;

class Install implements PluginInterface, EventSubscriberInterface {

  
  /**
   * @var Composer $composer
   */
  protected $composer;
  /**
   * @var IOInterface $io
   */
  protected $io;
  /**
   * @var Filesystem $fs
   */
  protected $fs;
  /**
   * @var EventDispatcher $eventDispatcher
   */
  protected $eventDispatcher;
  /**
   * @var ProcessExecutor $executor
   */
  protected $executor;

  public function activate(Composer $composer, IOInterface $io) {
      $this->composer = $composer;
      $this->io = $io;
      $this->eventDispatcher = $composer->getEventDispatcher();
      $this->executor = new ProcessExecutor($this->io);
      $this->fs = new Filesystem($this->executor);
    }

  public static function getSubscribedEvents() {
      return array(
          'init' => 'isProjectInstallationFinished'
      );
  }

  protected function isProjectInstallationFinished() {
    $projectInstallOK = false;
    try {
      if (!$this->hasComposerInstallRan()) {
        $this->io->write("<warning><error>composer install</error> command has not been ran yet.</warning>");
        $projectInstallOK = false;
      }

      if (!$this->hasVMStarted()) {
        $this->io->write("<warning><error>blt vm</error> command has not been ran yet.</warning>");
        $projectInstallOK = false;
      }
      
      if (!$projectInstallOK) {
        $this->showInstallDocumentationMessage();
      }
      else {
        $this->io->write('<info>Project installation seems to be just fine :) </info>');
      }
      
    }
    catch (Exception $ex) {
      $this->io->write('<error>Failed to execute the current command. Message: ' . $ex->getMessage() . '</error>');
    }
  }
  
  protected function hasVMStarted() {
    $boxPath = "box";
    return $this->fs->isDirEmpty($boxPath);
  }

  protected function hasComposerInstallRan() {
    $vendorPath = "vendor";
    return $this->fs->isDirEmpty($vendorPath);
  }

  protected function showInstallDocumentationMessage() {
    $this->io->write('<info>Please refer to this documentation for any additional information: http://www.google.ro</info>');
  }
}