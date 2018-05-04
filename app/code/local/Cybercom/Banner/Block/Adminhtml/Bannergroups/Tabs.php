<?php
/**
 * Category tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Cybercom_Banner_Block_Adminhtml_Bannergroups_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Default Attribute Tab Block
     *
     * @var string
     */
    protected $_attributeTabBlock = 'adminhtml/catalog_category_tab_attributes';

    /**
     * Initialize Tabs
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('bannergroup_info_tabs');
        $this->setDestElementId('bannergroup_tab_content');
        $this->setTitle(Mage::helper('cybercom_banner')->__('Banner Data'));
        //$this->setTemplate('widget/tabshoriz.phtml');
    }

    protected function _prepareLayout()
    {
         $this->addTab('products', array(
            'label'     => Mage::helper('cybercom_banner')->__('Banner'),
            'content'   => $this->getLayout()->createBlock(
                'cybercom_banner/adminhtml_bannergroups_edit'
            ),
        ));
        return parent::_prepareLayout();
    }
}
