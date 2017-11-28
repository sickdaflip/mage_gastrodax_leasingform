<?php
class Gastrodax_Leasingform_IndexController extends Mage_Core_Controller_Front_Action{

    public function indexAction(){
        $this->loadLayout();
        $this->_initLayoutMessages('core/session');
        $this->getLayout()->getBlock("head")->setTitle($this->__('Gastro-Leasing und -Mietkauf - Finanzierung Ihrer Investitionen'));
        $this->getLayout()->getBlock("head")->setDescription($this->__('Gastro-Leasing und -Mietkauf bei Gastrodax – wir liefern Top-Geräte für Gastronomie und Großküchen – unsere Finanzpartner ermöglichen Ihre Investition'));
        $this->renderLayout();
    }
    public function postAction(){

        $post = $this->getRequest()->getPost();
        if ($post) {
            try {

                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['type']), 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['object']), 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['sku']), 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['price']), 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['term']), 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['contact']), 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['mail']), 'EmailAddress')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['fon']), 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['bdsg']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }

                $this->notifyAdmin($post);
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('leasingform')->__('Your message was sent.'));
                $wbrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                $wcrl = str_ireplace($wbrl, "", $post['wcrl']);
                $this->_redirect($wcrl);
                return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('leasingform')->__('There are some errors in your form.'));
                $wbrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
                $wcrl = str_ireplace($wbrl, "", $post['wcrl']);
                $this->_redirect($wcrl);
                return;
            }
        }
    }

    private function notifyAdmin($post) {
        $mailSubject = 'Leasingform';
        $adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
        $adminName = Mage::getStoreConfig('trans_email/ident_general/name');
        $storeName = Mage::app()->getStore()->getName();
        $vars = array(
            'type' => $post['type'],
            'object' => $post['object'],
            'sku' => $post['sku'],
            'price' => $post['price'],
            'term' => $post['term'],
            'company' => $post['company'],
            'contact' => $post['contact'],
            'mail' => $post['mail'],
            'street' => $post['street'],
            'zip' => $post['zip'],
            'city' => $post['city'],
            'fon' => $post['fon'],
            'remarks' => $post['remarks'],
            'bdsg' => $post['bdsg'],
            'adminName' => $adminName,
            'storeName' => $storeName
        );

        $emailTemplate = Mage::getModel('core/email_template');
        $emailTemplate->loadDefault('leasing_notification_admin');
        $emailTemplate->setTemplateSubject($mailSubject);
        $emailTemplate->setSenderName($post['contact']);
        $emailTemplate->setSenderEmail($post['mail']);
        $emailTemplate->send($adminEmail, $storeName, $vars);
    }
}