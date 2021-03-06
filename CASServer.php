<?php

define('PHPCAS_PATH','/usr/share/php/CAS.php');
require_once PHPCAS_PATH;

define('CAS_VER',CAS_VERSION_2_0);

$CAS_SETUP = false;

final class CASServer {

    /**
    * Call this method to get singleton
    *
    * @return CASServer
    */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new CASServer();
        }
        return $inst;
    }

    /**
    * Private ctor so nobody else can instance it
    *
    */
    private function __construct()
    {

    }

    static $initialized = false;
    private function init()
    {
        if (static::$initialized) return;

        // phpCAS::setDebug();
        phpCAS::client(CAS_VER, 'localhost.lan', 443, 'cas');

        // SSL certification validation
        if (qa_opt('cas_cert_url') != "") {
            phpCAS::setCasServerCACert(qa_opt('cas_cert_url'));
        }
        else {
            phpCAS::setNoCasServerValidation();
        }

        static::$initialized = true;
    }

    public function isAuthenticated()
    {
        $this->init();
        return phpCAS::isAuthenticated();
    }

    public function getUser()
    {
        $this->init();
        return phpCAS::getUser();
    }

    public function forceAuthentication()
    {
        $this->init();
        phpCAS::forceAuthentication();
    }

    public function logout($url)
    {
        $this->init();
        qa_clear_session_cookie();
        qa_clear_session_user();

        //header('Location: '.qa_opt('cas_logout'));
        phpCAS::logout(array('url'=>$url));    }
    }
