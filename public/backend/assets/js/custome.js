document.addEventListener("DOMContentLoaded", function(){

    let productSearchInput = document.getElementById("product_search");
    let warehouseDropdown = document.getElementById("warehouse_id");
    let productList = document.getElementById("product_list");
    let warehouseError = document.getElementById("warehouse_error");
    let orderItemsTableBody = document.querySelector("tbody");

    productSearchInput.addEventListener("keyup", function(){
        let query = this.value;
        let warehouse_id = warehouseDropdown.value;

        if (!warehouse_id ) {
            warehouseError.classList.remove('d-none'); 
            productList.innerHTML = "";
            return;
        } else{
            warehouseError.classList.add('d-none'); 
        }
        if (query.length > 1) {
            fetchProducts(query,warehouse_id);
        }else{
            productList.innerHTML = "";
        }
    });


    function fetchProducts(query,warehouse_id) {
        fetch(productSearchUrl + "?query=" + query + "&warehouse_id=" + warehouse_id)
            .then(response => response.json())
            .then(data => {
                productList.innerHTML = "";
                if (data.length > 0) {
                    data.forEach(product => {
                        let item = `<a href="#" class="list-group-item list-group-item-action product-item"
                            data-id="${product.id}"
                            data-code="${product.code}"
                            data-name="${product.name}"
                            data-cost="${product.price}"
                            data-stock="${product.product_qty}">
                            <span class="mdi mdi-text-search"></span>
                            ${product.code} - ${product.name}
                            </a> `;
                            productList.innerHTML += item;
                    });

        // add event listener for product selection 
        document.querySelectorAll(".product-item").forEach(item => {
            item.addEventListener("click", function(e) {
                e.preventDefault();
                addProductToTable(this);
            });
        });
 
        } else {
            productList.innerHTML = '<p class="text-muted">No Product Found</p>'
        }
    });
        
    }



     ///// Add Product in to the table 
    function addProductToTable(productElement) {
        let productId = productElement.getAttribute("data-id");
        let productCode = productElement.getAttribute("data-code");
        let productName = productElement.getAttribute("data-name");
        let netUnitCost = parseFloat(productElement.getAttribute("data-cost"));
        let stock = parseInt(productElement.getAttribute("data-stock"));

        // Check if product already exists in table
        if (document.querySelector(`tr[data-id="${productId}"]`)) {
              alert("Product already added.");
              return;
        }
    
        let row = `
      <tr data-id="${productId}">
          <td>
              ${productCode} - ${productName} 
              <button type="button" class="btn btn-primary btn-sm edit-discount-btn"
                  data-id="${productId}" 
                  data-name="${productName}" 
                  data-cost="${netUnitCost}"
                  data-bs-toggle="modal">
                  <span class="mdi mdi-book-edit "></span>
              </button>
              <input type="hidden" name="products[${productId}][id]" value="${productId}">
              <input type="hidden" name="products[${productId}][name]" value="${productName}">
              <input type="hidden" name="products[${productId}][code]" value="${productCode}">
          </td>
          <td>${netUnitCost.toFixed(2)}
              <input type="hidden" name="products[${productId}][cost]" value="${netUnitCost}">
          </td>
          <td style="color:#ffc121">${stock}</td>
          <td>
              <div class="input-group">
                  <button class="btn btn-outline-secondary decrement-qty" type="button">−</button>
                  <input type="text" class="form-control text-center qty-input"
                      name="products[${productId}][quantity]" value="1" min="1" max="${stock}"
                      data-cost="${netUnitCost}" style="width: 30px;">
                  <button class="btn btn-outline-secondary increment-qty" type="button">+</button>
              </div>
          </td>
          <td>
              <input type="number" class="form-control discount-input"
                  name="products[${productId}][discount]" value="0" min="0" style="width:100px">
          </td>
          <td class="subtotal">${netUnitCost.toFixed(2)}</td>
          <td><button class="btn btn-danger btn-sm remove-product"><span class="mdi mdi-delete-circle mdi-18px"></span></button></td>
      </tr>
  `;

        orderItemsTableBody.innerHTML += row;
        productList.innerHTML = "";
        productSearchInput.value = ""; 

        updateEvents();
        updateGrandTotal();
        
  }


  function updateEvents() {
    document.querySelectorAll(".qty-input, .discount-input").forEach(input => {
          input.addEventListener("input", function () {
                let row = this.closest("tr");
                let qty = parseInt(row.querySelector(".qty-input").value) || 1;
                let unitCost = parseFloat(row.querySelector(".qty-input").getAttribute("data-cost")) || 0;
                let discount = parseFloat(row.querySelector(".discount-input").value) || 0;

                let subtotal = (unitCost * qty) - discount;
                row.querySelector(".subtotal").textContent = subtotal.toFixed(2);

                updateGrandTotal();
          });
    });

    // Increment quantity
    document.querySelectorAll(".increment-qty").forEach(button => {
          button.addEventListener("click", function () {
                let input = this.closest(".input-group").querySelector(".qty-input");
                let max = parseInt(input.getAttribute("max"));
                let value = parseInt(input.value);
                if (value < max) {
                      input.value = value + 1;
                      updateSubtotal(this.closest("tr"));
                }
          });
    });

    // Decrement quantity
    document.querySelectorAll(".decrement-qty").forEach(button => {
          button.addEventListener("click", function () {
                let input = this.closest(".input-group").querySelector(".qty-input");
                let min = parseInt(input.getAttribute("min"));
                let value = parseInt(input.value);
                if (value > min) {
                      input.value = value - 1;
                      updateSubtotal(this.closest("tr"));
                }
          });
    });
    
    // Remove product row
    document.querySelectorAll(".remove-product").forEach(button => {
          button.addEventListener("click", function () {
                this.closest("tr").remove();
                updateGrandTotal();
          });
    });
}

function updateSubtotal(row) {
    let qty = parseFloat(row.querySelector(".qty-input").value);
    let discount = parseFloat(row.querySelector(".discount-input").value) || 0;
    let netUnitCost = parseFloat(row.querySelector(".qty-input").dataset.cost);

    // Calculate subtotal after discount
    let subtotal = (netUnitCost * qty) - discount;
    row.querySelector(".subtotal").innerText = subtotal.toFixed(2);

    // Update Grand Total
    updateGrandTotal();
}




// Grand total update function
function updateGrandTotal() {
    let grandTotal = 0;

    // Calculate subtotal sum
    document.querySelectorAll(".subtotal").forEach(function (item) {
          grandTotal += parseFloat(item.textContent) || 0;
    });

    // Get discount and shipping values
    let discount = parseFloat(document.getElementById("inputDiscount").value) || 0;
    let shipping = parseFloat(document.getElementById("inputShipping").value) || 0;

    // Apply discount and add shipping cost
    grandTotal = grandTotal - discount + shipping;

    // Ensure grand total is not negative
    if (grandTotal < 0) {
          grandTotal = 0;
    }

    // Update Grand Total display
    document.getElementById("grandTotal").textContent = `TK ${grandTotal.toFixed(2)}`;

    document.querySelector("input[name='grand_total']").value = grandTotal.toFixed(2);

}

});