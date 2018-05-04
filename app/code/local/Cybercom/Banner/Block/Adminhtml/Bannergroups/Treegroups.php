<?php
class Cybercom_Banner_Block_Adminhtml_Bannergroups_Treegroups
    extends Mage_Adminhtml_Block_Template 
{
    protected $_categoryIds = null;
    protected $_selectedNodes = null;

    public function __construct() {
        parent::__construct();
        $this->setTemplate('cybercom_banner/bannergroups/tab/tree_group.phtml');
        $this->_withProductCount = false;
    }

    public function getBannergroup(){
        return Mage::registry('current_bannergroup'); //use other registration key if you have one
    }


    public function getTreeJson($parenNodeCategory=null)
    {
        $rootArray['text'] = "ROOT";
        $rootArraychild = $this->_getNodeJson($this->getRoot($parenNodeCategory));
        $rootArray['children'][0] = $rootArraychild;
        $rootArray['expanded'] = true;
        // echo "<pre>";
        // print_r($rootArray);
        // exit;
        $json = Mage::helper('core')->jsonEncode($rootArray);

        return $json;
    }  

    protected function _getNodeJson($node, $level = 0)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Varien_Data_Tree_Node($node, 'entity_id', new Varien_Data_Tree);
        }

        $item = array();
        $item['text'] = $this->buildNodeName($node);

        /* $rootForStores = Mage::getModel('core/store')
            ->getCollection()
            ->loadByCategoryIds(array($node->getEntityId())); */
        $rootForStores = in_array($node->getEntityId(), $this->getRootIds());

        $item['id']  = $node->getId();
        $item['store']  = (int) $this->getStore()->getId();
        $item['path'] = $node->getData('path');

        $item['cls'] = 'folder active-category';
        //$item['allowDrop'] = ($level<3) ? true : false;
        $allowMove = $this->_isCategoryMoveable($node);
        $item['allowDrop'] = $allowMove;
        // disallow drag if it's first level and category is root of a store
        $item['allowDrag'] = $allowMove && (($node->getLevel()==1 && $rootForStores) ? false : true);

        $hasChildren = 0;
        if ((int)$this->getChildrenCount($item['id'])>0) {
            $item['children'] = array();
            $hasChildren = 1;
        }

        $isParent = $this->_isParentSelectedCategory($node);

        if ($hasChildren) {
            $item['children'] = array();
            //if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) 
            {   

                foreach ($this->getChildren($item['id']) as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level+1);
                }
            }
        }

        if ($isParent || $node->getLevel() < 2) {
            $item['expanded'] = true;
        }
        return $item;
    }

    /**
     * Return children group of groupId
     *
     * @param $groupId
     * @param boolean $recursive
     * @return array
     */
    public function getChildren($groupId, $recursive = true)
    { 
        $children = Mage::getModel('cybercom_banner/bannergroup')->getCollection()
                    ->addFieldToFilter("parent_id",$groupId);
        return $children;
    }        

    public function getChildrenCount($groupId)
    {

        $db = Mage::getSingleton('core/resource')->getConnection('core_read');
        $query = "SELECT count(group_id) as child_count 
                    FROM cybercom_banner_bannergroup
                    WHERE parent_id = $groupId";
        $child_count  = $db->fetchOne($query); 
        return $child_count;
    }    

    /**
     * Get category name
     *
     * @param Varien_Object $node
     * @return string
     */
    public function buildNodeName($node)
    {
        $result = $this->escapeHtml($node->getName());
        if ($this->_withProductCount) {
             $result .= ' (' . $node->getProductCount() . ')';
        }
        return $result;
    }

    public function getRoot($parentNodeCategory=null, $recursionLevel=3)
    {
        if (!is_null($parentNodeCategory) && $parentNodeCategory->getId()) {
            return $this->getNode($parentNodeCategory, $recursionLevel);
        }

        $rootId = 1;
        
        $tree = Mage::getModel('cybercom_banner/bannergroup')
            ->load($rootId);
        
        //$tree->addCollectionData($this->getCategoryCollection());


        
        return $tree;        
    }

 /*   public function getNode($parentNode, $recursionLevel=2)
    {
        $tree = Mage::getResourceModel('cybercom_banner/bannergroup');

        $nodeId     = $parentNode->getId();
        $parentId   = $parentNode->getParentId();

        $node = $tree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);

        if ($node && $nodeId != Mage_Catalog_Model_Category::TREE_ROOT_ID) {
            $node->setIsVisible(true);
        } elseif($node && $node->getId() == Mage_Catalog_Model_Category::TREE_ROOT_ID) {
            $node->setName(Mage::helper('catalog')->__('Root'));
        }

        $tree->addCollectionData($this->getCategoryCollection());

        return $node;
    }*/
    
    protected function _isCategoryMoveable($node)
    {
        $options = new Varien_Object(array(
            'is_moveable' => true,
            'category' => $node
        ));

        Mage::dispatchEvent('adminhtml_catalog_category_tree_is_moveable',
            array('options'=>$options)
        );

        return $options->getIsMoveable();
    }

    protected function _isParentSelectedCategory($node)
    {
        if ($node && $this->getCategory()) {
            $pathIds = $this->getCategory()->getPathIds();
            if (in_array($node->getId(), $pathIds)) {
                return true;
            }
        }

        return false;
    }

    public function getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        return Mage::app()->getStore($storeId);
    }

    public function isClearEdit()
    {
        return (bool) $this->getRequest()->getParam('clear');
    }

    public function getEditUrl()
    {
        return $this->getUrl("*/bannergroups/edit", array('_current'=>true, 'store'=>null, '_query'=>false, 'id'=>null, 'parent'=>null));
    }   

    public function getNewGroupUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/bannergroups', array('_secure' => true));
    }    

    public function getAddRootButtonHtml()
    {
        return $this->getChildHtml('add_root_button');
    }
}
