<?php

namespace Dotdigitalgroup\Email\Block\Config;

use Magento\Catalog\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;

//class Wrapper extends Mage_Adminhtml_Block_System_Config_Form_Field
class Wrapper extends \Magento\Framework\View\Element\Template
{

	/**
	 * Ajax Validate the api credentials.
	 */
	protected function _getElementHtmlDemo(Varien_Data_Form_Element_Abstract $element)
	{
		$element->setData('onchange', "apiValidation(this.form, this);");

		$url = Mage::helper('adminhtml')->getUrl('*/connector/ajaxvalidation');

		$element->setData('after_element_html', "
            <script>
                document.observe('dom:loaded', function(){
                    apiValidation();
                });
                function apiValidation(form, element) {
                    var api_username   = $('connector_api_credentials_api_username');
                    var api_password   = $('connector_api_credentials_api_password');
                    var reloadurl  = '{$url}';
                    var encoded = btoa(api_password.value);
                    if(api_username.value && api_password.value){
                        new Ajax.Request(reloadurl, {
                            method: 'post',
                            parameters: {'api_username' : api_username.value, 'api_password' : encoded},
                            onComplete: function(transport) {
                                Element.hide('loadingmask');
                                if(transport.responseText == '\"Credentials Valid.\"'){
                                    api_username.setStyle({
                                        fontWeight: 'bold',
                                        color:  'green' ,
                                        background: 'transparent url(\"" . $this->getSkinUrl('images/success_msg_icon.gif') . "\") no-repeat right center'
                                    })
                                }else{
                                    api_username.setStyle({
                                        fontWeight: 'bold',
                                        color:  'red',
                                        background: 'transparent url(\"" . $this->getSkinUrl('images/error_msg_icon.gif') . "\") no-repeat right center'
                                    });
                                }
                            }
                        });
                    }
                    return false;
                }
            </script>
        ");

		return parent::_getElementHtml($element);

	}
}