<?php
/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Giftpromo
 * @copyright   Copyright (c) 2011 S3i Business sarl au. (http://www.s3ibusiness.com)
 * @author      Ahmed Mahi <1hmedmahi@gmail.com>
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class S3ibusiness_Giftpromo_Adminhtml_indexController extends Mage_Adminhtml_Controller_action
{

    protected function _initAction() {
        $this->loadLayout()
        //		->_setActiveMenu('giftpromo/items')
        ->_addBreadcrumb(Mage::helper('giftpromo')->__('Gifts Manager'), Mage::helper('giftpromo')->__('Gift Manager'));

        return $this;
    }

    public function indexAction() {
        $this->_initAction()
        ->_addContent($this->getLayout()->createBlock('giftpromo/adminhtml_giftpromo'))
        ->renderLayout();
    }

    public function editAction() {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('giftpromo/giftpromo')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('giftpromo_data', $model);

            $this->loadLayout();
            //$this->_setActiveMenu('giftpromo/items');

            $this->_addBreadcrumb(Mage::helper('giftpromo')->__('Gift Manager'), Mage::helper('giftpromo')->__('Gift Manager'));
            $this->_addBreadcrumb(Mage::helper('giftpromo')->__('Gift News'), Mage::helper('giftpromo')->__('Gift News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('giftpromo/adminhtml_giftpromo_edit'))
            ->_addLeft($this->getLayout()->createBlock('giftpromo/adminhtml_giftpromo_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftpromo')->__('Gift does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('giftpromo/giftpromo');
            $model->setData($data)
            ->setId($this->getRequest()->getParam('id'));
            $col=$model->getCollection()->addFieldToFilter('product_id',$data['product_id']);
            foreach($col as $g){
                if($g->getId()!=$model->getId()){
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftpromo')->__('Please select another product id'));
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
            }
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('giftpromo')->__('Gift was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftpromo')->__('Unable to find gift to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('giftpromo/giftpromo');

                $model->setId($this->getRequest()->getParam('id'))
                ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('giftpromo')->__('Gift was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $giftpromoIds = $this->getRequest()->getParam('giftpromo');
        if(!is_array($giftpromoIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftpromo')->__('Please select gift(s)'));
        } else {
            try {
                foreach ($giftpromoIds as $giftpromoId) {
                    $giftpromo = Mage::getModel('giftpromo/giftpromo')->load($giftpromoId);
                    $giftpromo->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('giftpromo')->__(
                        'Total of %d record(s) were successfully deleted', count($giftpromoIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $giftpromoIds = $this->getRequest()->getParam('giftpromo');
        if(!is_array($giftpromoIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select gift(s)'));
        } else {
            try {
                foreach ($giftpromoIds as $giftpromoId) {
                    $giftpromo = Mage::getSingleton('giftpromo/giftpromo')
                    ->load($giftpromoId)
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($giftpromoIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName   = 'giftpromo.csv';
        $content    = $this->getLayout()->createBlock('giftpromo/adminhtml_giftpromo_grid')
        ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'giftpromo.xml';
        $content    = $this->getLayout()->createBlock('giftpromo/adminhtml_giftpromo_grid')
        ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}