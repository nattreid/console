<?php

namespace NAttreid\Console\Collections;

use NAttreid\AppManager\AppManager,
    NAttreid\Console\CommandCollection;

/**
 * Konzolova aplikace
 * 
 * @author Attreid <attreid@gmail.com>
 */
class App extends CommandCollection {

    /** @var AppManager */
    private $app;

    public function __construct(AppManager $app) {
        $this->app = $app;
    }

    /**
     * Smazani expirovane session
     */
    public function clearSession() {
        $this->printLine('Clear Session');
        $this->app->clearSession();
    }

    /**
     * Smazani cache
     */
    public function clearCache() {
        $this->printLine('Clear Cache');
        $this->app->clearCache();
    }

    /**
     * Smazani cache modelu
     */
    public function cleanModelCache() {
        $this->printLine('Clear Model Cache');
        $this->app->cleanModelCache();
    }

    /**
     * Smazani logu
     */
    public function clearLog() {
        $this->printLine('Clear Log');
        $this->app->clearLog();
    }

    /**
     * Smazani temp
     */
    public function clearTemp() {
        $this->printLine('Clear Temp');
        $this->app->clearTemp();
    }

    /**
     * Git pull
     */
    public function gitPull() {
        $this->printLine('gitPull');
        $this->app->gitPull(TRUE);
    }

    /**
     * Aktualizace composeru
     */
    public function composerUpdate() {
        $this->printLine('Composer update');
        $this->app->composerUpdate(TRUE);
    }

    /**
     * Zapnuti udrzby
     */
    public function maintenance() {
        $this->printLine('Maintenance On');
        $this->printLine("To turn off maintenance run application with parameter maintenanceOff in browser or 'php index.php maintenanceOff' in console");
        $this->app->maintenance();
    }

}
