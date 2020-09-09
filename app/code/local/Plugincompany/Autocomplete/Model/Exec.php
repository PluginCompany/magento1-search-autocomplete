<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Search Autocomplete, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Autocomplete_Model_Exec
{

    private $phpExecutableList =
        array(
            'php-cli',
            'php5-cli',
            'php5.6-cli',
            'php7-cli',
            'php7.0-cli',
            'php7.1-cli',
            'php7.2-cli',
            'php5.6',
            'php5',
            'php7',
            'php7.0',
            'php7.1',
            'php7.2',
            'php'
        );

    private $execMethodName;

    public function isExecEnabled()
    {
        return (bool)$this->getExecMethodName();
    }

    private function getExecMethodName()
    {
        if (!$this->hasExecMethodName()) {
            $this->initExecMethodName();
        }
        return $this->execMethodName;
    }

    private function hasExecMethodName()
    {
        return isset($this->execMethodName);
    }

    private function initExecMethodName()
    {
        if(@exec('echo EXEC') == 'EXEC'){
            return $this->execMethodName = 'exec';
        }
        if(@shell_exec('echo EXEC') == "EXEC\n"){
            return $this->execMethodName = 'shell_exec';
        }
        $this->execMethodName = false;
    }

    public function execute($command, $background = false, $logFile = null)
    {
        if($background){
            $background = " >> {$this->getLogFilePath($logFile)} 2>&1 &";
        }
        if(!$this->getExecMethodName()){
            throw new Exception("Exec is disabled. Please enable exec or shell_exec in php.ini.");
        }
        $this->logExecution($command, $background);
        if($this->getExecMethodName() == 'exec'){
            return @exec($command . $background);
        }
        if($this->getExecMethodName() == 'shell_exec'){
            return @shell_exec($command . $background);
        }
        return $this;
    }

    private function getLogFilePath($logFile)
    {
        if(!$logFile){
            return '/dev/null';
        }
        return Mage::getBaseDir('log') . DS . $logFile;
    }

    private function logExecution($command, $background)
    {
        Mage::log(
            "Executing using {$this->getExecMethodName()}: " . $command . $background,
            null,
            'plugincompany_autocomplete_exec.log',
            true
        );
        return $this;
    }

    public function executePHP($command, $background = false, $logFile = null)
    {
        return $this->execute($this->getPhpExecutable() . ' ' . $command, $background, $logFile);
    }

    public function getPhpExecutable()
    {
        if(!empty(PHP_BINARY) && !stristr(PHP_BINARY, 'fpm')) {
            $executable = preg_replace('/(-cgi|-)$/', '', PHP_BINARY);
            if(file_exists($executable)) {
                return $executable;
            }
        }
        foreach($this->phpExecutableList as $executable) {
            if($this->getExecutable($executable)){
                return $this->getExecutable($executable);
            }
        }
        return 'php';
    }

    private function getExecutable($executable)
    {
        $path = $this->execute('which ' . $executable);
        if(is_array($path)){
            $path = $path[0];
        }
        if(strpos($path, '/') === 0){
            return $path;
        }
        return false;
    }

}