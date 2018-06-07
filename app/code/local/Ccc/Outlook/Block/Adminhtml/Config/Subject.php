<?php
class Ccc_Outlook_Block_Adminhtml_Config_Subject extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected $_StatusData = null;
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) 
    {
        $this->setElement($element);
        $output = '<script type="text/javascript">//<![CDATA[' . "\n";
        $output .= '    var xml_form_template = \'' . str_replace("'", "\'", $this->_getRowEditHtml()) .'\';' . "\n";
        $output .= '//]]></script>' . "\n";
        $output .= '<input type="hidden" name="' . $this->getElement()->getName() . '" value="">';
        $output .= '<div class="grid" >';
        $output .= '<table cellpadding="0" cellspacing="0" class="border"><tbody>';
        $output .= $this->_getHeaderHtml();
        if ($this->getElement()->getData('value')) {
            foreach ($this->getElement()->getData('value/code') as $elementIndex => $elementName) {
                $output .= $this->_getRowHtml($elementIndex);
            }
        }
        $output .= '<tr>';
        $output .= '<td>';
        $output .= $this->_getAddButtonHtml();
        $output .= '</td>';
        $output .= '<td>';
        $output .= '</td>';
        $output .= '<td>';
        $output .= '</td>';
        $output .= '</tr>';
        $output .= '</tbody></table>';
        $output .= '</div>';
        return $output;
    }

    protected function _getHeaderHtml() {
        $output = '<tr class="headings">';
        $output .= '<th>';
        $output .= 'Mail Subject';
        $output .= '</th>';
        $output .= '<th>';
        $output .= 'Order Status';
        $output .= '</th>';    
        $output .= '<th></th>';    
        $output .= '</tr>';
        return $output;
    }

    protected function _getRowHtml($index = 0) {
        $output = '<tr>';

        $output .= '<td>';
        $output .= '<input type="text" class="required-entry input-text" name="' . $this->getElement()->getName() . '[code][]" value="' . $this->getElement()->getData('value/code/' . $index) . '" />';
        $output .= '</td>';   

        $output .= '<td>';
        $output .= $this->_getOrderStatusSelectHtml($index);
        $output .= '</td>';

        $output .= '<td>';
        $output .= $this->_getRemoveButtonHtml();
        $output .= '</td>';

        $output .= '</tr>';
        return $output;
    }

    protected function _getRowEditHtml() {
        $output = '<tr>';
        $output .= '<td>';
        $output .= '<input class="required-entry input-text" name="' . $this->getElement()->getName() . '[code][]">';
        $output .= '</td>';        

        $output .= '<td>';
        $output .= $this->_getOrderStatusSelectHtml(null, false);
        $output .= '</td>';

        $output .= '<td>';
        $output .= $this->_getRemoveButtonHtml();
        $output .= '</td>';

        $output .= '</tr>';
        return $output;
    }

    protected function _getAddButtonHtml() {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('add')
            ->setLabel($this->__('Add More Order Status'))
            ->setOnClick("Element.insert($(this).up('tr'), {before: xml_form_template})")
            ->toHtml();
    }

    protected function _getRemoveButtonHtml() {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('delete v-middle')
            ->setLabel($this->__('Delete'))
            ->setOnClick("Element.remove($(this).up('tr'))")
            ->toHtml();
    }
    
    protected function _getOrderStatusData()
    {
        if($this->_StatusData == null){
            $collection = Mage::getModel('cybercom_vendor/vendordetail')->getCollection();
            // print_r($collection->getData());exit;
                
            if(count($collection) > 0) {
                foreach($collection as $_brand) {
                    $this->_StatusData[$_brand->getId()] = $_brand->getname();
                }
                return $this->_StatusData;
            }
        }
        return $this->_StatusData;
    }
    
    protected function _getOrderStatusSelectHtml($index = null, $flag = true)
    {   
        $statusData = $this->_getOrderStatusData();
        $select = '';
        $select .= '<select class="select" name="' . $this->getElement()->getName() . '[orderstatus][]">';
        $select .= '<option value="">Select Order Status</option>';
        if(count($statusData) > 0){
            foreach($statusData as $code => $status){
                if($this->getElement()->getData('value/orderstatus/' . $index) ==  $code && $flag){
                    $select .= '<option selected="selected" value="'.$code.'">'.$status.'</option>';
                }else{
                    $select .= '<option value="'.$code.'">'.$status.'</option>';
                }
            }
        }
        $select .= '</select>';
        return $select;
    }
}