<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class SNH_CheckOutCoupon_IndexController extends Mage_Checkout_CartController
{
	
    public function customcouponreviewpostAction()
    {
        /**
         * No reason continue with empty shopping cart
         */
        
        $response = array();
        $response['status'] = 'ERROR';
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            return;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }

        $oldCouponCode = $this->_getQuote()->getCouponCode();
    
        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            return;
        }
    
        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
            ->collectTotals()
            ->save();
    
            if (strlen($couponCode)) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    $response['msg'] =$this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode));
                    $response['status'] = 'SUCCESS';
                    
                }
                else {
                    $response['msg'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode));
                    $response['status'] = 'ERROR';
                }
            } else {
                $response['status'] = 'SUCCESS';
                $response['msg'] = $this->__('Coupon code was canceled.');
            }
    
        } catch (Mage_Core_Exception $e) {
            //$this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            //$this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
            //Mage::logException($e);
            $response['msg'] = $this->__('Cannot apply the coupon code.');
        }
        $this->loadLayout(false);
        $review = $this->getLayout('ajaxcoupon_index_customcouponreviewpost')->getBlock('roots')->toHtml();
        $response['review'] = $review;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
}
