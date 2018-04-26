<?php 
class Cybercom_Vendor_Block_Adminhtml_Vendors_Edit_Tab_Grid extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
         
        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('cybercom_vendor_price_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
    }    

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('example_cmstabgrid')->__('Blocks');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('example_cmstabgrid')->__('Blocks');
    }

    /**
     * Check if tab can be displayed
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
            
    protected function _prepareCollection()
    {

        $collection = Mage::getResourceModel('catalog/product_collection')
                    ->addAttributeToSelect('*')
                    ->addAttributeToSort('created_at', 'desc');
                    

        $collection->getSelect()
                    //->reset(Zend_Db_Select::COLUMNS)
                    //->columns('id as vendorMasterId')
                    ->joinCross(array('cvv' => 'cybercom_vendor_vendordetail'),  array(''));

        $collection->getSelect()
                   ->joinLeft(array('vp' => 'cybercom_vendor_price'), 
                        'vp.product_id = e.entity_id AND vp.vendor_id = cvv.id', 
                        array('vendor_price'=>'vp.price'))
                   ->where('cvv.id = '.$this->getRequest()->getParam('id'));

        //echo $collection->getSelect();exit;
        // echo "<pre>";
        // print_r($collection->getData());
        // exit;
        $this->setCollection($collection);    
        
        parent::_prepareCollection();        
        return $this;           
    }

    public function joinProducts()
    {
        $this->join(
            array('vp' => 'cybercom_vendor/price'),
            'main_table.entity_id=vp.product_id',
            array('*')
        );
        return $this;
    }    
     
    protected function _prepareColumns()
    {

		 	
        // Add the columns that should appear in the grid
        $this->addColumn('entity_id',
            array(  
                'header'=> $this->__('Product Id'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'entity_id'
            )
        );
         
        $this->addColumn('product_name',
            array(
                'header'=> $this->__('Product Name'),
                'index' => 'name'
            )
        );

        $this->addColumn('sku',
            array(
                'header'=> $this->__('Product SKU'),
                'align' => 'left',
                'index' => 'sku',
            )
        );  

        $this->addColumn('price',
            array(
                'header'=> $this->__('Actual Price'),
                'align' => 'left',
                'index' => 'price',
            )
        );  

        $this->addColumn('vendor_price',
            array(
                'header'        => $this->__('Vendor Price'),
                'index'         => 'vendor_price',
                'renderer'      => 'cybercom_vendor/adminhtml_vendors_edit_pricetext',
                //'filter_index'  => 'vendor_price',
                'filter_condition_callback' => array($this, '_priceFilter'),
            )
        );      
        
        return parent::_prepareColumns();
    }

    public function _priceFilter($collection,$column)
    {
        // $filterValue = $column->getFilter()->getValue();
        // $collection->getSelect()->where("vendor_price =".$filterValue);
        // return $this;
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        if (empty($value)) {
            $this->getCollection()->getSelect()->where(
                 "vp.price IS NULL");
        }
        else {
            $this->getCollection()->getSelect()->where(
                 "vp.price=".$column->getFilter()->getValue());
        }

        return $this;        
    }
     
    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl()
    {
      return $this->getUrl('*/*/gridPrice', array('_current'=>true));
    }
}
?>