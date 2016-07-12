<?php

namespace NAttreid\Console\Collections;

use NAttreid\AppManager\AppManager,
    NAttreid\Console\CommandCollection;

/**
 * Sprava aplikace
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
        $this->app->clearSession();
    }

    /**
     * Smazani cache
     */
    public function clearCache() {
        $this->app->clearCache();
    }

    /**
     * Smazani cache modelu
     */
    public function invalidateCache() {
        $this->app->invalidateCache();
    }

    /**
     * Smazani logu
     */
    public function clearLog() {
        $this->app->clearLog();
    }

    /**
     * Smazani temp
     */
    public function clearTemp() {
        $this->app->clearTemp();
    }

    /**
     * Git pull
     */
    public function gitPull() {
        $this->app->gitPull(TRUE);
    }

    /**
     * Aktualizace composeru
     */
    public function composerUpdate() {
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
