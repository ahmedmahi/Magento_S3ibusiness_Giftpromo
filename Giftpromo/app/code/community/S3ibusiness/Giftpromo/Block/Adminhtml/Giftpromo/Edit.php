<?php
/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Giftpromo
 * @copyright   Copyright (c) 2011 S3i Business sarl au. (http://www.s3ibusiness.com)
 * @author      Ahmed Mahi <1hmedmahi@gmail.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class S3ibusiness_Giftpromo_Block_Adminhtml_Giftpromo_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'giftpromo';
        $this->_controller = 'adminhtml_giftpromo';
        
        $this->_updateButton('save', 'label', Mage::helper('giftpromo')->__('Save Gift'));
        $this->_updateButton('delete', 'label', Mage::helper('giftpromo')->__('Delete Gift'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('giftpromo')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('giftpromo_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'giftpromo_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'giftpromo_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('giftpromo_data') && Mage::registry('giftpromo_data')->getId() ) {
            return Mage::helper('giftpromo')->__("Edit Gift '%s'", $this->htmlEscape(Mage::registry('giftpromo_data')->getGiftName()));
        } else {
            return Mage::helper('giftpromo')->__('Add Gift');
        }
    }
}