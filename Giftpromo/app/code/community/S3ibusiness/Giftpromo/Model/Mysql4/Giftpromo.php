<?php
/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Giftpromo
 * @copyright   Copyright (c) 2011 S3i Business sarl au. (http://www.s3ibusiness.com)
 * @author      Ahmed Mahi <1hmedmahi@gmail.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class S3ibusiness_Giftpromo_Model_Mysql4_Giftpromo extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the giftpromo_id refers to the key field in your database table.
        $this->_init('giftpromo/giftpromo', 'gift_id');
    }
}