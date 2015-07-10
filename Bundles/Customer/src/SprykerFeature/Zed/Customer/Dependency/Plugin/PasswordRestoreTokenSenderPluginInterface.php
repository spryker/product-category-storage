<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Customer\Dependency\Plugin;

interface PasswordRestoreTokenSenderPluginInterface
{

    /**
     * @param string $email
     * @param string $token
     *
     * @return bool
     */
    public function send($email, $token);

}
