<?php
/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Giftpromo
 * @copyright   Copyright (c) 2011 S3i Business sarl au. (http://www.s3ibusiness.com)
 * @author      Ahmed Mahi <1hmedmahi@gmail.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class S3ibusiness_Giftpromo_Model_Observer
{
    private $_giftsCollection;

    public function __construct() {
        if(Mage::getStoreConfig('giftpromo/settings/enabled')) {
            $this->_giftsCollection = Mage::getModel('giftpromo/giftpromo')->getCollection();
        }
    }
    public function deleteDiscountTitle($observer) {
        $block = $observer->getEvent()->getBlock();
        if($block instanceof Mage_Tax_Block_Checkout_Discount){
            $blockTitle=explode(',',str_replace(')','',str_replace(' (', ',', $block->getTotal()->getTitle())));
            $title=$blockTitle[0];
            unset($blockTitle[0]);
            foreach (explode(',',$block->getQuote()->getAppliedRuleIds()) as $appliedRuleId) {
                $rule=Mage::getModel('salesrule/rule')->load($appliedRuleId);
                foreach ($this->_giftsCollection as $gift){
                    if($rule->getSimpleAction()=='gift_product_'.$gift->getGiftId()) {
                        $store_labels=$rule->getStoreLabels();
                        $blockTitle=array_diff(array_diff(array_diff($blockTitle,array($rule->getName())),array($store_labels[0])),array($store_labels[1]));
                    }
                }
            }
            $block->getTotal()->setTitle(  $title.' ('.implode(",",$blockTitle).')');
        }
    }
    public function addSimpleAction($observer) {
        if(Mage::getStoreConfig('giftpromo/settings/enabled')) {
            $options=array(
                'by_percent' => Mage::helper('salesrule')->__('Percent of product price discount'),
                'by_fixed' => Mage::helper('salesrule')->__('Fixed amount discount'),
                'cart_fixed' => Mage::helper('salesrule')->__('Fixed amount discount for whole cart'),
                'buy_x_get_y' => Mage::helper('salesrule')->__('Buy X get Y free (discount amount is Y)'),
            );
            foreach ($this->_giftsCollection as $gift){
                if($gift->getProductId()&&$product=$this->getProduct($gift->getProductId())){
                    $options['gift_product_'.$gift->getGiftId()] = Mage::helper('giftpromo')->__("Gift : '%s'",$gift->getGiftName()/*$product->getName()*/);
                }
            }
            $form = $observer->getForm();
            $fieldset=$form->getElement('action_fieldset');
            $fieldset->removeField('simple_action');
            $fieldset->addField('simple_action', 'select', array(
            'label'     => Mage::helper('salesrule')->__('Apply'),
            'name'      => 'simple_action',
            'options'    => $options,
                ),'^');
        }
    }
    public function addGiftCart($giftId){
        $gift=Mage::getModel('giftpromo/giftpromo')->load($giftId);
        $this->removeGifts($gift);
        if($gift->getStatus()==1&&($product=$this->getProduct($gift->getProductId()))&&$product->getIsInStock()){
            $cart=Mage::getSingleton('checkout/cart');
            $cart->addProduct($product,-1);
            $cart->init();
            $cart->save();

        }
    }
    public function addGifts($observer) {
        if(Mage::getStoreConfig('giftpromo/settings/enabled')) {
            $Controller = $observer->getControllerAction();
            if ($Controller instanceof Mage_Checkout_CartController) {
                $actionName = $Controller->getFullActionName();
                $cart=Mage::getSingleton('checkout/cart');
                $quote=$cart->getQuote();
                $actions=array('add','addgroup','updatePost','delete','couponPost','estimateUpdatePost');
                foreach ($actions as $action) {
                    if($actionName=='checkout_cart_'.$action){
                        $appliedRuleIds=$quote->getAppliedRuleIds();
                        $giftIds=array();
                        foreach (explode(',',$appliedRuleIds) as $appliedRuleId) {
                            $simpleAction=$rule = Mage::getModel('salesrule/rule')
                            ->load($appliedRuleId)->getSimpleAction();
                            if (!(stripos($simpleAction, 'gift_product_')===false)){
                                list($g, $p, $giftId) = explode('_',$simpleAction);
                                $this->addGiftCart($giftId);
                                $giftIds[]=$giftId;
                            }
                        }
                        $this->removeGifts(false,$giftIds);
                    }
                }
            }
        }
    }
    public function removeGifts($giftToDelet,$giftsToLet=false) {

        foreach (Mage::getSingleton('checkout/session')->getQuote()->getAllItems() as $item) {
            if($giftToDelet&&$item->getProductId() == $giftToDelet->getProductId()) {
                Mage::getSingleton('checkout/cart')->removeItem($item->getId())->save();
                break;
            }
            elseif(is_array($giftsToLet)){
                foreach ($this->_giftsCollection as $gift){
                    if($item->getProductId() == $gift->getProductId()&&(!in_array( $gift->getId(), $giftsToLet))) {
                        Mage::getSingleton('checkout/cart')->removeItem($item->getId())->save();
                    }
                }
            }
        }
    }

    public function getProduct($productId) {
        if ($productId) {
            $product = Mage::getModel('catalog/product')
            ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

}