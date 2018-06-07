<?php
class Ccc_Outlook_Model_Order_Observer
{

    public function processPendingEmails()
    {
        $resource       = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $pendingEmails = Mage::getModel("outlook/ordermail")
            ->getCollection()
            ->addFieldToFilter('order_id', array('neq' => null))
            ->addFieldToFilter('mfr_id', array('neq' => null))
            ->addFieldToFilter('status_processed', array('eq' => 0))
            ->setOrder("received_date", "DESC")
        ;
        $pendingEmails->getSelect()->join(array('flat_order' => $resource->getTableName('sales/order')), 'main_table.order_id = flat_order.increment_id', array('order_entity_id' => 'entity_id', 'status', 'internal_status'));
        $pendingEmails->getSelect()->group("main_table.order_id")->group("main_table.mfr_id");
        $pendingEmails->getSelect()->limit(50);
        $pendingEmails->load();
        echo $pendingEmails->getSelect();

        $processedOrders = array();
        if ($pendingEmails->count()) {
            foreach ($pendingEmails as $_pendingEmail) {
                //skin order if processed in prev iteration
                if (in_array($_pendingEmail->getOrderEntityId(), $processedOrders)) {
                    continue;
                }
                $currentDate = Mage::getModel('core/date')->gmtdate('Y-m-d H:i:s');
                $select = $readConnection->select()
                    ->from(array('SFOI' => $resource->getTableName('sales/order_item')), array(''))
                    ->columns(array('total_item' => 'COUNT(SFOIA.item_id)', 'total_mfr_order_date' => 'SUM(IF(SFOIA.mfr_order_date IS NOT NULL,1,0))'))
                    ->join(array('SFOIA' => $resource->getTableName('ordereditor/additional_item')), 'SFOI.item_id = SFOIA.item_id', array(''))
                    ->where('SFOI.product_type != ?', 'bundle')
                    ->where("SFOI.sku NOT LIKE '%WARRANTY%'")
                    ->where('SFOI.order_id = ?', $_pendingEmail->getEn)
                    ->having('total_item = total_mfr_order_date');
                $IsAllItemOrdered = ($readConnection->fetchRow($select)) ? 1 : 0;

                if ($_pendingEmail->getOrderStatus() == 'Awaiting Acknowledgement' && 
                    $_pendingEmail->getInternalStatus() != 'onorder' && 
                    in_array($_pendingEmail->getInternalStatus(), array('ordered', 'processing')) && 
                    $IsAllItemOrdered) {
                    // set order status to onorder                    
                } elseif ($_pendingEmail->getOrderStatus() == 'Awaiting Acknowledgement' && 
                    $_pendingEmail->getInternalStatus() == 'onorder' && 
                    $IsAllItemOrdered && 
                    (date('Y-m-d H:i:s', strtotime($_pendingEmail->getReceivedDate(). ' + 6 days')) <= $currentDate)
                    ) {
                    // set order status to no reply
                }elseif ($_pendingEmail->getOrderStatus() == 'Acknowledgement Received' && 
                    $_pendingEmail->getInternalStatus() != 'Acknowledgement Received' && 
                    in_array($_pendingEmail->getInternalStatus(),array('Awaiting Acknowledgement','Acknowledgement No Reply')) &&   
                    $IsAllItemOrdered 
                    ){
                    //set Acknowledgement Received   
                }
                $processedOrders[] = $_pendingEmail->getOrderEntityId();
            }
        }

       die;
    }
}
