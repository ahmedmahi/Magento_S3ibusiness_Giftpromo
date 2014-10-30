<?php
/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Giftpromo
 * @copyright   Copyright (c) 2011 S3i Business sarl au. (http://www.s3ibusiness.com)
 * @author      Ahmed Mahi <1hmedmahi@gmail.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class S3ibusiness_Giftpromo_Block_Adminhtml_Giftpromo extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_giftpromo';
    $this->_blockGroup = 'giftpromo';
    $this->_headerText = Mage::helper('giftpromo')->__('Gift Manager');
    $this->_addButtonLabel = Mage::helper('giftpromo')->__('Add Gift');
    parent::__construct();
  }
}