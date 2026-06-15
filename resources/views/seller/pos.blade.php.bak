@extends('layouts.seller')

@section('title', 'Terminal Kasir - Pondasikita')

@section('content')

{{-- HANYA MENGGUNAKAN FONT ICON --}}
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    /* =========================================================
       CSS TINGKAT DEWA (NATIVE/VANILLA) - ANTI MERUSAK SIDEBAR
       ========================================================= */
    :root {
        --pos-dark: #09090b;
        --pos-dark-panel: #18181b;
        --pos-white: #ffffff;
        --pos-bg: #f4f4f5;
        --pos-border: #e4e4e7;
        --pos-border-dark: #27272a;
        --pos-primary: #2563eb;
        --pos-primary-hover: #1d4ed8;
        --pos-primary-light: #eff6ff;
        --pos-text-main: #09090b;
        --pos-text-muted: #71717a;
        --pos-text-light: #a1a1aa;

        --pos-radius-xl: 24px;
        --pos-radius-lg: 16px;
        --pos-radius-md: 12px;

        --pos-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
        --pos-shadow-glow: 0 0 20px rgba(37,99,235,0.4);
    }

    /* MATIKAN SCROLL BODY UTAMA */
    body { overflow: hidden !important; }

    .pos-wrapper {
        font-family: 'Inter', sans-serif;
        /* Tinggi dihitung pas dengan layar dikurangi header seller */
        height: calc(100vh - 90px);
        padding: 20px;
        display: flex;
        gap: 20px;
        background-color: var(--pos-bg);
        box-sizing: border-box;
        overflow: hidden;
    }

    .font-digital {
        font-family: 'JetBrains Mono', monospace;
        letter-spacing: -1px;
    }

    /* KIRI: KATALOG */
    .pos-catalog {
        flex: 6;
        background: var(--pos-white);
        border-radius: var(--pos-radius-xl);
        box-shadow: var(--pos-shadow);
        border: 1px solid var(--pos-border);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .catalog-header {
        padding: 20px;
        border-bottom: 1px solid var(--pos-border);
        display: flex;
        gap: 15px;
        flex-shrink: 0;
    }

    .search-wrapper { position: relative; flex: 1; }
    .search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--pos-text-light); font-size: 18px; }
    .search-input {
        width: 100%; padding: 14px 16px 14px 45px;
        border: 2px solid var(--pos-border); border-radius: var(--pos-radius-lg);
        font-size: 14px; font-weight: 700; color: var(--pos-text-main); font-family: 'JetBrains Mono', monospace;
        outline: none; transition: all 0.2s; box-sizing: border-box;
    }
    .search-input:focus { border-color: var(--pos-primary); box-shadow: 0 0 0 4px rgba(37,99,235,0.1); }

    .filter-wrapper { position: relative; width: 250px; }
    .filter-select {
        width: 100%; height: 100%; padding: 0 40px 0 16px;
        border: 2px solid var(--pos-border); border-radius: var(--pos-radius-lg);
        font-size: 14px; font-weight: 700; color: var(--pos-text-main); font-family: 'Inter', sans-serif;
        outline: none; transition: all 0.2s; appearance: none; cursor: pointer; box-sizing: border-box;
    }
    .filter-select:focus { border-color: var(--pos-primary); box-shadow: 0 0 0 4px rgba(37,99,235,0.1); }
    .filter-icon { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: var(--pos-text-light); pointer-events: none; }

    .catalog-body { flex: 1; overflow-y: auto; position: relative; min-height: 0; }
    .pos-table { width: 100%; border-collapse: collapse; text-align: left; }
    .pos-table th {
        position: sticky; top: 0; background: rgba(255,255,255,0.95); backdrop-filter: blur(5px);
        padding: 16px 24px; font-size: 10px; font-weight: 900; color: var(--pos-text-muted);
        text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid var(--pos-border); z-index: 10;
    }
    .pos-table td { padding: 16px 24px; border-bottom: 1px solid #f4f4f5; vertical-align: middle; transition: background 0.2s; }
    .pos-table tr { cursor: pointer; }
    .pos-table tr:hover td { background-color: var(--pos-primary-light); }

    .sku-badge { background: #f4f4f5; color: var(--pos-text-muted); font-family: 'JetBrains Mono', monospace; font-size: 10px; font-weight: 700; padding: 6px 10px; border-radius: 6px; border: 1px solid var(--pos-border); }
    .pos-table tr:hover .sku-badge { background: #dbeafe; color: var(--pos-primary); border-color: #bfdbfe; }
    .item-name { font-size: 14px; font-weight: 700; color: var(--pos-text-main); margin: 0; }
    .pos-table tr:hover .item-name { color: var(--pos-primary); }
    .stock-badge { display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 900; border: 1px solid transparent; }
    .stock-safe { background: #ecfdf5; color: #059669; border-color: #a7f3d0; }
    .stock-low { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
    .item-price { font-size: 14px; font-weight: 900; color: var(--pos-text-main); }

    /* KANAN: KASIR */
    .pos-cart {
        flex: 4; min-width: 380px; max-width: 450px;
        background: var(--pos-dark); border-radius: var(--pos-radius-xl);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid var(--pos-border-dark);
        display: flex; flex-direction: column; overflow: hidden;
    }

    .cart-header { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; }
    .header-left { display: flex; align-items: center; gap: 12px; }
    .header-icon { width: 40px; height: 40px; border-radius: 12px; background: var(--pos-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; box-shadow: var(--pos-shadow-glow); }
    .header-title { color: white; font-size: 16px; font-weight: 900; margin: 0 0 2px 0; }
    .header-subtitle { color: #60a5fa; font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; margin: 0; }
    .btn-reset { background: var(--pos-dark-panel); color: var(--pos-text-light); border: 1px solid var(--pos-border-dark); padding: 8px 12px; border-radius: var(--pos-radius-md); font-size: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
    .btn-reset:hover { background: #27272a; color: white; }
    .btn-reset:active { transform: scale(0.95); }

    .customer-box { padding: 16px 20px; background: var(--pos-dark); border-bottom: 1px solid rgba(255,255,255,0.05); flex-shrink: 0; }
    .customer-input-wrapper { position: relative; }
    .customer-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--pos-text-muted); }
    .customer-input { width: 100%; padding: 12px 16px 12px 40px; background: var(--pos-dark-panel); border: 1px solid var(--pos-border-dark); border-radius: var(--pos-radius-md); color: white; font-size: 14px; font-weight: 700; font-family: 'Inter', sans-serif; outline: none; transition: 0.2s; box-sizing: border-box; }
    .customer-input:focus { border-color: var(--pos-primary); box-shadow: 0 0 0 2px rgba(37,99,235,0.3); }

    .cart-body { flex: 1; overflow-y: auto; padding: 16px 20px; background: var(--pos-dark); min-height: 0; }
    .empty-state { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--pos-text-muted); }
    .empty-icon { font-size: 60px; margin-bottom: 16px; opacity: 0.5; }
    .empty-text { font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; margin: 0; }

    .cart-item { background: var(--pos-dark-panel); border: 1px solid var(--pos-border-dark); border-radius: var(--pos-radius-lg); padding: 16px; margin-bottom: 12px; }
    .cart-item-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; padding-right: 20px; position: relative; }
    .cart-item-name { color: white; font-size: 13px; font-weight: 700; margin: 0; line-height: 1.4; }
    .btn-remove { position: absolute; right: 0; top: 0; background: transparent; border: none; color: var(--pos-text-muted); cursor: pointer; transition: 0.2s; font-size: 12px; padding: 4px; }
    .btn-remove:hover { color: #ef4444; }
    .cart-item-footer { display: flex; justify-content: space-between; align-items: flex-end; }
    .qty-control { display: flex; align-items: center; background: black; border-radius: var(--pos-radius-md); padding: 4px; border: 1px solid var(--pos-border-dark); }
    .qty-btn { width: 28px; height: 28px; background: transparent; border: none; color: var(--pos-text-muted); font-weight: 900; font-size: 14px; border-radius: 8px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; }
    .qty-btn:hover { color: white; background: rgba(255,255,255,0.1); }
    .qty-btn.plus { color: var(--pos-primary); }
    .qty-btn.plus:hover { background: rgba(37,99,235,0.2); }
    .qty-input { width: 36px; background: transparent; border: none; color: white; font-weight: 900; font-size: 12px; text-align: center; font-family: 'JetBrains Mono', monospace; outline: none; }
    .item-subtotal-box { text-align: right; }
    .item-price-unit { display: block; color: var(--pos-text-muted); font-size: 9px; font-weight: 900; letter-spacing: 1px; margin-bottom: 2px; }
    .item-subtotal { color: #60a5fa; font-size: 14px; font-weight: 900; }

    .cart-footer { background: var(--pos-white); padding: 24px; border-radius: 32px 32px 0 0; flex-shrink: 0; box-shadow: 0 -10px 30px rgba(0,0,0,0.15); z-index: 20; }
    .total-screen { background: var(--pos-bg); border-radius: var(--pos-radius-lg); padding: 16px; margin-bottom: 20px; border: 1px solid var(--pos-border); box-shadow: inset 0 2px 10px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: flex-end; transition: transform 0.15s; }
    .total-label { color: var(--pos-text-light); font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; padding-bottom: 4px; }
    .total-value-wrapper { display: flex; align-items: flex-start; gap: 4px; }
    .total-currency { font-size: 14px; font-weight: 700; color: var(--pos-text-main); margin-top: 4px; }
    .total-value { font-size: 36px; font-weight: 900; color: var(--pos-text-main); line-height: 1; letter-spacing: -2px; }

    .payment-input-wrapper { position: relative; margin-bottom: 16px; }
    .payment-currency { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); font-weight: 900; color: var(--pos-text-light); }
    .payment-input { width: 100%; padding: 14px 16px 14px 48px; border: 2px solid var(--pos-border); border-radius: var(--pos-radius-lg); font-size: 24px; font-weight: 900; color: var(--pos-text-main); text-align: right; outline: none; transition: 0.2s; box-sizing: border-box; }
    .payment-input:focus { border-color: var(--pos-primary); box-shadow: 0 0 0 4px rgba(37,99,235,0.1); }

    .keypad-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 24px; }
    .keypad-btn { background: #f4f4f5; border: 1px solid var(--pos-border); border-bottom: 4px solid #d4d4d8; color: var(--pos-text-main); font-weight: 900; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; padding: 12px 0; border-radius: var(--pos-radius-md); cursor: pointer; outline: none; }
    .keypad-btn:hover { background: #e4e4e7; }

    .change-row { display: flex; justify-content: space-between; align-items: center; padding: 0 8px; margin-bottom: 24px; }
    .change-label { font-size: 10px; font-weight: 900; color: var(--pos-text-light); text-transform: uppercase; letter-spacing: 1px; }
    .change-value { font-size: 20px; font-weight: 900; color: var(--pos-text-light); }
    .change-value.success { color: var(--pos-primary); }
    .change-value.error { color: #ef4444; }

    .btn-checkout { width: 100%; background: var(--pos-primary); color: white; font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; padding: 18px; border-radius: var(--pos-radius-lg); border: none; cursor: pointer; transition: 0.2s; box-shadow: var(--pos-shadow-glow); display: flex; align-items: center; justify-content: center; gap: 8px; }
    .btn-checkout:hover { background: var(--pos-primary-hover); transform: translateY(-2px); }
    .btn-checkout:active { transform: translateY(0); box-shadow: none; }
    .btn-checkout:disabled { background: var(--pos-border); color: var(--pos-text-light); box-shadow: none; cursor: not-allowed; transform: none; }

    /* Custom Scrollbar Styles */
    .catalog-body::-webkit-scrollbar, .cart-body::-webkit-scrollbar { width: 6px; }
    .catalog-body::-webkit-scrollbar-track, .cart-body::-webkit-scrollbar-track { background: transparent; }
    .catalog-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .cart-body::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 10px; }
</style>

<div class="pos-wrapper">

    {{-- KIRI: KATALOG --}}
    <div class="pos-catalog">
        <div class="catalog-header">
            <div class="search-wrapper">
                <i class="fas fa-barcode search-icon"></i>
                <input type="text" id="search-input" class="search-input" placeholder="Scan Barcode (F2)..." autocomplete="off" autofocus>
            </div>
            <div class="filter-wrapper">
                <select id="category-filter" class="filter-select">
                    <option value="all">Semua Kategori</option>
                </select>
                <i class="fas fa-chevron-down filter-icon"></i>
            </div>
        </div>

        <div class="catalog-body">
            <table class="pos-table">
                <thead>
                    <tr>
                        <th width="20%">SKU Item</th>
                        <th width="45%">Material</th>
                        <th width="15%" style="text-align: center;">Stok</th>
                        <th width="20%" style="text-align: right;">Harga</th>
                    </tr>
                </thead>
                <tbody id="product-table-body">
                    <tr><td colspan="4" style="text-align: center; padding: 40px; color: var(--pos-text-light); font-weight: 700; font-size: 14px;"><i class="fas fa-circle-notch fa-spin" style="margin-right: 8px;"></i> Sinkronisasi Gudang...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- KANAN: KASIR --}}
    <div class="pos-cart">
        <div class="cart-header">
            <div class="header-left">
                <div class="header-icon"><i class="fas fa-cash-register"></i></div>
                <div>
                    <h2 class="header-title">Terminal Kasir</h2>
                    <p class="header-subtitle"><i class="fas fa-circle" style="font-size: 5px; animation: pulse 2s infinite; margin-right: 4px;"></i> Online</p>
                </div>
            </div>
            <button id="clear-cart-btn" class="btn-reset"><i class="fas fa-rotate-right"></i> Reset</button>
        </div>

        <div class="customer-box">
            {{-- 1. Menampilkan Nama Toko (Readonly / Tidak bisa diedit) --}}
            <div class="customer-input-wrapper" style="margin-bottom: 12px; opacity: 0.7;" title="Toko Anda">
                <i class="fas fa-store customer-icon"></i>
                <input type="text" class="customer-input" value="{{ $tokoPos->nama_toko ?? 'Pondasikita Enterprise' }}" readonly>
            </div>
            
            {{-- 2. Menampilkan Nama Kasir (Bisa diedit oleh kasir shift lain) --}}
            <div class="customer-input-wrapper" title="Ketik nama kasir yang bertugas">
                <i class="fas fa-user-tie customer-icon"></i>
                <input type="text" id="kasir-name" class="customer-input" value="{{ auth()->user()->nama ?? auth()->user()->username }}" placeholder="Nama Kasir">
            </div>

            {{-- 3. Input Tersembunyi: KUNCI UTAMA VALIDASI DATABASE --}}
            <input type="hidden" id="pos-user-id" value="{{ auth()->id() }}">
        </div>

        <div class="cart-body" id="cart-items">
            <div id="empty-cart-message" class="empty-state">
                <i class="fas fa-barcode empty-icon"></i>
                <p class="empty-text">Menunggu Scan Barcode</p>
            </div>
        </div>

        <div class="cart-footer">
            <div class="total-screen" id="total-screen">
                <span class="total-label">Total Tagihan</span>
                <div class="total-value-wrapper">
                    <span class="total-currency">Rp</span>
                    <span id="total-price" class="total-value font-digital">0</span>
                </div>
            </div>

            <div class="payment-input-wrapper">
                <span class="payment-currency">Rp</span>
                <input type="number" id="amount-paid" class="payment-input font-digital" placeholder="0">
            </div>

            <div class="keypad-grid">
                <button type="button" class="keypad-btn" data-amount="exact">Pas</button>
                <button type="button" class="keypad-btn" data-amount="50000">50K</button>
                <button type="button" class="keypad-btn" data-amount="100000">100K</button>
            </div>

            <div class="change-row">
                <span class="change-label">Kembalian</span>
                <span id="change-due" class="change-value font-digital">Rp 0</span>
            </div>

            <button id="process-payment-btn" class="btn-checkout" disabled>
                <i class="fas fa-print"></i> Bayar & Cetak (F9)
            </button>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let allProducts = [];
    let cart = [];
    let currentTotal = 0;

    const formatRp = (num) => new Intl.NumberFormat('id-ID').format(num);

    function loadProducts() {
        fetch("{{ route('seller.pos.api.products') }}")
            .then(res => res.json())
            .then(data => { allProducts = data; renderProducts(allProducts); });
    }

    function loadCategories() {
        fetch("{{ route('seller.pos.api.categories') }}")
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('category-filter');
                data.forEach(cat => select.insertAdjacentHTML('beforeend', `<option value="${cat.id}">${cat.nama_kategori}</option>`));
            });
    }

    function renderProducts(products) {
        const tbody = document.getElementById('product-table-body');
        tbody.innerHTML = '';

        if(products.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; padding:40px; color:#ef4444; font-weight:700;"><i class="fas fa-exclamation-triangle" style="margin-right:8px;"></i>Data tidak ditemukan</td></tr>`;
            return;
        }

        products.forEach(p => {
            let sku = p.kode_barang ? p.kode_barang : 'SKU-'+String(p.id).padStart(4, '0');
            let stockClass = p.stok <= 5 ? 'stock-low' : 'stock-safe';

            let html = `
                <tr onclick="addToCart(${p.id})">
                    <td><span class="sku-badge font-digital">${sku}</span></td>
                    <td><p class="item-name">${p.nama_barang}</p></td>
                    <td style="text-align: center;"><span class="stock-badge ${stockClass}">${p.stok}</span></td>
                    <td style="text-align: right;"><span class="item-price font-digital">Rp ${formatRp(p.harga)}</span></td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', html);
        });
    }

    const searchInput = document.getElementById('search-input');
    searchInput.addEventListener('input', filterProducts);

    searchInput.addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
            e.preventDefault();
            let keyword = this.value.toLowerCase().trim();

            let matchedProduct = allProducts.find(p => (p.kode_barang && p.kode_barang.toLowerCase() === keyword));
            if(!matchedProduct) matchedProduct = allProducts.find(p => p.nama_barang.toLowerCase() === keyword);

            if(matchedProduct) {
                addToCart(matchedProduct.id);
                this.value = ''; filterProducts();
            } else {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Tidak Ditemukan!', showConfirmButton: false, timer: 1500 });
                this.value = '';
            }
        }
    });

    document.getElementById('category-filter').addEventListener('change', filterProducts);

    function filterProducts() {
        let keyword = searchInput.value.toLowerCase();
        let categoryId = document.getElementById('category-filter').value;

        let filtered = allProducts.filter(p => {
            let matchCat = categoryId === 'all' || p.kategori_id == categoryId;
            let kode = p.kode_barang ? p.kode_barang.toLowerCase() : '';
            let matchKey = p.nama_barang.toLowerCase().includes(keyword) || kode.includes(keyword);
            return matchCat && matchKey;
        });
        renderProducts(filtered);
    }

    document.addEventListener('keydown', function(e) {
        if(e.key === 'F2') { e.preventDefault(); searchInput.focus(); }
        if(e.key === 'F9') { e.preventDefault(); document.getElementById('process-payment-btn').click(); }
    });

    window.addToCart = function(productId) {
        let product = allProducts.find(p => p.id === productId);
        if(!product) return;

        let existing = cart.find(item => item.id === productId);
        if(existing) {
            if(existing.qty < product.stok) existing.qty++;
            else Swal.fire({toast: true, position: 'top-end', icon: 'warning', title: 'Stok Fisik Habis!', showConfirmButton: false, timer: 1500});
        } else {
            cart.push({ id: product.id, nama_barang: product.nama_barang, harga: product.harga, qty: 1, stok: product.stok });
        }
        updateCartDisplay();
    };

    window.updateQty = function(productId, change) {
        let item = cart.find(i => i.id === productId);
        if(!item) return;
        let newQty = item.qty + change;
        if(newQty > 0 && newQty <= item.stok) item.qty = newQty;
        else if (newQty === 0) cart = cart.filter(i => i.id !== productId);
        updateCartDisplay();
    };

    document.getElementById('clear-cart-btn').addEventListener('click', () => { cart = []; updateCartDisplay(); amountInput.value = ''; calculateChange(); });

    function updateCartDisplay() {
        const container = document.getElementById('cart-items');
        const emptyMsg = document.getElementById('empty-cart-message');
        document.querySelectorAll('.cart-item').forEach(e => e.remove());
        currentTotal = 0;

        if(cart.length === 0) {
            emptyMsg.style.display = 'flex';
            document.getElementById('process-payment-btn').disabled = true;
        } else {
            emptyMsg.style.display = 'none';
            document.getElementById('process-payment-btn').disabled = false;

            cart.forEach(item => {
                currentTotal += (item.harga * item.qty);
                let html = `
                    <div class="cart-item">
                        <div class="cart-item-header">
                            <h6 class="cart-item-name">${item.nama_barang}</h6>
                            <button onclick="updateQty(${item.id}, -${item.qty})" class="btn-remove"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="cart-item-footer">
                            <div class="qty-control">
                                <button class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                                <input type="text" class="qty-input" value="${item.qty}" readonly>
                                <button class="qty-btn plus" onclick="updateQty(${item.id}, 1)">+</button>
                            </div>
                            <div class="item-subtotal-box">
                                <span class="item-price-unit font-digital">Rp${formatRp(item.harga)}</span>
                                <span class="item-subtotal font-digital">Rp${formatRp(item.harga * item.qty)}</span>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
            });
        }

        const screenEl = document.getElementById('total-screen');
        screenEl.style.transform = 'scale(1.02)';
        document.getElementById('total-price').innerText = formatRp(currentTotal);
        setTimeout(() => screenEl.style.transform = 'scale(1)', 150);

        calculateChange();
    }

    const amountInput = document.getElementById('amount-paid');
    const changeDisplay = document.getElementById('change-due');

    amountInput.addEventListener('input', calculateChange);
    document.querySelectorAll('.keypad-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            let val = this.getAttribute('data-amount');
            amountInput.value = val === 'exact' ? currentTotal : val;
            calculateChange();
        });
    });

    function calculateChange() {
        let paid = parseInt(amountInput.value) || 0;
        let change = paid - currentTotal;
        if(currentTotal === 0) {
            changeDisplay.innerText = "Rp 0";
            changeDisplay.className = "change-value font-digital";
            return;
        }

        if(change < 0) {
            changeDisplay.innerText = "UANG KURANG!!!!";
            changeDisplay.className = "change-value font-digital error";
        } else {
            changeDisplay.innerText = "Rp " + formatRp(change);
            changeDisplay.className = "change-value font-digital success";
        }
    }

    document.getElementById('process-payment-btn').addEventListener('click', function() {
        let paid = parseInt(amountInput.value) || 0;
        if(paid < currentTotal) { 
            Swal.fire({icon: 'error', title: 'Uang Tidak Cukup', text: 'Jumlah uang tunai kurang.'}); 
            return; 
        }

        this.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> MEMPROSES...';
        this.disabled = true;

        fetch("{{ route('seller.pos.api.checkout') }}", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json' // Tambahkan ini agar Laravel mengembalikan JSON jika error
            },
            body: JSON.stringify({
                user_id: document.getElementById('pos-user-id').value, // Kirim ID ke backend
                kasir_name: document.getElementById('kasir-name').value || 'Kasir', // Kirim Nama Kasir
                payment_method: 'Tunai Kasir',
                amount_paid: paid,
                total: currentTotal,
                cart: cart
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                Swal.fire({
                    title: 'TRANSAKSI BERHASIL!',
                    html: `
                        <div style="background:#f4f4f5; padding:15px; border-radius:12px; margin:15px 0;">
                            <span style="font-size:10px; font-weight:900; color:#71717a;">KEMBALIAN</span>
                            <b style="font-size:32px; color:#2563eb; display:block; font-family:monospace;">Rp ${formatRp(paid - currentTotal)}</b>
                        </div>
                        <span style="font-size:10px; color:#a1a1aa; font-weight:bold;">No. Struk: ${data.invoice}</span>
                    `,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-print"></i> Cetak Struk',
                    cancelButtonText: 'Selesai',
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#09090b',
                    allowOutsideClick: false
                }).then((result) => {
                    // JIKA KLIK CETAK STRUK
                    if (result.isConfirmed) {
                        // Ganti URL ini sesuai dengan route print struk Anda di backend
                        window.open("{{ url('seller/pos/print') }}/" + data.invoice, '_blank');
                    }
                    
                    // Reset POS
                    cart = []; 
                    amountInput.value = ''; 
                    document.getElementById('customer-name').value = '';
                    updateCartDisplay(); 
                    loadProducts(); 
                    searchInput.focus(); 
                    resetBtn();
                });
            } else { 
                // Menangani pesan error spesifik dari database yang dikirim Controller
                Swal.fire('Gagal Simpan', data.message, 'error'); 
                resetBtn(); 
            }
        })
        .catch(err => { 
            console.error(err);
            Swal.fire('System Error', 'Pastikan Anda masih dalam keadaan Login.', 'error'); 
            resetBtn(); 
        });

        function resetBtn() {
            const btn = document.getElementById('process-payment-btn');
            btn.innerHTML = '<i class="fas fa-print"></i> Bayar & Cetak (F9)';
            btn.disabled = false;
        }
    });

    loadProducts(); loadCategories();
});
</script>
@endpush
