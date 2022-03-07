var app = angular.module('myApp', []);

 // var site_url = 'http://localhost/pos_new/index.php';
 //var site_url = 'http://khybersoft.com/accounts/index.php';
 
app.controller('purchaseCtrl', function($scope,$http) {
    
    $scope.loader = true;//show loader gif
    //get all products
    $scope.getAllProduct= function(){
     
        $http.get('http://khybersoft.com/accounts/index.php/pos/items/get_allItems').success(function(data){
       
       $scope.loader = false;//hide loader gif
        $scope.products = data;
    
        });
    }
    
    
    //clear All the cart
    $scope.clearCart = function()
    {   
        $scope.invoice = {
            supplier_id:'',
            supplier_invoice_no:'',
            
            items: []
            };
    }
    
    //call the clear cart function to clear all product
    $scope.clearCart();
    
    //Add product to purchasing cart
    $scope.addItem = function(item_id,size_id) {
        
         
        //search product name using product id
        var returnData = $.grep($scope.products,function(element,index){
        return (element.item_id == item_id && element.size_id == size_id);
        })
       
        $scope.invoice.items.push({
                item_id: parseInt(returnData[0].item_id),
                quantity: parseInt(1),
                name: returnData[0].name,
                unit_price: parseInt(returnData[0].unit_price),
                avg_cost: parseInt(returnData[0].avg_cost),
                size_id:(returnData[0].size_id == null ? 0 : returnData[0].size_id),
                color_id:0,
                cost_price:parseInt(returnData[0].avg_cost),
                discount:parseInt(0)
            });
    }
    
    // Purchase products 
    $scope.purchaseProducts = function(){
        
        //collect all cart info and submit to db
        $scope.invoice = {
            supplier_ledger_id:parseInt($scope.supplier_ledger_id),
            supplier_invoice_no:$scope.supplier_invoice_no,
            purchaseType:$scope.purchaseType,
            register_mode:$scope.register_mode,
            amount_due:$scope.amount_due,
            description:$scope.description,
            
            items: $scope.invoice.items
            };
         ///////
         
         
         var file = 'http://khybersoft.com/accounts/index.php/pos/c_receivings/purchaseProducts';
         
        // fields in key-value pairs
        $http.post(file, $scope.invoice).success(function (data, status, headers, config) {
             
           // alert('Successfully Purchased'+data);    
            
            $scope.clearCart();       
          //'  $scope.getAllProduct();     
            window.location = "http://khybersoft.com/accounts/index.php/pos/c_receivings/receipt/"+data.receiving_id;
            
            
        }).error(function(data){
                alert(data); 
                $scope.clearCart();
            });
    }
    ///// end Purchase Products 
    
    
    
    
    //delete item from cart
    $scope.removeItem = function(index) {
        $scope.invoice.items.splice(index, 1);
    },
    
    //get discount of the cart products
    $scope.Tdiscount = function() {
        var discount = 0;
        angular.forEach($scope.invoice.items, function(item) {
            discount += (item.quantity * item.cost_price)*item.discount/100;
        })

        return discount.toFixed(2);
    }
    
    //get total of the cart products
    $scope.total = function() {
        var total = 0;
        angular.forEach($scope.invoice.items, function(item) {
            total += item.quantity * item.cost_price;
        })

        return Math.ceil(total).toFixed(2);
    }
    
    
});


////////////////////////////////////////////////////////
//THIS IS SALES CONTROLLER 
///////////////////////////////////////////////////////
app.controller('salesProductCtrl', function($scope,$http) {
    
    //get all products for sales
    $scope.getAllProduct= function(){
     
     $scope.loader = true;//show loader gif
        $http.get('http://khybersoft.com/accounts/index.php/pos/items/get_items').success(function(data){
       
       $scope.loader = false;//hide loader gif
        $scope.products = data;
    
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
    
    //Add product to Sales cart
    $scope.addItem = function(item_id,size_id) {
        
         
        //search product using product id
        var returnData = $.grep($scope.products,function(element,index){
        return (element.item_id == item_id && element.size_id == size_id);
        })
       
        $scope.invoice.items.push({
                item_id: parseInt(returnData[0].item_id),
                quantity: parseInt(1),
                name: returnData[0].name + ' - '+ returnData[0].size,
                unit_price: parseInt(returnData[0].unit_price),
                cost_price:parseInt(returnData[0].avg_cost),
                discount:parseInt(0),
                size_id:returnData[0].size_id,
                color_id:0         
            });
    },
    
    // Sale products 
    $scope.saleProducts = function(){
        
         //collect all cart info and submit to db
        $scope.invoice = {
            customer_ledger_id:parseInt($scope.customer_ledger_id),
            saleType:$scope.saleType,
            register_mode:$scope.register_mode,
            amount_due:$scope.amount_due,
            description:$scope.description,
            
            items: $scope.invoice.items
            };
         ///////
         
         
        var file = 'http://khybersoft.com/accounts/index.php/pos/c_sales/saleProducts';
         
        // fields in key-value pairs
        $http.post(file, $scope.invoice).success(function (data, status, headers, config) {
             
           // alert('Successfully Sold'+data);    
            // refresh and clear the cart
            $scope.clearCart();
           // $scope.getAllProduct();
           window.location ="http://khybersoft.com/accounts/index.php/pos/c_sales/receipt/"+data.sale_id;
            
        }).error(function(data){
                alert(data); 
            });
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

        return total.toFixed(2);
    }
});

////////////////////////////////////////////////////////
//THIS IS expense CONTROLLER 
///////////////////////////////////////////////////////
app.controller('expenseCtrl', function($scope,$http) {
    
    //get all products for sales
    $scope.getAllExpenses= function(){
     
     
        $http.get('http://khybersoft.com/accounts/index.php/pos/c_expenses/get_allExpenses').success(function(data){
       
        $scope.allExpenses = data;
    
        });
    }
    
    //clear All the cart
    $scope.clearCart = function()
    {   
        $scope.expense = {
            items: []
            };
    }
    
    //call the clear cart function to clear all product
    $scope.clearCart();
    
    
     //Add product to purchasing cart
    $scope.addItem = function(id) {
        
         
        //search expenses using exp id
        var returnData = $.grep($scope.allExpenses,function(element,index){
        return element.id == id;
        })
       
        $scope.expense.items.push({
                id: parseInt(returnData[0].id),
                 
                name: returnData[0].name,
                 
            });
    }
    
    
    // Save Expense 
    $scope.saveExpenses = function(){
        
         //collect all cart info and submit to db
        $scope.expense = {
            cash_account:$scope.cash_account,
            
            items: $scope.expense.items
            };
         ///////
         
         
         var file = 'http://khybersoft.com/accounts/index.php/pos/c_expenses/saveExpenses';
         
        // fields in key-value pairs
        $http.post(file, $scope.expense).success(function (data, status, headers, config) {
             
            alert('Successfully Paid');    
            // refresh and clear the cart
            $scope.clearCart();
            
        }).error(function(data){
                alert(data); 
            });
    }
    ///// end sale product 
    
    //delete item from cart
    $scope.removeItem = function(index) {
        $scope.expense.items.splice(index, 1);
    },
    
    //get total of the cart products
    $scope.total = function() {
        var total = 0;
        angular.forEach($scope.expense.items, function(item) {
            total += item.amount;
        })

        return total;
    }
    
});