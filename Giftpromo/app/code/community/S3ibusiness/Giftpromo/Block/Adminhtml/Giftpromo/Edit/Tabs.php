<?php
/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Giftpromo
 * @copyright   Copyright (c) 2011 S3i Business sarl au. (http://www.s3ibusiness.com)
 * @author      Ahmed Mahi <1hmedmahi@gmail.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class S3ibusiness_Giftpromo_Block_Adminhtml_Giftpromo_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('giftpromo_tabs');
      $this->setDestElementId('edit_form');
     // $this->setTitle(Mage::helper('giftpromo')->__(''));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('giftpromo')->__('Gift Information'),
          'title'     => Mage::helper('giftpromo')->__('Gift Information'),
          'content'   => $this->getLayout()->createBlock('giftpromo/adminhtml_giftpromo_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}