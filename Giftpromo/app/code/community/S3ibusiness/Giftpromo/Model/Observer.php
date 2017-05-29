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

    public function __construct()
    {
        if (Mage::getStoreConfig('giftpromo/settings/enabled')) {
            $this->_giftsCollection = Mage::getModel('giftpromo/giftpromo')->getCollection();
        }
    }
    public function deleteDiscountTitle($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Tax_Block_Checkout_Discount) {
            $blockTitle = explode(',', str_replace(')', '', str_replace(' (', ',', $block->getTotal()->getTitle())));
            $title      = $blockTitle[0];
            unset($blockTitle[0]);
            foreach (explode(',', $block->getQuote()->getAppliedRuleIds()) as $appliedRuleId) {
                $rule = Mage::getModel('salesrule/rule')->load($appliedRuleId);
                foreach ($this->_giftsCollection as $gift) {
                    if ($rule->getSimpleAction() == 'gift_product_' . $gift->getGiftId()) {
                        $store_labels = $rule->getStoreLabels();
                        $blockTitle   = array_diff(array_diff(array_diff($blockTitle, array($rule->getName())), array($store_labels[0])), array($store_labels[1]));
                    }
                }
            }
            $block->getTotal()->setTitle($title . ' (' . implode(",", $blockTitle) . ')');
        }
    }
    public function addSimpleAction($observer)
    {
        if (Mage::getStoreConfig('giftpromo/settings/enabled')) {
            $options       = array();
            $fieldset      = $observer->getForm()->getElement('action_fieldset');
            $simple_action = $observer->getForm()->getElement('simple_action');
            $options       = $simple_action->getOptions();
            foreach ($this->_giftsCollection as $gift) {
                if ($gift->getProductId() && $product = $this->getProduct($gift->getProductId())) {
                    $options['gift_product_' . $gift->getGiftId()] = Mage::helper('giftpromo')->__("Gift : '%s'", $gift->getGiftName());
                }
            }
            $fieldset->removeField('simple_action');
            $fieldset->addField('simple_action', 'select', array(
                'label'   => Mage::helper('salesrule')->__('Apply'),
                'name'    => 'simple_action',
                'options' => $options,
            ), '^');
        }
    }

    public function beforeCollectTotals($observer)
    {
        $productsGiftsIds = $this->getHelper()->getCartValidateGiftsProductsIds();
        $quote            = $this->getHelper()->getQuote();
        foreach ($observer->getEvent()->getQuote()->getAllItems() as $item) {
            if (in_array($item->getProductId(), $productsGiftsIds)) {
                $quote->removeItem($item->getId());
            }
        }
        $this->getHelper()->deleteOldValidatesGifts();
    }

    public function afterCollectTotals($observer)
    {
        $this->getHelper()->addAllGift();
    }

    public function addGifts($observer)
    {
        $rule         = $observer->getRule();
        $address      = $observer->getAddress();
        $simpleAction = $rule->getSimpleAction();
        if (!(stripos($simpleAction, 'gift_product_') === false)) {
            list($g, $p, $giftId) = explode('_', $simpleAction);
            $this->addGiftToValidateGifts($giftId);
        }

    }
    public function addGiftToValidateGifts($giftId)
    {
        if (!$this->getHelper()->isInCartValidateGift($giftId)) {
            $cartValidateGifts   = $this->getHelper()->getCartValidateGifts();
            $cartValidateGifts[] = $giftId;
            $this->getHelper()->setCartValidateGifts($cartValidateGifts);
        }
    }
    public function getProduct($productId)
    {
        return $this->getHelper()->getProduct($productId);
    }

    private function getHelper()
    {
        return Mage::helper('giftpromo');
    }

}
