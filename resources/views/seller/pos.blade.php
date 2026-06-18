@extends('layouts.seller')

@section('title', 'Pondasikita POS - Terminal Kasir Cerdas')

@section('content')

{{-- IMPORT GOOGLE FONTS & ICONS --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;500;600;700;800&family=Rubik:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    /* =========================================================
       POS UI PRO MAX - OLED EDITION (FULL & STABLE)
       ========================================================= */
    :root {
        --pos-bg: #020617; 
        --pos-panel: #0f172a; 
        --pos-panel-light: #1e293b; 
        --pos-primary: #3b82f6; 
        --pos-primary-glow: rgba(59, 130, 246, 0.5);
        --pos-success: #22c55e; 
        --pos-danger: #ef4444; 
        --pos-text: #f8fafc;
        --pos-text-muted: #94a3b8;
        --pos-border: rgba(255, 255, 255, 0.1);
        --pos-radius: 20px;
        --pos-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }

    body { 
        background-color: var(--pos-bg) !important;
        overflow: hidden;
    }

    .pos-container {
        font-family: 'Nunito Sans', sans-serif;
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 20px;
        padding: 20px;
        height: calc(100vh - 65px); /* Maximized height */
        max-width: 100%;
        box-sizing: border-box;
    }

    .font-digital { font-family: 'JetBrains Mono', monospace; }

    /* LEFT SIDE: CATALOG */
    .pos-catalog {
        display: flex;
        flex-direction: column;
        gap: 15px;
        min-height: 0;
    }

    .catalog-toolbar {
        background: var(--pos-panel);
        padding: 12px 18px;
        border-radius: var(--pos-radius);
        border: 1px solid var(--pos-border);
        display: flex;
        gap: 12px;
        align-items: center;
        box-shadow: var(--pos-shadow);
    }

    .search-group { position: relative; flex: 1; }
    .search-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--pos-text-muted); }
    .search-group input {
        width: 100%; background: var(--pos-panel-light); border: 1px solid var(--pos-border);
        border-radius: 12px; padding: 10px 15px 10px 45px; color: white; outline: none; transition: 0.3s;
        font-size: 14px;
    }
    .search-group input:focus { border-color: var(--pos-primary); box-shadow: 0 0 15px var(--pos-primary-glow); }

    .filter-group select {
        background: var(--pos-panel-light); border: 1px solid var(--pos-border);
        border-radius: 12px; padding: 10px 15px; color: white; outline: none; cursor: pointer;
        font-size: 13px;
    }

    .catalog-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
        gap: 12px;
        overflow-y: auto;
        padding-bottom: 20px;
        flex: 1;
        align-content: start;
    }

    .product-card {
        background: var(--pos-panel); border: 1px solid var(--pos-border);
        border-radius: 15px; padding: 10px; cursor: pointer; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex; flex-direction: column; gap: 6px; position: relative;
        min-height: 220px;
    }

    .product-image-container {
        width: 100%;
        height: 120px;
        background: var(--pos-panel-light);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-image-container i {
        font-size: 30px;
        color: var(--pos-text-muted);
        opacity: 0.3;
    }

    .product-card:hover { transform: translateY(-3px); border-color: var(--pos-primary); box-shadow: 0 8px 25px rgba(0,0,0,0.4); }

    .card-sku { font-size: 9px; color: var(--pos-text-muted); background: var(--pos-panel-light); padding: 1px 6px; border-radius: 4px; width: fit-content; }
    .card-name { font-weight: 700; font-size: 13px; color: var(--pos-text); min-height: 34px; line-height: 1.2; margin: 2px 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .card-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 5px; }
    .card-price { font-weight: 800; color: var(--pos-primary); font-size: 13px; }
    .card-stock { font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 20px; }
    .stock-ok { background: rgba(34, 197, 94, 0.15); color: #4ade80; }
    .stock-warning { background: rgba(239, 68, 68, 0.15); color: #f87171; }

    /* RIGHT SIDE: CART */
    .pos-sidebar {
        background: var(--pos-panel); border-radius: var(--pos-radius);
        border: 1px solid var(--pos-border); display: flex; flex-direction: column;
        box-shadow: var(--pos-shadow); overflow: hidden; height: 100%;
    }

    .sidebar-header { padding: 15px 20px; border-bottom: 1px solid var(--pos-border); display: flex; justify-content: space-between; align-items: center; }
    .status-badge { font-size: 10px; text-transform: uppercase; font-weight: 800; display: flex; align-items: center; gap: 5px; color: white; }
    .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--pos-success); box-shadow: 0 0 10px var(--pos-success); }

    .cart-items { flex: 1; overflow-y: auto; padding: 15px; display: flex; flex-direction: column; gap: 10px; }
    .cart-item { background: var(--pos-panel-light); border-radius: 12px; padding: 12px; display: flex; flex-direction: column; gap: 8px; border: 1px solid rgba(255,255,255,0.03); }
    .item-top { display: flex; justify-content: space-between; gap: 10px; }
    .item-title { font-weight: 700; font-size: 13px; color: white; flex: 1; line-height: 1.3; }
    .btn-del { color: var(--pos-text-muted); cursor: pointer; font-size: 14px; padding: 2px; }
    .btn-del:hover { color: var(--pos-danger); }

    .item-bottom { display: flex; justify-content: space-between; align-items: center; }
    .qty-box { display: flex; align-items: center; background: #000; border-radius: 8px; padding: 2px; border: 1px solid var(--pos-border); }
    .qty-btn { width: 26px; height: 26px; background: transparent; border: none; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
    .qty-btn:hover { background: rgba(255,255,255,0.1); border-radius: 6px; }
    .qty-input { width: 34px; background: transparent; border: none; color: white; text-align: center; font-size: 13px; font-weight: 800; outline: none; }
    .item-subtotal { font-weight: 800; color: white; font-size: 14px; }

    .sidebar-footer { padding: 15px 20px; background: var(--pos-panel-light); border-top: 1px solid var(--pos-border); }
    .total-display { background: rgba(0,0,0,0.3); padding: 12px; border-radius: 12px; margin-bottom: 12px; text-align: center; border: 1px solid rgba(255,255,255,0.05); }
    .total-label { font-size: 10px; font-weight: 700; color: var(--pos-text-muted); text-transform: uppercase; letter-spacing: 1px; }
    .total-amount { font-size: 30px; font-weight: 900; color: var(--pos-primary); margin: 2px 0; }

    .input-cash { width: 100%; background: #000; border: 1px solid var(--pos-border); border-radius: 12px; padding: 10px 15px; color: white; font-size: 22px; font-weight: 800; text-align: right; outline: none; transition: 0.2s; }
    .input-cash:focus { border-color: var(--pos-primary); box-shadow: 0 0 10px var(--pos-primary-glow); }

    .cash-shortcuts { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; margin: 10px 0; }
    .btn-cash { background: var(--pos-panel); border: 1px solid var(--pos-border); padding: 8px 4px; border-radius: 8px; color: white; font-size: 10px; font-weight: 800; cursor: pointer; transition: 0.2s; }
    .btn-cash:hover { background: #000; border-color: var(--pos-primary); }

    .btn-pay {
        width: 100%; background: var(--pos-primary); color: white; padding: 16px; border-radius: 12px; border: none;
        font-weight: 800; text-transform: uppercase; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 15px var(--pos-primary-glow);
    }
    .btn-pay:hover:not(:disabled) { transform: translateY(-2px); filter: brightness(1.1); }
    .btn-pay:disabled { opacity: 0.5; cursor: not-allowed; }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .pos-container { grid-template-columns: 1fr; height: auto; overflow: visible; }
        body { overflow-y: auto; }
        .pos-sidebar { height: 600px; margin-top: 20px; }
    }

    @media (max-width: 640px) {
        .catalog-grid { grid-template-columns: 1fr 1fr; }
        .catalog-toolbar { flex-direction: column; align-items: stretch; }
    }
</style>

<div class="pos-container">
    {{-- CATALOG --}}
    <div class="pos-catalog">
        <div class="catalog-toolbar">
            <div class="search-group">
                <i class="fas fa-barcode"></i>
                <input type="text" id="search-input" placeholder="Cari Nama / Scan Barcode (F2)..." autocomplete="off" autofocus>
            </div>
            <div class="filter-group">
                <select id="category-filter">
                    <option value="all">Semua Kategori</option>
                </select>
            </div>
        </div>
        <div class="catalog-grid" id="product-grid">
            <div style="grid-column: 1/-1; text-align: center; padding: 100px; color: var(--pos-text-muted);">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p style="margin-top: 15px; font-weight: 600;">Menghubungkan ke Gudang...</p>
            </div>
        </div>
    </div>

    {{-- SIDEBAR KASIR --}}
    <div class="pos-sidebar">
        <div class="sidebar-header">
            <div>
                <h2 style="font-size: 18px; font-weight: 900; color: white; margin: 0;">TERMINAL POS</h2>
                <div class="status-badge" style="margin-top: 4px;">
                    <div class="dot"></div> Online • {{ $tokoPos->nama_toko ?? 'Kasir Pondasikita' }}
                </div>
            </div>
            <button id="clear-cart-btn" title="Reset Keranjang" style="background: transparent; border: 1px solid var(--pos-border); color: var(--pos-text-muted); padding: 5px 10px; border-radius: 8px; cursor: pointer;">
                <i class="fas fa-rotate"></i>
            </button>
        </div>

        <div class="cart-items" id="cart-items">
            <div id="empty-cart-message" style="margin: auto; text-align: center; color: var(--pos-text-muted);">
                <i class="fas fa-shopping-basket fa-3x" style="margin-bottom: 15px; opacity: 0.2;"></i>
                <p style="font-weight: 700; font-size: 13px;">Belum ada material terpilih</p>
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="total-display">
                <span class="total-label">Total Tagihan</span>
                <div class="total-amount font-digital">
                    <span style="font-size: 16px;">Rp</span> <span id="total-price">0</span>
                </div>
            </div>

            <div class="payment-area">
                <div class="payment-method-area" style="margin-bottom: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <button class="btn-method active" data-method="Tunai Kasir" onclick="setPaymentMethod('Tunai Kasir')">Tunai / QRIS</button>
                    @if(isset($dpSettings) && $dpSettings['enable_dp_system'] == '1')
                    <button class="btn-method dp-btn" data-method="DP B2B" onclick="setPaymentMethod('DP B2B')" style="background: var(--pos-card); color: var(--pos-warning); border-color: var(--pos-warning);">
                        <i class="fas fa-handshake"></i> Sistem DP
                    </button>
                    @endif
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                    <span class="total-label" id="label-amount-paid">Tunai Pembeli</span>
                    <span id="change-due" class="font-digital" style="font-size: 14px; font-weight: 800; color: var(--pos-text-muted);">Kembali: Rp 0</span>
                </div>
                <input type="number" id="amount-paid" class="input-cash font-digital" placeholder="0">

                <div class="cash-shortcuts" id="cash-shortcuts-area">
                    <button class="btn-cash" data-amount="exact">PAS</button>
                    <button class="btn-cash" data-amount="50000">50K</button>
                    <button class="btn-cash" data-amount="100000">100K</button>
                </div>

                <div id="dp-info-area" style="display: none; padding: 10px; border-radius: 8px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); margin-bottom: 10px;">
                    <div style="font-size: 11px; font-weight: bold; color: var(--pos-warning); margin-bottom: 5px;">
                        <i class="fas fa-info-circle"></i> B2B Uang Muka (DP)
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: white;">
                        <span>Min. Belanja:</span> <span class="font-digital">Rp {{ number_format(isset($dpSettings) ? $dpSettings['min_nominal_dp'] : 10000000, 0, ',', '.') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: white; margin-top: 2px;">
                        <span>Min. Bayar DP:</span> <span class="font-digital">{{ isset($dpSettings) ? $dpSettings['dp_percent'] : 50 }}%</span>
                    </div>
                </div>

                <input type="hidden" id="selected-payment-method" value="Tunai Kasir">

                <input type="hidden" id="pos-user-id" value="{{ auth()->id() }}">
                <input type="hidden" id="kasir-name" value="{{ auth()->user()->nama ?? auth()->user()->username }}">

                <button id="process-payment-btn" class="btn-pay" disabled>
                    <i class="fas fa-bolt"></i> Bayar & Cetak (F9)
                </button>
            </div>
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

    function getEffectivePrice(p) {
        if (!p.nilai_diskon || p.nilai_diskon <= 0) return p.harga;
        
        const now = new Date();
        if (p.diskon_mulai && p.diskon_berakhir) {
            const start = new Date(p.diskon_mulai.replace(' ', 'T'));
            const end = new Date(p.diskon_berakhir.replace(' ', 'T'));
            if (now < start || now > end) return p.harga;
        }

        if (p.tipe_diskon === 'PERSEN') {
            return p.harga - (p.harga * (p.nilai_diskon / 100));
        } else {
            return p.harga - p.nilai_diskon;
        }
    }

    function loadProducts() {
        fetch("{{ route('seller.pos.api.products') }}")
            .then(res => res.json())
            .then(data => { 
                allProducts = data; 
                renderProducts(allProducts); 
            });
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
        const grid = document.getElementById('product-grid');
        grid.innerHTML = '';
        if(products.length === 0) {
            grid.innerHTML = `<div style="grid-column: 1/-1; text-align: center; padding: 100px; color: var(--pos-text-muted);"><i class="fas fa-search fa-2x" style="opacity: 0.2;"></i><p style="margin-top: 15px; font-weight: 600;">Material tidak ditemukan</p></div>`;
            return;
        }
        products.forEach(p => {
            let sku = p.kode_barang ? p.kode_barang : 'SKU-'+String(p.id).padStart(4, '0');
            let stockClass = p.stok <= 5 ? 'stock-warning' : 'stock-ok';
            let imgHtml = (p.gambar_utama && p.gambar_utama !== 'default.jpg') 
                ? `<img src="/assets/uploads/products/${p.gambar_utama}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                   <div class="fallback-icon" style="display:none; width:100%; height:100%; align-items:center; justify-content:center;"><i class="fas fa-box"></i></div>`
                : `<i class="fas fa-box"></i>`;

            const effectivePrice = getEffectivePrice(p);
            const hasPromo = effectivePrice < p.harga;

            let priceHtml = hasPromo 
                ? `<div style="display: flex; flex-direction: column;">
                    <span style="font-size: 10px; text-decoration: line-through; color: var(--pos-text-muted);">Rp ${formatRp(p.harga)}</span>
                    <span class="card-price font-digital">Rp ${formatRp(effectivePrice)}</span>
                   </div>`
                : `<span class="card-price font-digital">Rp ${formatRp(p.harga)}</span>`;

            let html = `
                <div class="product-card" onclick="addToCart(${p.id})">
                    <div class="product-image-container">
                        ${imgHtml}
                        ${hasPromo ? `<div style="position: absolute; top: 5px; right: 5px; bg: var(--pos-primary); color: white; font-size: 8px; font-weight: 900; padding: 2px 5px; border-radius: 5px; background: #ef4444;">PROMO</div>` : ''}
                    </div>
                    <span class="card-sku font-digital">${sku}</span>
                    <h3 class="card-name">${p.nama_barang}</h3>
                    <div class="card-footer">
                        ${priceHtml}
                        <span class="card-stock ${stockClass}">Stok: ${p.stok}</span>
                    </div>
                </div>`;
            grid.insertAdjacentHTML('beforeend', html);
        });
    }

    const searchInput = document.getElementById('search-input');
    searchInput.addEventListener('input', filterProducts);
    document.getElementById('category-filter').addEventListener('change', filterProducts);

    function filterProducts() {
        let keyword = searchInput.value.toLowerCase();
        let categoryId = document.getElementById('category-filter').value;
        let filtered = allProducts.filter(p => {
            let matchCat = categoryId === 'all' || p.kategori_id == categoryId;
            let kode = p.kode_barang ? p.kode_barang.toLowerCase() : '';
            return matchCat && (p.nama_barang.toLowerCase().includes(keyword) || kode.includes(keyword));
        });
        renderProducts(filtered);
    }

    window.addToCart = function(productId) {
        let product = allProducts.find(p => p.id === productId);
        if(!product) return;
        
        const effectivePrice = getEffectivePrice(product);
        
        let existing = cart.find(item => item.id === productId);
        if(existing) {
            if(existing.qty < product.stok) existing.qty++;
            else Swal.fire({toast: true, position: 'top-end', icon: 'warning', title: 'Stok Terbatas!', showConfirmButton: false, timer: 1500});
        } else {
            cart.push({ id: product.id, nama_barang: product.nama_barang, harga: effectivePrice, qty: 1, stok: product.stok });
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

    function updateCartDisplay() {
        const container = document.getElementById('cart-items');
        const emptyMsg = document.getElementById('empty-cart-message');
        document.querySelectorAll('.cart-item').forEach(e => e.remove());
        currentTotal = 0;
        if(cart.length === 0) {
            emptyMsg.style.display = 'block';
            document.getElementById('process-payment-btn').disabled = true;
        } else {
            emptyMsg.style.display = 'none';
            document.getElementById('process-payment-btn').disabled = false;
            cart.forEach(item => {
                currentTotal += (item.harga * item.qty);
                let html = `
                    <div class="cart-item">
                        <div class="item-top">
                            <span class="item-title">${item.nama_barang}</span>
                            <i class="fas fa-times btn-del" onclick="updateQty(${item.id}, -${item.qty})"></i>
                        </div>
                        <div class="item-bottom">
                            <div class="qty-box">
                                <button class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                                <input type="text" class="qty-input font-digital" value="${item.qty}" readonly>
                                <button class="qty-btn" onclick="updateQty(${item.id}, 1)" style="color: var(--pos-primary)">+</button>
                            </div>
                            <span class="item-subtotal font-digital">Rp ${formatRp(item.harga * item.qty)}</span>
                        </div>
                    </div>`;
                container.insertAdjacentHTML('beforeend', html);
            });
        }
        document.getElementById('total-price').innerText = formatRp(currentTotal);
        calculateChange();
    }

    const amountInput = document.getElementById('amount-paid');
    const changeDisplay = document.getElementById('change-due');
    amountInput.addEventListener('input', calculateChange);
    document.querySelectorAll('.btn-cash').forEach(btn => {
        btn.addEventListener('click', function() {
            let val = this.getAttribute('data-amount');
            amountInput.value = val === 'exact' ? currentTotal : val;
            calculateChange();
        });
    });

    let dpMinTotal = {{ isset($dpSettings) ? $dpSettings['min_nominal_dp'] : 10000000 }};
    let dpPercent = {{ isset($dpSettings) ? $dpSettings['dp_percent'] : 50 }};

    window.setPaymentMethod = function(method) {
        document.getElementById('selected-payment-method').value = method;
        document.querySelectorAll('.btn-method').forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-method') === method) {
                btn.style.background = method === 'DP B2B' ? 'var(--pos-warning)' : 'var(--pos-primary)';
                btn.style.color = 'white';
            } else {
                btn.style.background = 'var(--pos-card)';
                btn.style.color = btn.classList.contains('dp-btn') ? 'var(--pos-warning)' : 'white';
            }
        });

        const labelAmount = document.getElementById('label-amount-paid');
        const shortcuts = document.getElementById('cash-shortcuts-area');
        const dpInfo = document.getElementById('dp-info-area');
        
        if (method === 'DP B2B') {
            labelAmount.innerText = 'Nominal DP Diterima';
            labelAmount.style.color = 'var(--pos-warning)';
            shortcuts.style.display = 'none';
            dpInfo.style.display = 'block';
            document.getElementById('process-payment-btn').innerHTML = '<i class="fas fa-handshake"></i> Konfirmasi DP (F9)';
        } else {
            labelAmount.innerText = 'Tunai Pembeli';
            labelAmount.style.color = 'var(--pos-text-muted)';
            shortcuts.style.display = 'flex';
            dpInfo.style.display = 'none';
            document.getElementById('process-payment-btn').innerHTML = '<i class="fas fa-bolt"></i> Bayar & Cetak (F9)';
        }
        calculateChange();
    };

    function calculateChange() {
        let paid = parseInt(amountInput.value) || 0;
        let method = document.getElementById('selected-payment-method').value;
        let change = paid - currentTotal;
        
        if(currentTotal === 0) {
            changeDisplay.innerText = method === 'DP B2B' ? "Sisa Tagihan: Rp 0" : "Kembali: Rp 0";
            changeDisplay.style.color = 'var(--pos-text-muted)';
            return;
        }

        if (method === 'DP B2B') {
            let sisa = currentTotal - paid;
            let minDp = currentTotal * (dpPercent / 100);
            
            if (paid < minDp) {
                changeDisplay.innerText = "Kurang dari Min DP ("+dpPercent+"%)";
                changeDisplay.style.color = 'var(--pos-danger)';
            } else if (paid > currentTotal) {
                changeDisplay.innerText = "DP Lebihi Total!";
                changeDisplay.style.color = 'var(--pos-danger)';
            } else {
                changeDisplay.innerText = "Sisa Tagihan: Rp " + formatRp(sisa);
                changeDisplay.style.color = 'var(--pos-warning)';
            }
            return;
        }

        if(change < 0) {
            changeDisplay.innerText = "Uang Kurang!";
            changeDisplay.style.color = 'var(--pos-danger)';
        } else {
            changeDisplay.innerText = "Kembali: Rp " + formatRp(change);
            changeDisplay.style.color = 'var(--pos-success)';
        }
    }

    document.getElementById('process-payment-btn').addEventListener('click', function() {
        let paid = parseInt(amountInput.value) || 0;
        let method = document.getElementById('selected-payment-method').value;
        
        if (method === 'DP B2B') {
            if (currentTotal < dpMinTotal) {
                Swal.fire({icon: 'error', title: 'Belum Memenuhi Syarat', text: 'Total belanja harus minimal Rp ' + formatRp(dpMinTotal) + ' untuk menggunakan fitur DP B2B.'}); 
                return;
            }
            let minDp = currentTotal * (dpPercent / 100);
            if (paid < minDp) {
                Swal.fire({icon: 'error', title: 'DP Kurang', text: 'Minimal DP adalah ' + dpPercent + '% dari total belanja (Rp ' + formatRp(minDp) + ').'}); 
                return;
            }
            if (paid > currentTotal) {
                Swal.fire({icon: 'error', title: 'DP Tidak Valid', text: 'Jumlah DP tidak boleh melebihi total belanja.'}); 
                return;
            }
        } else {
            if(paid < currentTotal) { 
                Swal.fire({icon: 'error', title: 'Ups!', text: 'Uang tunai kurang.'}); 
                return; 
            }
        }
        
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> MEMPROSES...';
        this.disabled = true;
        fetch("{{ route('seller.pos.api.checkout') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({
                user_id: document.getElementById('pos-user-id').value,
                kasir_name: document.getElementById('kasir-name').value || 'Kasir',
                payment_method: method,
                amount_paid: paid,
                total: currentTotal,
                cart: cart
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                let htmlContent = method === 'DP B2B' 
                    ? `<div style="color: #f59e0b; font-weight: 700; margin-bottom: 10px;">Sisa Tagihan (Belum Lunas):</div><div style="font-size: 32px; font-weight: 900; color: #f59e0b; font-family: 'JetBrains Mono';">Rp ${formatRp(currentTotal - paid)}</div>`
                    : `<div style="color: #94a3b8; font-weight: 700; margin-bottom: 10px;">Kembalian:</div><div style="font-size: 32px; font-weight: 900; color: #3b82f6; font-family: 'JetBrains Mono';">Rp ${formatRp(paid - currentTotal)}</div>`;
                
                Swal.fire({
                    title: method === 'DP B2B' ? 'DP BERHASIL DICATAT' : 'TRANSAKSI BERHASIL',
                    html: htmlContent,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-print"></i> Cetak Struk',
                    cancelButtonText: 'Tutup',
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#1e293b'
                }).then((result) => {
                    if (result.isConfirmed) window.open("{{ url('seller/pos/print') }}/" + data.invoice, '_blank');
                    cart = []; amountInput.value = ''; updateCartDisplay(); loadProducts(); resetBtn();
                });
            } else { 
                Swal.fire('Gagal', data.message, 'error'); resetBtn(); 
            }
        })
        .catch(err => { Swal.fire('Error', 'Kesalahan koneksi.', 'error'); resetBtn(); });
    });

    function resetBtn() {
        const btn = document.getElementById('process-payment-btn');
        btn.innerHTML = '<i class="fas fa-bolt"></i> Bayar & Cetak (F9)';
        btn.disabled = false;
    }

    document.getElementById('clear-cart-btn').addEventListener('click', () => {
        cart = []; updateCartDisplay(); amountInput.value = ''; calculateChange();
    });

    document.addEventListener('keydown', function(e) {
        if(e.key === 'F2') { e.preventDefault(); searchInput.focus(); }
        if(e.key === 'F9') { e.preventDefault(); document.getElementById('process-payment-btn').click(); }
    });

    loadProducts(); 
    loadCategories();
});
</script>
@endpush
