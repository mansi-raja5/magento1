<?php
class Cybercom_Vendor_Block_Adminhtml_Vendors_Edit_Pricetext extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
        $html = "";
        
        $productId 		= $row->getId();
        $vendorPriceId 	= $row->getVendor_price_id();
        $vendorPrice 	= $row->getVendor_price();

        //if($vendorPrice != "") 
        $todo = "vendor_prices[insert][".$productId."]";
        if(isset($vendorPriceId) && $vendorPriceId != '')  //Deside insert or update
        	$todo = "vendor_prices[update][".$vendorPriceId."][".$productId."]";


        $html .= '<input type="number" min=0 step=".01" name="'.$todo.'" value="'.$vendorPrice.'" class="input-text validate-number validate-vendor-price">';
       // $html .= '<input type="hidden" name="vendor_product_ids[]" value="'.$row->getId().'" class="input-text ">';

         return $html;
    }
}
?>