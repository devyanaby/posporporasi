@extends('layouts.default', [
    'paceTop' => true,
    'appContentFullHeight' => true,
    'appContentClass' => 'p-0',
    'appSidebarHide' => true,
    'appHeaderHide' => true,
])

@section('title', 'Pembelian')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/pembelian.css') }}">
    <style>
        .product .card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .nav-item .nav-link .icon-wrapper {
    display: flex;
    flex-direction: column;  
    align-items: center;  
    gap: 4px;   
}

.nav-item .nav-link .icon-wrapper i {
    font-size: 24px;  
}

.nav-item .nav-link .icon-wrapper span {
    font-size: 14px;   
    text-align: center;   
    white-space: nowrap;   
}


        .custom-bg {
            background-color: #f9f9ff;
        }

        .product .card-img-top {
            object-fit: cover;
            height: 200px;
            width: 100%;
        }

        .product .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product .card-title {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }

        .product .card-text {
            font-size: 0.9rem;
            flex-grow: 1;
            margin-bottom: 15px;
        }

        .add-to-cart {
            margin-top: 10px;
        }

        .cart-item {
            display: flex;
            flex-direction: column;
        }
        .quantity-controls {
    display: flex;
    align-items: center;   
    gap: 12px;  
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 8px; 
}

.adjust-quantity {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;   
    height: 28px;  
    font-size: 14px;  
    border-radius: 4px;  
    background-color: #00acac;  
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.adjust-quantity:hover {
    background-color: #0cb4cb;  
    transform: scale(1.1);  
}

.adjust-quantity:active {
    background-color: #09c3a7;  
    transform: scale(0.95);  
}

.adjust-quantity:focus {
    outline: none;  
    box-shadow: 0 0 0 2px rgba(2, 164, 170, 0.5);  
}

.adjust-quantity:disabled {
    background-color: #d9534f; 
    cursor: not-allowed;
}


.product-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}

    </style>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light px-4 py-3">
        <a class="navbar-brand d-flex align-items-center" href="/dashboard" style="font-size: 1.5rem;">
            <img src="/assets/img/pos/logo.svg" width="30" height="30" alt="Pine & Dine" class="me-2">
            Pine & Dine
        </a>
        <div class="navbar-collapse collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="/pembelian" class="nav-link fs-5">
                        <i class="fa fa-shopping-cart"></i> Order
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/pembayaran" class="nav-link fs-5">
                        <i class="fa fa-cash-register"></i> Checkout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Kategori Menu -->
            <div class="col-md-1">
                <div class="col-md-1">
                    <div class="sticky-navbar">
                        <ul class="nav flex-column nav-pills">
                            <li class="nav-item">
                                <button class="nav-link active" data-filter="all">
                                    <div class="icon-wrapper">
                                        <i class="fa fa-home"></i>
                                        <span>Semua Menu</span>
                                    </div>
                                </button>
                            </li>
                            @foreach ($kategori as $item)
                                <li class="nav-item">
                                    <button class="nav-link" data-filter="{{ $item->id }}">
                                        <div class="icon-wrapper">
                                            <i class="fa {{ $item->icon }}"></i>
                                            <span>{{ $item->nama_kategori }}</span>
                                        </div>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                        
                        
                    </div>
                </div>
            </div>
            <!-- Produk dan Keranjang -->
            <div class="col-md-8">
                <div class="row">
                    @foreach ($produk as $item)
                        <div class="col-md-4 mb-4 product" data-kategori="{{ $item->id_kategori }}">
                            <div class="card shadow-sm">
                                <img src="{{ asset('storage/' . $item->foto) }}" class="card-img-top"
                                    alt="{{ $item->nama_produk }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $item->nama_produk }}</h5>
                                    <p class="card-description text-mute"><em>{{ $item->deskripsi }}</em></p>
                                    <p class="card-text fw-bold">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</p>
                                    <button class="btn btn-primary add-to-cart" data-produk-id="{{ $item->id }}"
                                        data-produk-name="{{ $item->nama_produk }}"
                                        data-produk-description="{{ $item->deskripsi }}"
                                        data-produk-price="{{ $item->harga_jual }}"
                                        data-produk-stock="{{ $item->stok }}"
                                        @if ($item->stok < 1) disabled style="background-color: #d9534f; border-color: #d9534f;" @endif>
                                        @if ($item->stok < 1)
                                            Stok Habis
                                        @else
                                            Tambah ke Keranjang
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-3">
                <div class="cart border rounded shadow-sm p-4 bg-light">
                    <h4 class="mb-4 text-center text-success" >Keranjang Pembelian</h4>

                    <!-- Daftar item di keranjang -->
                    <div id="cart-items" class="mb-4">
                        <!-- Item akan ditambahkan di sini -->
                    </div>

                    <!-- Ringkasan harga -->
                    <div class="cart-summary mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total:</span>
                            <span id="total-price" class="fw-bold">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Pajak:</span>
                            <span id="tax-price" class="fw-bold">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold border-top pt-2">
                            <span>Total Akhir:</span>
                            <span id="final-price" class="text-success">Rp 0</span>
                        </div>
                    </div>

                    <!-- Tipe Pesanan -->
                    <div class="mb-4">
                        <label for="order-type" class="form-label fw-semibold">Tipe Pesanan:</label>
                        <select class="form-select" id="order-type" required>
                            <option value="" disabled selected>Pilih Tipe Pesanan</option>
                            <option value="dine-in">Dine In</option>
                            <option value="take-away">Take Away</option>
                        </select>
                    </div>

                    <!-- Pilihan Meja (tersembunyi jika tidak dibutuhkan) -->
                    <div class="mb-4" id="meja-container" style="display: none;">
                        <label for="meja-select" class="form-label fw-semibold">Pilih Meja:</label>
                        <select class="form-select" id="meja-select" required>
                            <option value="" disabled selected>Pilih Meja</option>
                            @foreach ($meja as $item)
                                @if ($item->status == 'tersedia')
                                    <option value="{{ $item->id }}">
                                        Meja {{ $item->nomor_meja }} ({{ $item->kapasitas }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Nama Pembeli -->
                    <div class="mb-4">
                        <label for="customer-name" class="form-label fw-semibold">Nama Pembeli:</label>
                        <input type="text" class="form-control" id="customer-name" placeholder="Masukkan nama pembeli"
                            required>
                    </div>

                    <!-- Tombol Checkout -->
                    <button id="checkout-btn" class="btn btn-success w-100">Checkout</button>
                </div>
            </div>

        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const orderTypeSelect = document.getElementById('order-type');
                const mejaContainer = document.getElementById('meja-container');

                orderTypeSelect.addEventListener('change', function() {
                    if (this.value === 'dine-in') {
                        mejaContainer.style.display = 'block';
                    } else {
                        mejaContainer.style.display = 'none';
                        document.getElementById('meja-select').value = '';
                    }
                });

                const cartItemsContainer = document.getElementById('cart-items');
                const totalPriceElement = document.getElementById('total-price');
                const taxPriceElement = document.getElementById('tax-price');
                const finalPriceElement = document.getElementById('final-price');

                let totalPrice = 0;
                let taxPrice = 0;
                let finalPrice = 0;
                let cartItems = [];

                function updateAddToCartButton(productId, stock) {
                    const button = document.querySelector(`.add-to-cart[data-produk-id="${productId}"]`);
                    const item = cartItems.find(item => item.id === productId);

                    if (item) {
                        button.textContent = 'Sudah ada di keranjang (' + item.quantity + ')';
                        button.disabled = false;
                    } else {
                        button.textContent = stock > 0 ? 'Tambah ke Keranjang' : 'Stok Habis';
                        button.disabled = stock <= 0;
                    }
                }

                function addToCart(productId, productName, productPrice, productDescription, stock) {
                    const existingItem = cartItems.find(item => item.id === productId);
                    if (existingItem) {
                        if (existingItem.quantity < stock) {
                            existingItem.quantity += 1;
                        } else {
                            alert('Stok tidak cukup untuk menambah produk ini.');
                        }
                    } else {
                        if (stock > 0) {
                            cartItems.push({
                                id: productId,
                                name: productName,
                                price: productPrice,
                                description: productDescription,
                                quantity: 1
                            });
                        } else {
                            alert('Stok tidak tersedia untuk produk ini.');
                        }
                    }

                    updateAddToCartButton(productId, stock);
                    updateCartDisplay();
                }

                function updateCartDisplay() {
                    cartItemsContainer.innerHTML = '';
                    totalPrice = 0;

                    cartItems.forEach((item) => {
                        const itemTotalPrice = item.price * item.quantity;
                        totalPrice += itemTotalPrice;

                        const itemElement = document.createElement('div');
                        itemElement.className = 'cart-item mb-2';

                        itemElement.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <span>${item.name}</span>
                    <span class="item-price">Rp ${numberWithCommas(itemTotalPrice)}</span>
                </div>
                <div class="quantity-controls mt-1">
                    <button class="btn btn-sm adjust-quantity" data-id="${item.id}" data-action="decrease">-</button>
                    <span class="item-quantity mx-2">${item.quantity}</span>
                    <button class="btn btn-sm adjust-quantity" data-id="${item.id}" data-action="increase">+</button>
                </div>
            `;
                        cartItemsContainer.appendChild(itemElement);
                    });

                    taxPrice = totalPrice * 0.1;
                    finalPrice = totalPrice + taxPrice;

                    totalPriceElement.innerText = `Rp ${numberWithCommas(totalPrice)}`;
                    taxPriceElement.innerText = `Rp ${numberWithCommas(taxPrice)}`;
                    finalPriceElement.innerText = `Rp ${numberWithCommas(finalPrice)}`;
                }

                function numberWithCommas(x) {
                    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }

                document.addEventListener('click', function(event) {
                    if (event.target.matches('.adjust-quantity')) {
                        const productId = event.target.getAttribute('data-id');
                        const action = event.target.getAttribute('data-action');
                        const item = cartItems.find(item => item.id === productId);

                        const productStock = parseInt(document.querySelector(
                            `.add-to-cart[data-produk-id="${productId}"]`).getAttribute(
                            'data-produk-stock'));

                        if (action === 'increase' && item.quantity < productStock) {
                            item.quantity += 1;
                        } else if (action === 'decrease') {
                            if (item.quantity > 1) {
                                item.quantity -= 1;
                            } else {
                                cartItems = cartItems.filter(item => item.id !== productId);
                            }
                        } else if (action === 'increase') {
                            alert('Stok tidak cukup untuk menambah produk ini.');
                        }

                        updateCartDisplay();
                        updateAddToCartButton(productId, productStock);
                    }
                });

                document.querySelectorAll('.add-to-cart').forEach(button => {
                    button.addEventListener('click', function() {
                        const productId = this.getAttribute('data-produk-id');
                        const productName = this.getAttribute('data-produk-name');
                        const productPrice = parseFloat(this.getAttribute('data-produk-price'));
                        const productDescription = this.getAttribute('data-produk-description');
                        const stock = parseInt(this.getAttribute('data-produk-stock'));

                        addToCart(productId, productName, productPrice, productDescription, stock);
                    });
                });

                document.getElementById('checkout-btn').addEventListener('click', function() {
    const checkoutBtn = this; 
    checkoutBtn.disabled = true; 

    const customerName = document.getElementById('customer-name').value;
    const selectedMeja = document.getElementById('meja-select').value;

    if (!customerName) {
        alert('Silakan masukkan nama pembeli.');
        checkoutBtn.disabled = false; 
        return;
    }
    if (orderTypeSelect.value === 'dine-in' && !selectedMeja) {
        alert('Silakan pilih meja.');
        checkoutBtn.disabled = false; 
        return;
    }
    if (!cartItems.length) {
        alert('Keranjang pembelian kosong.');
        checkoutBtn.disabled = false;  
        return;
    }

    const cartData = {
        customer_name: customerName,
        jenis_pesanan: orderTypeSelect.value,
        meja_id: orderTypeSelect.value === 'dine-in' ? selectedMeja : null,
        items: cartItems.map(item => ({
            id: item.id,
            quantity: item.quantity,
            price: item.price
        })),
        total_price: finalPrice,
        status: 'pending'
    };

    fetch('/pembelian/checkout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(cartData)
    })
    .then(response => {
        if (!response.ok) throw new Error('Gagal melakukan checkout');
        return response.json();
    })
    .then(data => {
        Swal.fire({
            icon: 'success',
            title: 'Checkout Berhasil',
            footer: '<a href="/pembayaran">Lanjut ke pembayaran?</a>'
        }).then(() => {
            window.location.href = '/pembelian';
        });
    })
    .catch(error => {
        Swal.fire({
            title: 'Terjadi Kesalahan!',
            text: error.message,
            icon: 'error',
            confirmButtonText: 'OK'
        }).then(() => {
            checkoutBtn.disabled = false;  
        });
    });
});

            });
            document.querySelectorAll('.nav-link[data-filter]').forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    document.querySelectorAll('.product').forEach(product => {
                        const kategoriId = product.getAttribute('data-kategori');
                        if (filter === 'all' || filter === kategoriId) {
                            product.style.display = 'block';
                        } else {
                            product.style.display = 'none';
                        }
                    });
                    document.querySelectorAll('.nav-link').forEach(btn => btn.classList.remove(
                        'active'));
                    this.classList.add('active');
                });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush



@endsection
