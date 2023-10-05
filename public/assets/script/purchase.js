// import { updateCartCount } from './index.js';

const purchaseBtn = document.getElementById('purchaseBtn');

purchaseBtn.addEventListener('click', (e) => {
     purchaseBtn.innerHTML = `<div class="threeDot-loader"></div>`;
     purchaseBtn.disabled = true;

     const slug = purchaseBtn.dataset.slug;

     addToCartApi(slug);
});

function addToCartApi(slug){
     const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

     const requestData = { slug: slug };
     // Send a POST request to the cart route to update the cart
     fetch('/api/add-to-cart', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken, // Add CSRF token
          },
          body: JSON.stringify(requestData),
      })
      .then(response => response.json())
      .then(data => {
          console.log(data);
          if(data.stage == 'success'){
               let updatedCount = data.count;
               updateCartCount(updatedCount);
               purchaseBtn.innerHTML = `<span style="color: white;">Done</span>`;
          }
      })
      .catch(error => console.error('Error:', error));
}