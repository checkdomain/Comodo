<?php
namespace Checkdomain\Comodo\Model;

/**
 * Class Account
 * Provides the login username and password to comodo
 *
 * @package Checkdomain\Comodo\Model
 */
class Account
{
    private $loginName;
    private $loginPassword;

    /**
     * @param string|null $loginName
     * @param string|null $loginPassword
     */
    public function __construct($loginName = null, $loginPassword = null)
    {
        $this->setLoginName($loginName);
        $this->setLoginPassword($loginPassword);
    }

    /**
     * @param string $loginName
     *
     * @return Account
     */
    public function setLoginName($loginName)
    {
        $this->loginName = $loginName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLoginName()
    {
        return $this->loginName;
    }

    /**
     * @param string $loginPassword
     *
     * @return Account
     */
    public function setLoginPassword($loginPassword)
    {
        $this->loginPassword = $loginPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getLoginPassword()
    {
        return $this->loginPassword;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if ($this->getLoginName() == null || $this->getLoginPassword() == null) {
            return false;
        }

        return true;
    }
}