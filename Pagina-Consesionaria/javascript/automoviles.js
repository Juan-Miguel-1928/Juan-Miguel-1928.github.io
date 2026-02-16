document.addEventListener("DOMContentLoaded", () => {

    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    const cartCount = document.querySelector(".cart-count");
    const buttons = document.querySelectorAll(".add-to-cart");
    const cartPanel = document.getElementById("cart-panel");
    const cartIcon = document.getElementById("cart-icon");
    const closeCart = document.getElementById("close-cart");
    const cartItemsContainer = document.querySelector(".cart-items");
    const cartTotal = document.getElementById("cart-total");

    function updateCartCount() {
        cartCount.textContent = cart.length;
    }

    function renderCart() {
        cartItemsContainer.innerHTML = "";
        let total = 0;

        cart.forEach((item, index) => {
            total += Number(item.price);

            const div = document.createElement("div");
            div.classList.add("cart-item");

            div.innerHTML = `
                <img src="${item.image}" class="cart-img">
                <div class="cart-info">
                    <p>${item.name}</p>
                    <p>$${Number(item.price).toLocaleString()}</p>
                </div>
                <button data-index="${index}" class="delete-btn">✕</button>
            `;

            cartItemsContainer.appendChild(div);
        });

        cartTotal.textContent = total.toLocaleString();

        localStorage.setItem("cart", JSON.stringify(cart));
        updateCartCount();
    }

    buttons.forEach(button => {
        button.addEventListener("click", () => {

            const product = {
                name: button.dataset.name,
                price: button.dataset.price,
                image: button.dataset.image
            };

            cart.push(product);
            renderCart();
        });
    });

    cartIcon.addEventListener("click", (e) => {
        e.preventDefault();
        cartPanel.classList.add("active");
        renderCart();
    });

    closeCart.addEventListener("click", () => {
        cartPanel.classList.remove("active");
    });

    cartItemsContainer.addEventListener("click", (e) => {
        if (e.target.classList.contains("delete-btn")) {
            const index = e.target.dataset.index;
            cart.splice(index, 1);
            renderCart();
        }
    });

    renderCart();
});

document.addEventListener("DOMContentLoaded", () => {

    let cart = JSON.parse(localStorage.getItem("cart")) || [];

    const cartCount = document.querySelector(".cart-count");
    const buttons = document.querySelectorAll(".add-to-cart");

    function updateCartCount() {
        cartCount.textContent = cart.length;
    }

    buttons.forEach(button => {
        button.addEventListener("click", () => {

            const name = button.dataset.name;
            const price = button.dataset.price;

            const product = {
                name: name,
                price: price
            };

            cart.push(product);

            localStorage.setItem("cart", JSON.stringify(cart));

            updateCartCount();

            alert(name + " agregado al carrito 🛒");
        });
    });

    updateCartCount();
});
var swiper = new Swiper(".mySwiper-1", {
	slidesPerView:1,
	spaceBetween: 30,
	loop:true,
	pagination: {
		el:".swiper-pagination",
		clickable: true,
	},
	navigation: {
		nextEl:".swiper-button-next",
		prevEl:".swiper-button-prev",
	}
});
var swiper = new Swiper(".mySwiper-2", {
	slidesPerView:1,
	spaceBetween: 20,
	loop:true,
	loopFillGroupWithBlank:true,
	navigation: {
		nextEl:".swiper-button-next",
		prevEl:".swiper-button-prev",
	}, 
	breakpoints : {
		0: {
			slidesPerView:1,
		},
		520: {
			slidesPerView:2,
		},
		950: {
			slidesPerView:3,
		}
	}
});
let tabInputs = document.querySelectorAll(".tabInput");

tabInputs.forEach(function(input){
	input.addEventListener('change', function () {
		let id = input.ariaValueMax;
		let thisSwiper = document.getElementById('swiper' + id);
		thisSwiper.swiper.update(); 
	})
});

