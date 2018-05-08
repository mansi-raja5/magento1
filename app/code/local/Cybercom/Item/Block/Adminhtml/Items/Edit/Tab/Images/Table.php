<?php
/**
 * Catalog product form gallery content
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Cybercom_Item_Block_Adminhtml_Items_Edit_Tab_Images_Table
    extends Mage_Adminhtml_Block_Widget
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('media_item_content');
        $this->setTemplate('cybercom_item/images/gallery.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('uploader',
            $this->getLayout()->createBlock('adminhtml/media_uploader')
        );

        $this->getUploader()->getConfig()
            ->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/items/upload'))
            ->setFileField('image')
            ->setFilters(array(
                'images' => array(
                    'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
                    'files' => array('*.gif', '*.jpg','*.jpeg', '*.png')
                )
            ));

        Mage::dispatchEvent('cybercom_item_image_prepare_layout', array('block' => $this));

        return parent::_prepareLayout();
    }

    /**
     * Retrive uploader block
     *
     * @return Mage_Adminhtml_Block_Media_Uploader
     */
    public function getUploader()
    {
        return $this->getChild('uploader');
    }

    /**
     * Retrive uploader block html
     *
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    public function getAddImagesButton()
    {
        return $this->getButtonHtml(
            Mage::helper('catalog')->__('Add New Images'),
            $this->getJsObjectName() . '.showUploader()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }

    public function getImagesJson()
    {

    	$itemId  = $this->getRequest()->getParam('id');
    	$itemImagesModel = Mage::getModel('cybercom_item/itemimages')->getCollection()
    						->addFieldToFilter('item_id',$itemId);
    	
    	$imageData = array();
    	foreach ($itemImagesModel->getData() as $key => $image) {
	        $imageData[$key]['value_id'] = $image['image_id'];
            $imageData[$key]['file'] 	 = $image['image'];
            $imageData[$key]['label'] 	 = $image['label'];
            $imageData[$key]['position'] = $image['sort_order'];
            $imageData[$key]['disabled'] = 0;
            $imageData[$key]['label_default'] ="";
            $imageData[$key]['position_default'] = 1;
            $imageData[$key]['disabled_default'] = 0;
            $imageData[$key]['url'] = Mage::getBaseUrl("media").$image['image'];
    	}

		return Mage::helper('core')->jsonEncode($imageData);    						
    	
    	
        /*if(is_array($this->getElement()->getValue())) {
            $value = $this->getElement()->getValue();
            if(count($value['images'])>0) {
                foreach ($value['images'] as &$image) {
                    $image['url'] = Mage::getSingleton('catalog/product_media_config')
                                        ->getMediaUrl($image['file']);
                }
                return Mage::helper('core')->jsonEncode($value['images']);
            }
        }*/

        return '[]';
    }

    public function getImagesValuesJson()
    {
        $values = array();
        // foreach ($this->getMediaAttributes() as $attribute) {
        //     /* @var $attribute Mage_Eav_Model_Entity_Attribute */
        //     $values[$attribute->getAttributeCode()] = $this->getElement()->getDataObject()->getData(
        //         $attribute->getAttributeCode()
        //     );
        // }
        return Mage::helper('core')->jsonEncode($values);
    }
    /**
     * Enter description here...
     *
     * @return array
     */
    public function getImageTypes()
    {
        $imageTypes = array();
		$imageTypes[image][label] = "Base Image[STORE VIEW]";
		$imageTypes[image][field] = "product[image]";

		$imageTypes[small_image][label] = "Small Image[STORE VIEW]";
		$imageTypes[small_image][field] = "product[small_image]";

		$imageTypes[thumbnail][label] = "Thumbnail[STORE VIEW]";
		$imageTypes[thumbnail][field] = "product[thumbnail]";       
        return $imageTypes;
    }

    public function hasUseDefault()
    {
        foreach ($this->getMediaAttributes() as $attribute) {
            if($this->getElement()->canDisplayUseDefault($attribute))  {
                return true;
            }
        }

        return false;
    }

    public function getImageTypesJson()
    {
        return Mage::helper('core')->jsonEncode($this->getImageTypes());
    }

 

}