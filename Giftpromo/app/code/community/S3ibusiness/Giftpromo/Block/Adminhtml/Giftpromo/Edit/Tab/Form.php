<?php
/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Giftpromo
 * @copyright   Copyright (c) 2011 S3i Business sarl au. (http://www.s3ibusiness.com)
 * @author      Ahmed Mahi <1hmedmahi@gmail.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class S3ibusiness_Giftpromo_Block_Adminhtml_Giftpromo_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('giftpromo_form', array('legend'=>Mage::helper('giftpromo')->__('Gift Information')));
     
      $fieldset->addField('gift_name', 'text', array(
          'label'     => Mage::helper('giftpromo')->__('Gift name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'gift_name',
      ));
    $fieldset->addField('product_id', 'text', array(
          'label'     => Mage::helper('giftpromo')->__('Product id'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'product_id',
      ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('giftpromo')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('giftpromo')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('giftpromo')->__('Disabled'),
              ),
          ),
      ));
     
 
      if ( Mage::getSingleton('adminhtml/session')->getGiftpromoData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getGiftpromoData());
          Mage::getSingleton('adminhtml/session')->setGiftpromoData(null);
      } elseif ( Mage::registry('giftpromo_data') ) {
          $form->setValues(Mage::registry('giftpromo_data')->getData());
      }
      return parent::_prepareForm();
  }
}