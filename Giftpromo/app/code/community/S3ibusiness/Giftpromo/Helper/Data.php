<?php
/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Giftpromo
 * @copyright   Copyright (c) 2011 S3i Business sarl au. (http://www.s3ibusiness.com)
 * @author      Ahmed Mahi <1hmedmahi@gmail.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class S3ibusiness_Giftpromo_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function addGiftToCart($gift)
    {
        $cart = $this->getCart();

        try {
            if ($giftProduct = $this->getProduct($gift->getProductId())) {
                $cart->addProduct(
                    $giftProduct,
                    array(
                        'qty' => 1,
                    )
                );
            }
        } catch (Exception $e) {

            $this->AddCartNotice('Giftpromo: ( gift Id:' . $gift->getId() . ' ) ' . $e->getMessage());
        }

    }
    public function addAllGift()
    {
        $validateGifts = $this->getCartValidateGifts();
        $quote         = '';
        foreach ($validateGifts as $giftId) {
            $gift  = Mage::getModel('giftpromo/giftpromo')->load($giftId);
            $quote = $this->addGiftToCart($gift);
        }
        try {
            if (is_object($quote)) {
                $quote->save();
            }
        } catch (Exception $e) {

            $this->AddCartNotice('Giftpromo: ' . $e->getMessage());
        }
    }

    public function getCartValidateGiftsProductsIds()
    {
        $validateGifts = $this->getCartValidateGifts();
        $productsIds   = array();
        foreach ($validateGifts as $giftId) {
            $gift          = Mage::getModel('giftpromo/giftpromo')->load($giftId);
            $productsIds[] = $gift->getProductId();
        }
        return $productsIds;
    }
    public function isInCartValidateGift($giftId)
    {
        $validateGifts = $this->getCartValidateGifts();
        if (in_array($giftId, $validateGifts)) {
            return true;
        }
        return false;
    }
    public function getProduct($productId)
    {
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }
    public function AddCartNotice($message)
    {
        if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
            Mage::getSingleton('checkout/session')->addNotice($message);
        } else {
            Mage::getSingleton('checkout/session')->addError($message);
        }
    }

    public function getCartValidateGifts()
    {
        return Mage::getSingleton('giftpromo/session')
            ->getCartValidateGifts();
    }
    public function setCartValidateGifts($newCartValidateGifts)
    {
        Mage::getSingleton('giftpromo/session')
            ->setCartValidateGifts($newCartValidateGifts);
    }
    public function deleteOldValidatesGifts()
    {
        Mage::getSingleton('giftpromo/session')
            ->clear();

    }
    public function getCart()
    {
        return Mage::getModel('checkout/cart');
    }
    public function getQuote()
    {
        return $this->getCart()->getQuote();
    }

}
