<?php
class Cybercom_Vendor_Block_Adminhtml_Vendors_Gridexport extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
         
        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('cybercom_vendor_vendordetail_gridexport');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }
     
        
    protected function _prepareCollection()
    {
        // Get and set our collection for the grid
        $collection = Mage::getModel('cybercom_vendor/vendordetail')->getCollection();
        //$collection ->addExpressionFieldToSelect('vendorMasterId','ANY_VALUE(id)',array('name'));
                    // ->addAttributeToSelect('*');
        //since SUM requires a GROUP BY...group by some column
        $collection ->getSelect()
                    ->reset(Zend_Db_Select::COLUMNS)
                    ->columns('ANY_VALUE(id) as vendorMasterId')
                    ->columns('ANY_VALUE(name) as vendor_name')
                    ->columns('ANY_VALUE(status) as vendor_status')
                    ->joinLeft(array('cvp' => 'cybercom_vendor_price'), 
                            'cvp.vendor_id = main_table.id', 
                            array('vendor_price'=>'ANY_VALUE(cvp.price)','id'=>'ANY_VALUE(cvp.entity_id)'));
        $collection ->getSelect()
                    ->joinLeft(array('cpev' => 'catalog_product_entity_varchar'), 
                            'cpev.entity_id = cvp.product_id', 
                            array('product_name'=>'ANY_VALUE(cpev.value)',
                                'attribute_id'=>'ANY_VALUE(cpev.attribute_id)',
                                'store_id'=>'ANY_VALUE(cpev.store_id)'));
        $collection ->getSelect()
                    ->joinLeft(array('cpe' => 'catalog_product_entity'),
                        'cpe.entity_id = cvp.product_id',
                        array('product_sku' => 'ANY_VALUE(cpe.sku)'));   
        $collection ->getSelect()
                    ->joinLeft(array('price_index' => 'catalog_product_index_price'),
                        'price_index.entity_id = cvp.product_id',
                        array('product_price' => 'ANY_VALUE(price_index.final_price)'));                                                          
        $collection ->getSelect()
                    ->joinLeft(array('eav' => 'eav_attribute'), 
                            'eav.attribute_id = cpev.attribute_id', 
                            array('vendor_price_id'=>'ANY_VALUE(cvp.entity_id)'))
                    ->where("(eav.entity_type_id=4 AND eav.attribute_code='name') OR (eav.entity_type_id is null)")
                    ->group('main_table.id')   
                    ->group('price_index.entity_id');                                     
        
        //echo $collection->getSelect();exit;


        $this->setCollection($collection);         
        return parent::_prepareCollection();
    }
     
    protected function _prepareColumns()
    {
        // Add the columns that should appear in the grid
        $this->addColumn('id',
            array(  
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'id',
                'is_system' => true,
            )
        );

        $this->addColumn('vendorMasterId',
            array(  
                'header'=> $this->__('Vendor ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'vendorMasterId'
            )
        );        
         
        $this->addColumn('name',
            array(
                'header'=> $this->__('Vendor Name'),
                'index' => 'vendor_name'
            )
        );

        $this->addColumn('status',
            array(
                'header'=> $this->__('Status'),
                'align' => 'left',
                'index' => 'vendor_status',
                'type' => 'options',
                'options' => array( 1 => 'Enable', 0 => 'Disable' )
            )
        );     
        $this->addColumn('vendor_product',
            array(
                'header'=> $this->__('Vendor Product'),
                'align' => 'left',
                'index' => 'product_name',
            )
        );  

        $this->addColumn('vendor_product_sku',
            array(
                'header'=> $this->__('SKU'),
                'align' => 'left',
                'index' => 'product_sku',
            )
        );         


        $this->addColumn('product_actual_price',
            array(
                'header'=> $this->__('Actual Price'),
                'align' => 'left',
                'index' => 'product_price',
            )
        );       

        $this->addColumn('vendor_price',
            array(
                'header'=> $this->__('Vendor Price'),
                'align' => 'left',
                'index' => 'vendor_price',
            )
        );    

         
        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection() {
        echo 111;exit;
        if(!$this->_isExport) {
            $this->removeColumn('vendor_price');
        }
    }     
}