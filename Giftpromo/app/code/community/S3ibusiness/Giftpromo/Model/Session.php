<?php
/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Giftpromo
 * @copyright   Copyright (c) 2017 S3i Business sarl au. (http://www.s3ibusiness.com)
 * @author      Ahmed Mahi <1hmedmahi@gmail.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class S3ibusiness_Giftpromo_Model_Session extends Mage_Core_Model_Session_Abstract
{

    public function __construct()
    {
        $this->init('giftpromo');
        if (!is_array($this->getCartValidateGifts())) {
            $this->setCartValidateGifts(array());
        }
    }

    public function clear()
    {
        $this->setCartValidateGifts(array());
    }
}
