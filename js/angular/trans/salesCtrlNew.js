////////////////////////////////////////////////////////
//THIS IS SALES CONTROLLER 
///////////////////////////////////////////////////////
app.controller('salesProductCtrl', function($scope,$http,$timeout) {
    
    $scope.sale_date = new Date();
    
    //get all products for sales
    $scope.getAllProduct= function(){
     
     $scope.loader = true;//show loader gif
        
        $http.get(site_url+'/pos/Items/get_items').success(function(data){
       
        $scope.loader = false;//hide loader gif
        $scope.products = data;
        console.log(data);
        });
    }
    
    //get all products for sales
    $scope.getCustomerCurrency = function(customer_id){
       
        $scope.curr_loader = true;//show loader gif
       //INITIALIZE
        $scope.customer_currency_id = 0;
        $scope.customer_currency_code = '';
        $scope.customer_currency_name = '';
        $scope.customer_currency_symbol = '';
        
        $http.get(site_url+'/trans/C_sales/getCustomerCurrencyJSON/'+customer_id).success(function(data){
        
        $scope.customer_currency_id = parseInt(data[0].id);
        $scope.customer_currency_code = data[0].code;
        $scope.customer_currency_name = data[0].name;
        $scope.customer_currency_symbol = data[0].symbol;
        //console.log(data);
        
        $http.get(site_url+'/pos/C_currencies/currency_rate/'+$scope.customer_currency_code).success(function(data){
        
        $scope.exchange_rate = parseFloat(data);
        //console.log(data);
            });
        $scope.curr_loader = false;//hide loader gif
        });
        
    }
    
    //get all products for sales
    $scope.getsupplierCurrency = function(supplier_id){
       
       $scope.curr_loader = true;//show loader gif
       //INITIALIZE
        $scope.customer_currency_id = 0;
        $scope.customer_currency_code = '';
        $scope.customer_currency_name = '';
        $scope.customer_currency_symbol = '';
        
        $http.get(site_url+'/trans/C_receivings/getSupplierCurrencyJSON/'+supplier_id).success(function(data){
        
        $scope.customer_currency_id = parseInt(data[0].id);
        $scope.customer_currency_code = data[0].code;
        $scope.customer_currency_name = data[0].name;
        $scope.customer_currency_symbol = data[0].symbol;
        //console.log(data);
        
        
        $http.get(site_url+'/pos/C_currencies/currency_rate/'+$scope.supplier_currency_code).success(function(data){
        
        $scope.exchange_rate = parseFloat(data);
        //console.log(data);
            });
        
        $scope.curr_loader = false;//hide loader gif
        });
        
    }
    
    //clear All the cart
    $scope.clearCart = function()
    {   
        $scope.invoice = {
           items: [
                   {
                        item_id: parseInt(0),
                        quantity: parseFloat(1),
                        item_qty:parseFloat(1),
                        name: '',
                        unit_price: parseFloat(0),
                        cost_price:parseFloat(0),
                        /*unit:'',*/
                        size_id:0,
                        color_id:0,
                        exchange_rate:0,
                        currency_id:0,
                        service:parseInt(0),
                        avg_cost: parseFloat(0)
                   }
                ]
            };
    }
    
    //call the clear cart function to clear all product
    $scope.clearCart();
    
    //add product by barcode in sales form
    $scope.addItemByBarcode = function (){
            $timeout(function () {
                $scope.barcode; //from input
               
        //search product using barcode
        var returnData = $.grep($scope.products,function(element,index){
        return (element.barcode == $scope.barcode);
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
                name: returnData[0].name + ' - '+ returnData[0].size,
                unit_price: parseFloat(returnData[0].unit_price),
                cost_price:parseFloat(returnData[0].avg_cost),
                /*unit:'',*/
                size_id:(returnData[0].size_id == null ? 0 : returnData[0].size_id),
                color_id:0,
                exchange_rate:0,
                currency_id:0,
                service:parseInt(returnData[0].service),
                avg_cost: parseFloat(returnData[0].avg_cost)
            });
        }
        $scope.barcode = '';
        },10);
    }
        
    
    //Add product to Sales cart
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
                item_id: $scope.product_id,
                quantity: $scope.quantity,
                item_qty:parseFloat(returnData[0].quantity),
                name: returnData[0].name + ' - '+ returnData[0].size,
                unit_price: parseFloat(returnData[0].unit_price),
                cost_price:parseFloat(returnData[0].avg_cost),
                /*unit:'',*/
                exchange_rate:0,
                currency_id:0,
                service:parseInt(returnData[0].service),
                size_id:(returnData[0].size_id == null ? 0 : returnData[0].size_id),
                color_id:0,
            });
       }
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
                    saleType:$scope.saleType,
                    register_mode:$scope.register_mode,
                    amount_due:0,//$scope.amount_due,
                    total_amount:$scope.total_amount,
                    description:$scope.description,
                    discount:$scope.discount,
                    sale_date:$scope.sale_date,
                    exchange_rate: ($scope.exchange_rate === undefined ? '' : $scope.exchange_rate),
                    currency_id:($scope.customer_currency_id === undefined ? '' : $scope.customer_currency_id),
                    supplier_id:($scope.supplier_id === undefined ? '' : $scope.supplier_id),
                    
                    items: $scope.invoice.items
                    };
                 ///////
                 
                 
                 var file = site_url+'/trans/C_sales/saleProducts';
                 
                // fields in key-value pairs
                $http.post(file, $scope.invoice).success(function (data, status, headers, config) {
                     
                    //alert(data);    
                    console.log(data);
                    // refresh and clear the cart
                    $scope.cart_loader = false;//hide loader gif
                    $scope.clearCart();
                   // $scope.getAllProduct(); 
                   if(data.invoice_no == 'no-posting-type')
                   {
                     alert('Please assign posting type to customer otherwise amount will not be post to accounts');
                     window.location = site_url+"/trans/C_sales";
                   }else
                   {
                      window.location = site_url+"/trans/C_sales/receipt/"+data.invoice_no; 
                   }
                   
                    
                }).error(function(data){
                        console.log(data);
                        alert(data); 
                    });
            }
            else{
                alert('please select customer');
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
    
    //get discount of the cart products
    $scope.Tdiscount = function() {
        var discount = 0;
        angular.forEach($scope.invoice.items, function(item) {
            discount += (item.quantity * item.unit_price)*item.discount/100;
        })

        return discount.toFixed(2);
    }
    
    //get total of the cart products
    $scope.total = function() {
        var total = 0;
        angular.forEach($scope.invoice.items, function(item) {
            total += item.quantity * item.unit_price;
        })
        $scope.total_amount = total.toFixed(2);
        
        return total.toFixed(2);
    }
});