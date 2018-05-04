<?php
/**
 * Category edit general tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Cybercom_Banner_Block_Adminhtml_Bannergroups_Tab_General extends Mage_Adminhtml_Block_Catalog_Form
{

    protected $_bannergroup;

    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    public function getBannergroup()
    {
        if (!$this->_bannergroup) {
            $this->_bannergroup = Mage::registry('bannergroup');
        }
        return $this->_bannergroup;
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_general');
        $form->setDataObject($this->getBannergroup());

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('catalog')->__('General Information')));

//         if (!$this->getBannergroup()->getId()) {
// //            $fieldset->addField('path', 'select', array(
// //                'name'  => 'path',
// //                'label' => Mage::helper('catalog')->__('Parent Category'),
// //                'value' => base64_decode($this->getRequest()->getParam('parent')),
// //                'values'=> $this->_getParentCategoryOptions(),
// //                //'required' => true,
// //                //'class' => 'required-entry'
// //                ),
// //                'name'
// //            );
//             $parentId = $this->getRequest()->getParam('parent');
//             if (!$parentId) {
//                 $parentId = Mage_Catalog_Model_bannergroup::TREE_ROOT_ID;
//             }
//             $fieldset->addField('path', 'hidden', array(
//                 'name'  => 'path',
//                 'value' => $parentId
//             ));
//         } else {
            $fieldset->addField('id', 'hidden', array(
                'name'  => 'id',
                //'value' => $this->getBannergroup()->getId()
            ));
            $fieldset->addField('path', 'hidden', array(
                'name'  => 'path',
                //'value' => $this->getBannergroup()->getPath()
            ));
        // }

        // $this->_setFieldset($this->getBannergroup()->getAttributes(true), $fieldset);

        // if ($this->getBannergroup()->getId()) {
        //     if ($this->getBannergroup()->getLevel() == 1) {
        //         $fieldset->removeField('url_key');
        //         $fieldset->addField('url_key', 'hidden', array(
        //             'name'  => 'url_key',
        //             'value' => $this->getBannergroup()->getUrlKey()
        //         ));
        //     }
        // }

        //$form->addValues($this->getBannergroup()->getData());

        $form->setFieldNameSuffix('general');
        $this->setForm($form);
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_bannergroup_helper_image')
        );
    }

    protected function _getParentCategoryOptions($node=null, &$options=array())
    {
        if (is_null($node)) {
            $node = $this->getRoot();
        }

        if ($node) {
            $options[] = array(
               'value' => $node->getPathId(),
               'label' => str_repeat('&nbsp;', max(0, 3*($node->getLevel()))) . $this->escapeHtml($node->getName()),
            );

            foreach ($node->getChildren() as $child) {
                $this->_getParentCategoryOptions($child, $options);
            }
        }
        return $options;
    }

}

