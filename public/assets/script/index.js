// To Update cart count on overly cart icon
function updateCartCount(updatedCount){
     const cartCountContainer = document.getElementById('cartCount');

     cartCountContainer.innerHTML = updatedCount;
}
updateCartCount(cartCount);

// Update sub total and total value
function updateTotal(subTotal, tax, total){
     const subTotalEle = document.getElementById('subTotal');
     const taxEle = document.getElementById('tax');
     const totalEle = document.getElementById('total');

     subTotalEle.innerHTML = "$ " + subTotal;
     taxEle.innerHTML = "$ " + tax;
     totalEle.innerHTML = "$ " + total;
}