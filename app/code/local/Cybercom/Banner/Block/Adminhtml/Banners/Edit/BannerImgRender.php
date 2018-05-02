<?php
class Cybercom_Banner_Block_Adminhtml_Banners_Edit_BannerImgRender extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
        $html = "";
        
        $bannerId    = $row->getId();
        $bannerImage = $row->getImage();
        $bannerRedirectUrl = $row->getUrl();
        
        
        $imgPath     = Mage::getBaseDir('media')."/banner/".$bannerImage;  //Image store path
        if(!file_exists($imgPath) || $bannerImage == "")
            $imgUrl      = Mage::getBaseUrl('media')."banner/no_img.png";
        else
            $imgUrl      = Mage::getBaseUrl('media')."/banner/thumbnail/".$bannerImage;  //Image Web Url
        
        // return $imgUrl;
        $html .= '<a href="'.$bannerRedirectUrl.'" target="_blank"><img src="'.$imgUrl.'" width="50" height="25"></a>';
        return $html;
       

    }
}
?>