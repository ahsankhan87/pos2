<?php header("Content-Type:text/xml"); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>';

$customer =  @$this->M_customers->get_customers(@$sales_items[0]['customer_id']);
$total_amount = (@$sales_items[0]['total_amount'] + @$sales_items[0]['total_tax'] - @$sales_items[0]['discount_value']);

// Set headers for download
$filename = @$sales_items[0]['invoice_no'] . ".xml";

header('Content-type: text/xml');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Your code to read the XML file from the server
?>

<Invoice xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2">
    <cbc:ID><?php echo $invoice_no ?></cbc:ID>
    <cbc:IssueDate><?php echo @$sales_items[0]['sale_date']; ?></cbc:IssueDate>
    <cbc:DueDate><?php echo @$sales_items[0]['due_date']; ?></cbc:DueDate>
    <cac:InvoicePeriod>
        <cbc:StartDate><?php echo @$sales_items[0]['sale_date']; ?></cbc:StartDate>
        <cbc:EndDate><?php echo @$sales_items[0]['sale_date']; ?></cbc:EndDate>
    </cac:InvoicePeriod>
    <cac:AccountingSupplierParty />
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PartyName>
                <cbc:Name><?php echo @$customer[0]['store_name']; ?></cbc:Name>
            </cac:PartyName>
        </cac:Party>
    </cac:AccountingCustomerParty>
    <cac:LegalMonetaryTotal>
        <cbc:PayableAmount currencyID="<?php echo $_SESSION['home_currency_code']; ?>"><?php echo $total_amount; ?></cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
    <cac:InvoiceLine>
        <cbc:ID>1</cbc:ID>
        <cbc:LineExtensionAmount currencyID="<?php echo $_SESSION['home_currency_code']; ?>"><?php echo $total_amount; ?></cbc:LineExtensionAmount>
        <cac:Item>
            <cbc:Description><?php echo @$sales_items[0]['description']; ?></cbc:Description>
        </cac:Item>
    </cac:InvoiceLine>
</Invoice>