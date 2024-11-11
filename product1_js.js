const images = document.querySelectorAll('.image-slider img');
const prevBtn = document.querySelector('.prev-btn');
const nextBtn = document.querySelector('.next-btn');
let currentIndex = 0;

images[0].classList.add('active');

prevBtn.addEventListener('click', () => {
    images[currentIndex].classList.remove('active');
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    images[currentIndex].classList.add('active');
});

nextBtn.addEventListener('click', () => {
    images[currentIndex].classList.remove('active');
    currentIndex = (currentIndex + 1) % images.length;
    images[currentIndex].classList.add('active');
});

function buyNow() {
    const productName = document.getElementById('name').textContent;
    const productPrice = document.getElementById('price').textContent;

    fetch('/buy-product', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            name: productName,
            price: productPrice
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product purchased successfully!');
        } else {
            alert('Error purchasing the product.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
