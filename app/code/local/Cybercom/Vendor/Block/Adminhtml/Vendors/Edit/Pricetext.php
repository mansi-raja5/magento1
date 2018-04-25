<?php
class Cybercom_Vendor_Block_Adminhtml_Vendors_Edit_Pricetext extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
        $html = "";

        $html .= '<input type="number" name="vendor_prices[]" value="'.$row->getVendor_price().'" class="input-text ">';
        $html .= '<input type="hidden" name="vendor_product_ids[]" value="'.$row->getId().'" class="input-text ">';

         return $html;
    }
}
?>