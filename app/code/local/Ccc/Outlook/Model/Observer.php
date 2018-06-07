<?php
class Ccc_Outlook_Model_Observer
{
    /** 
     * Add new column to orders grid
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addColumnsToGrid(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        
        // Check whether the loaded block is the orders grid block
        if (!($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid)
            || $block->getNameInLayout() != 'sales_order.grid'
        ) {
            return $this;
        }

        
        $block->addColumnAfter('totalmail', [
            'header' => $block->__('Total Mail Count'),
            'index' => 'totalmail',
            'filter_condition_callback' => array($this, 'totalMailFilter')
        ], 'order_verified');

        return $this;
    }

    /***
    * Add Filter above the newly added column 
    ***/
    public function totalMailFilter($collection, $column)
    {
       
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }       
        
        $collection->getSelect()->where('outlook.totalmail = '.$value); 
                   

       return $this;
    }

    /**
     * Add data to the collection to be displayed to the added row 
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function prepareOrderGridCollection(Varien_Event_Observer $observer)
    {
        $collection = $observer->getEvent()->getOrderGridCollection();

        $outlookCollection = Mage::getModel('outlook/ordermail')->getCollection();
        $outlookCollection->getSelect()
        ->reset(Zend_Db_Select::COLUMNS)
        ->columns('order_id as increment_id,ifnull(count(*),0) as totalmail')
        ->group('main_table.order_id');

        
        $collection->getSelect()
            ->joinLeft(['outlook' => $outlookCollection->getSelect()],
                'outlook.increment_id = main_table.increment_id',
                ['totalmail']);

        return $this;
    }    

    public function readMail()
    {
        //For testing purpose
        $outlookModel = Mage::getModel("outlook/outlook");
        $filename = $outlookModel->getTokenFilepath()."/1sb.txt";
        $content  = file_get_contents($filename)."====== Read Mail ".date('Y-m-d H:i:s');
        file_put_contents($filename, $content);

        $orderModel = Mage::getModel("outlook/ordermail");
        $orderMailCount = $orderModel->readMail(); 
        $result = $orderModel->getMfr(); 
        return $this;
    }    

    public function tokenRefresh()
    {

        //For testing purpose
        $outlookModel     = Mage::getModel("outlook/outlook");
        $filename = $outlookModel->getTokenFilepath()."/1sb.txt";
        $content  = file_get_contents($filename)."====== Token refresh ".date('Y-m-d H:i:s');
        file_put_contents($filename, $content);

        $orderModel = Mage::getModel("outlook/ordermail");
        $hasToken = $orderModel->refreshToken(); 
        return $this;        
    }

    public function downloadAttachments()
    {

        //For testing purpose
        $outlookModel     = Mage::getModel("outlook/outlook");
        $filename = $outlookModel->getTokenFilepath()."/1sb.txt";
        $content  = file_get_contents($filename)."====== Download ".date('Y-m-d H:i:s');
        file_put_contents($filename, $content);        
    
        $orderModel     = Mage::getModel("outlook/ordermail");
        $isDownloaded   = $orderModel->downloadAttchment(); 
        return $this;        
    }    
}