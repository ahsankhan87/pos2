////////////////////////////////////////////////////////
//THIS IS estimate CONTROLLER 
///////////////////////////////////////////////////////
app.controller('estimateProductCtrl', function($scope,$http,$timeout) {
    
    $scope.sale_date = new Date();
    $scope.delivery_date = new Date();

    //get all products for estimate
    $scope.getAllProduct= function(){
     
     $scope.loader = true;//show loader gif
        
        $http.get(site_url+'/pos/Items/get_items',{cache: true}).then(function(response){
       
        $scope.loader = false;//hide loader gif
        $scope.products = response.data;
        console.log(response.data);
        });
    }
    
    //get all products for estimate
    $scope.getCustomerCurrency = function(customer_id){
       
        $scope.curr_loader = true;//show loader gif
       //INITIALIZE
        $scope.customer_currency_id = 0;
        $scope.customer_currency_code = '';
        $scope.customer_currency_name = '';
        $scope.customer_currency_symbol = '';
        
        $http.get(site_url+'/pos/C_estimate/getCustomerCurrencyJSON/'+customer_id).then(function(response){
        
        if(response.data.length > 0)
        {
            $scope.customer_currency_id = parseInt(response.data[0].id);
            $scope.customer_currency_code = response.data[0].code;
            $scope.customer_currency_name = response.data[0].name;
            $scope.customer_currency_symbol = response.data[0].symbol;
        //console.log(response);
        }
        
        $http.get(site_url+'/pos/C_currencies/currency_rate/'+$scope.customer_currency_code).then(function(response){
        
        $scope.exchange_rate = parseFloat(response);
        //console.log(response);
            });
        $scope.curr_loader = false;//hide loader gif
        });
        
    }
    
    //get all products for estimate
    $scope.getsupplierCurrency = function(supplier_id){
       
       $scope.curr_loader = true;//show loader gif
       //INITIALIZE
        $scope.customer_currency_id = 0;
        $scope.customer_currency_code = '';
        $scope.customer_currency_name = '';
        $scope.customer_currency_symbol = '';
        
        $http.get(site_url+'/pos/C_receivings/getSupplierCurrencyJSON/'+supplier_id).then(function(response){
        
        if(response.data.length > 0)
        {
            $scope.customer_currency_id = parseInt(response.data[0].id);
            $scope.customer_currency_code = response.data[0].code;
            $scope.customer_currency_name = response.data[0].name;
            $scope.customer_currency_symbol = response.data[0].symbol;
        //console.log(response);
        }
        
        $http.get(site_url+'/pos/C_currencies/currency_rate/'+$scope.supplier_currency_code).then(function(response){
        
        $scope.exchange_rate = parseFloat(response);
        //console.log(response);
            });
        
        $scope.curr_loader = false;//hide loader gif
        });
        
    }
    
    //clear All the cart
    $scope.clearCart = function()
    {   
        $scope.invoice = {
           items: []
            };
    }
    
    //call the clear cart function to clear all product
    $scope.clearCart();
    
    //add product by barcode in estimate form
    $scope.addItemByBarcode = function (barcode){
            $timeout(function () {
               // $scope.barcode; //from input
              
        //search product using barcode
        var returnData = $.grep($scope.products,function(element,index){
        return (element.barcode == barcode);
        })
       //IF QTY IS ZERO THEN ALERT ERROR MSG
       if(parseInt(returnData[0].quantity) <= 0 && parseInt(returnData[0].service) == 0)
       {
            alert('Product is not in stock, Please Purchase Product');
       }else //ADD PR0DUCT TOTHE CART
       {
        $scope.invoice.items.push({
                item_id: parseInt(returnData[0].item_id),
                quantity: parseFloat(1),
                item_qty:parseFloat(returnData[0].quantity),
                name: returnData[0].name + (returnData[0].size == null ? '' : ' '+returnData[0].size),
                unit_price: parseFloat(returnData[0].unit_price),
                cost_price:parseFloat(returnData[0].avg_cost),
                discount_percent:parseFloat(0),
                discount_value:parseFloat(0),
                size_id:(returnData[0].size_id == null ? 0 : returnData[0].size_id),
                unit_id:(returnData[0].unit_id == null ? 0 : returnData[0].unit_id),
                color_id:0,
                tax_id:parseFloat(returnData[0].tax_id),
                tax_rate:parseFloat((returnData[0].tax_rate == null ? 0 : returnData[0].tax_rate)),
                tax_name:returnData[0].tax_name,
                exchange_rate:0,
                currency_id:0,
                service:parseInt(returnData[0].service),
                avg_cost: parseFloat(returnData[0].avg_cost),
                inventory_acc_code : returnData[0].inventory_acc_code
            });
        }
        $scope.barcode = '';
        },10);
    }
        
    
    //Add product to estimate cart
    $scope.addItem = function(item_id,size_id) {
        
        //search product using product id
        var returnData = $.grep($scope.products,function(element,index){
        return (element.item_id == item_id && element.size_id == size_id);
        })
       
       if(parseInt(returnData[0].quantity) <= 0 && parseInt(returnData[0].service) == 0 && $scope.register_mode == 'sale')
       {
            alert('Product is not in stock, Please Purchase Product');
       }else //ADD PR0DUCT TOTHE CART
       {
         $scope.invoice.items.push({
                item_id: parseInt(returnData[0].item_id),
                quantity: parseFloat(1),
                item_qty:parseFloat(returnData[0].quantity),
                name: returnData[0].name + (returnData[0].size == null ? '' : ' '+returnData[0].size),
                unit_price: parseFloat(returnData[0].unit_price),
                cost_price:parseFloat(returnData[0].avg_cost),
                discount_percent:parseFloat(0),
                discount_value:parseFloat(0),
                exchange_rate:0,
                currency_id:0,
                tax_id:parseFloat(returnData[0].tax_id),
                tax_rate:parseFloat((returnData[0].tax_rate == null ? 0 : returnData[0].tax_rate)),
                tax_name:returnData[0].tax_name,
                service:parseInt(returnData[0].service),
                size_id:(returnData[0].size_id == null ? 0 : returnData[0].size_id),
                unit_id:(returnData[0].unit_id == null ? 0 : returnData[0].unit_id),
                color_id:0,
                inventory_acc_code : returnData[0].inventory_acc_code
            });
       }
       //console.log($scope.invoice.items);
    },
    
    // Sale products 
    $scope.saleProducts = function(){
        
        var confirmSale = confirm('Are you absolutely sure you want to sale?');
        
        if (confirmSale) {
            
        if($scope.invoice.items.length > 0)
        {
            if(parseInt($scope.customer_id) !== 0 || parseInt($scope.supplier_id) !== 0 || $scope.supplier_id == null || $scope.customer_id == null)
            
            {
                $scope.cart_loader = true;//show loader gif
                
                 //collect all cart info and submit to db
                $scope.invoice = {
                    customer_id:parseInt($scope.customer_id),
                    emp_id:$scope.emp_id,
                    saleType:$scope.saleType,
                    register_mode:$scope.register_mode,
                    amount_due:0,//$scope.amount_due,
                    total_amount:$scope.total_amount,
                    total_tax:$scope.total_tax(),
                    description:$scope.description,
                    //discount:$scope.discount, //DISCOUNT BY VALUES
                    discount: ($scope.discount_value === undefined ? '' : $scope.discount_value), //BY PERCENT
                    sale_date:$scope.sale_date,
                    exchange_rate: ($scope.exchange_rate === undefined ? '' : $scope.exchange_rate),
                    currency_id:($scope.customer_currency_id === undefined ? '' : $scope.customer_currency_id),
                    supplier_id:($scope.supplier_id === undefined ? '' : $scope.supplier_id),
                    advance:$scope.advance,
                    delivery_date:$scope.delivery_date,

                    items: $scope.invoice.items
                    };
                 ///////
                 
                 
                 var file = site_url+'/pos/C_estimate/saleProducts';
                 
                // fields in key-value pairs
                $http.post(file, $scope.invoice).then(function (response) {
                     
                    //alert(response);    
                    //console.log(response);
                    //refresh and clear the cart
                    $scope.cart_loader = false;//hide loader gif
                    $scope.clearCart();
                   // $scope.getAllProduct(); 
                   if(response.data.invoice_no == 'no-posting-type')
                   {
                     alert('Please assign posting type to customer otherwise amount will not be post to accounts');
                     window.location = site_url+"/pos/C_estimate";
                   }else
                   {
                      window.location = site_url+"/pos/C_estimate/receipt/"+response.data.invoice_no; 
                      console.log(response.data);
                   }
                   
                    
                });
            }
            else{
                alert('Please select customer');
            }
            
        }
        else
        {
            alert('Please select product');
        }
        
        }//confirm msg
    }
    ///// end sale product 
    
    
     //delete item from cart
    $scope.removeItem = function(index) {
        $scope.invoice.items.splice(index, 1);
    },
    
    //get discount of the cart products ONLY BY VALUES
    $scope.Tdiscount = function() {
        var discount = 0;
        angular.forEach($scope.invoice.items, function(item) {
            discount += (item.quantity * item.unit_price)*item.discount_percent/100;
        })

        return discount.toFixed(4);
    }
    
    //IT USES DISCOUNT PERCENT
    $scope.Tdiscount_value = function() {
        var discount = 0;
        angular.forEach($scope.invoice.items, function(item) {
            discount += item.discount_value;
        })
        $scope.discount_value = discount.toFixed(4);
        return discount.toFixed(4);
    }
    
     //CALCULATE TOTAL TAX
    $scope.total_tax = function() {
        var tax = 0;
        angular.forEach($scope.invoice.items, function(item) {
            if(!isNaN(item.tax_rate))
            {
                tax += ((item.quantity * item.unit_price)-item.discount_value)*item.tax_rate/100;    
            }
            
        })
        return tax.toFixed(4);
    }
    
    
    //get total of the cart products
    $scope.total = function() {
        var total = 0;
        angular.forEach($scope.invoice.items, function(item) {
            total += ((item.quantity * item.unit_price)+((item.quantity * item.unit_price)-item.discount_value)*item.tax_rate/100);//unit price + tax
        })
        $scope.total_amount = total.toFixed(4);
        
        return total.toFixed(4);
    }
});