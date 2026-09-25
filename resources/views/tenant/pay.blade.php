@extends('layouts.app') @section('title', 'Pay Rent – TenantCloud')
@section('content')

<style>
    .pay-wrap {
        max-width: 760px;
        margin: 0 auto;
    }

    /* ---- Progress bar ---- */
    .progress-labels {
        display: flex;
        gap: 16px;
        margin-bottom: 10px;
        font-size: 14px;
        font-weight: 600;
    }
    .progress-labels > div {
        flex: 1;
        color: #9ca3af;
    }
    .progress-labels > div.active {
        color: #16a34a;
    }
    .progress-labels .num {
        margin-right: 6px;
    }

    .progress-bars {
        display: flex;
        gap: 16px;
        margin-bottom: 40px;
    }
    .progress-bars .bar {
        flex: 1;
        height: 6px;
        border-radius: 6px;
        background: #e5e7eb;
    }
    .progress-bars .bar.active {
        background: #22c55e;
    }

    /* ---- Headings ---- */
    .pay-h2 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 4px;
        color: #111827;
    }
    .pay-sub {
        color: #6b7280;
        font-size: 14px;
        margin-bottom: 32px;
    }
    .pay-section {
        margin-bottom: 28px;
    }
    .pay-section h3 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 14px;
        color: #111827;
    }

    /* ---- Info boxes ---- */
    .pay-box {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 22px 26px;
    }
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    /* ---- Inputs ---- */
    .card-input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        outline: none;
        transition: border 0.15s;
        font-family: inherit;
        background: #fff;
    }
    .card-input:focus {
        border-color: #22c55e;
    }
    .card-input.is-error {
        border-color: #ef4444;
        background: #fef2f2;
    }
    .card-input.is-error:focus {
        border-color: #ef4444;
    }

    .card-label {
        display: block;
        font-size: 12px;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 8px;
    }
    .field-error {
        color: #ef4444;
        font-size: 12px;
        margin-top: 6px;
        min-height: 16px;
        display: block;
    }

    /* ---- Method selector ---- */
    .method-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }
    .method-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 18px 12px;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        cursor: pointer;
        background: #fff;
        transition:
            border-color 0.15s,
            background 0.15s,
            transform 0.15s;
        text-align: center;
    }
    .method-card:hover {
        border-color: #22c55e;
    }
    .method-card.selected {
        border-color: #22c55e;
        background: #f0fdf4;
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.15);
    }
    .method-card .name {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
    }
    .method-card .sub {
        font-size: 11px;
        color: #6b7280;
    }
    .method-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
    }
    .method-icon.card {
        background: #e0e7ff;
    }
    .method-icon.aba {
        background: #fee2e2;
    }
    .method-icon.cash {
        background: #dcfce7;
    }

    /* Mastercard mini icon */
    .mc {
        display: inline-flex;
    }
    .mc span {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: inline-block;
    }
    .mc .red {
        background: #eb001b;
    }
    .mc .yellow {
        background: #f79e1b;
        margin-left: -6px;
    }

    /* ---- ABA KHQR card ---- */
    .khqr-card {
        max-width: 380px;
        margin: 0 auto;
        background: #0f172a;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
    }
    .khqr-banner {
        background: #dc2626;
        color: #fff;
        padding: 18px 0;
        text-align: center;
        font-weight: 800;
        font-size: 22px;
        letter-spacing: 6px;
        font-family: "Segoe UI", sans-serif;
    }
    .khqr-body {
        padding: 26px 26px 30px;
    }
    .khqr-name {
        color: #e5e7eb;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 8px;
    }
    .khqr-amount {
        color: #fff;
        font-size: 34px;
        font-weight: 800;
        margin-bottom: 20px;
    }
    .khqr-divider {
        border-top: 2px dashed #334155;
        margin: 0 0 22px;
    }
    .khqr-qr-wrap {
        background: #fff;
        border-radius: 16px;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .khqr-qr-wrap img,
    .khqr-qr-wrap svg {
        width: 100%;
        max-width: 280px;
        height: auto;
        display: block;
    }
    .khqr-logo-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #dc2626;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid #fff;
    }
    .khqr-logo-overlay svg {
        width: 28px;
        height: 28px;
    }

    .khqr-help {
        text-align: center;
        color: #94a3b8;
        font-size: 12px;
        margin-top: 18px;
        line-height: 1.5;
    }

    /* ---- Cash panel ---- */
    .cash-panel {
        max-width: 440px;
        margin: 0 auto;
        text-align: center;
        padding: 30px 24px;
        background: #f0fdf4;
        border: 2px dashed #22c55e;
        border-radius: 16px;
    }
    .cash-panel .cash-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #22c55e;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 28px;
    }
    .cash-panel h4 {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 6px;
    }
    .cash-panel p {
        font-size: 14px;
        color: #6b7280;
        line-height: 1.5;
    }

    /* ---- Terms ---- */
    .terms {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        color: #6b7280;
        line-height: 1.55;
        margin: 32px 0 20px;
    }
    .terms input {
        margin-top: 4px;
        accent-color: #22c55e;
    }
    .terms a {
        color: #16a34a;
        font-weight: 600;
        text-decoration: none;
    }
    .terms a:hover {
        text-decoration: underline;
    }
    .terms.is-error {
        color: #ef4444;
    }

    .pay-note {
        font-size: 13px;
        color: #6b7280;
        line-height: 1.55;
        margin-bottom: 32px;
    }

    /* ---- Buttons ---- */
    .pay-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }
    .btn-back {
        background: #fff;
        border: 1.5px solid #111827;
        color: #111827;
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s;
    }
    .btn-back:hover {
        background: #f9fafb;
    }

    .btn-pay {
        background: #7c3aed;
        color: #fff;
        border: none;
        padding: 14px 32px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        transition:
            transform 0.15s,
            opacity 0.15s;
    }
    .btn-pay:hover {
        transform: translateY(-2px);
        opacity: 0.94;
    }

    /* ---- Success overlay ---- */
    .success-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 900;
    }
    .success-overlay.show {
        display: flex;
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .success-card {
        background: #fff;
        border-radius: 20px;
        padding: 44px 48px;
        text-align: center;
        max-width: 420px;
        width: 90%;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.25);
        animation: popIn 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes popIn {
        from {
            transform: scale(0.7);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
    .success-ring {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        background: #dcfce7;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    .success-ring svg {
        width: 56px;
        height: 56px;
    }
    .success-ring path {
        stroke: #16a34a;
        stroke-width: 4;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-dasharray: 60;
        stroke-dashoffset: 60;
        animation: draw 0.5s 0.15s ease forwards;
    }
    @keyframes draw {
        to {
            stroke-dashoffset: 0;
        }
    }

    .success-title {
        font-size: 22px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 6px;
    }
    .success-text {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 26px;
    }
    .success-btn {
        background: #22c55e;
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
    }
    .success-btn:hover {
        background: #16a34a;
    }

    @media (max-width: 640px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }
        .method-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="pay-wrap">
    {{-- ============ PROGRESS ============ --}}
    <div class="progress-labels">
        <div class="active">
            <span class="num">1.</span> Select Payment Options
        </div>
        <div class="active"><span class="num">2.</span> Review &amp; Pay</div>
    </div>
    <div class="progress-bars">
        <div class="bar active"></div>
        <div class="bar active"></div>
    </div>

    {{-- ============ HEADINGS ============ --}}
    <h2 class="pay-h2">Review &amp; Pay</h2>
    <p class="pay-sub">Please review payment details.</p>

    <form
        method="POST"
        action="{{ route('tenant.payments.store') }}"
        id="payForm"
        autocomplete="off"
        novalidate
    >
        @csrf
        <input type="hidden" name="method" id="methodField" value="card" />

        {{-- ============ BILLING DETAILS ============ --}}
        <div class="pay-section">
            <h3>Billing Details</h3>
            <div class="pay-box">
                <div class="grid-2">
                    <div>
                        <label class="card-label">Amount (USD)</label>
                        <input
                            type="number"
                            name="amount"
                            step="0.01"
                            min="1"
                            value="{{ old('amount', '15.00') }}"
                            placeholder="0.00"
                            class="card-input"
                            id="fieldAmount"
                        />
                        <span class="field-error" id="errAmount"></span>
                    </div>
                    <div>
                        <label class="card-label">Payment Date</label>
                        <input
                            type="date"
                            name="paid_on"
                            value="{{ old('paid_on', date('Y-m-d')) }}"
                            class="card-input"
                            id="fieldPaidOn"
                        />
                        <span class="field-error" id="errPaidOn"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ PAYMENT METHOD ============ --}}
        <div class="pay-section">
            <h3>Payment Method</h3>
            <div class="method-grid">
                <div
                    class="method-card selected"
                    data-method="card"
                    id="methodCard"
                >
                    <div class="method-icon card">
                        <span class="mc"
                            ><span class="red"></span
                            ><span class="yellow"></span
                        ></span>
                    </div>
                    <div>
                        <div class="name">Visa / Card</div>
                        <div class="sub">Credit or debit card</div>
                    </div>
                </div>

                <div class="method-card" data-method="aba_khqr" id="methodAba">
                    <div class="method-icon aba">
                        <span
                            style="
                                font-weight: 800;
                                color: #dc2626;
                                font-size: 14px;
                            "
                            >QR</span
                        >
                    </div>
                    <div>
                        <div class="name">ABA KHQR</div>
                        <div class="sub">Scan to pay</div>
                    </div>
                </div>

                <div class="method-card" data-method="cash" id="methodCash">
                    <div class="method-icon cash">
                        <span style="font-size: 20px">💵</span>
                    </div>
                    <div>
                        <div class="name">Cash</div>
                        <div class="sub">Pay in person</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ CARD PANEL ============ --}}
        <div class="pay-section" id="panelCard">
            <h3>Card Details</h3>
            <div class="pay-box">
                <div style="margin-bottom: 20px">
                    <label class="card-label">Card Number</label>
                    <div style="position: relative">
                        <input
                            type="text"
                            name="card_number"
                            id="cardNumber"
                            inputmode="numeric"
                            maxlength="19"
                            placeholder="1234 5678 9012 3456"
                            value="{{ old('card_number') }}"
                            class="card-input"
                            style="padding-right: 56px"
                        />
                        <div
                            class="card-icon"
                            style="
                                position: absolute;
                                right: 14px;
                                top: 50%;
                                transform: translateY(-50%);
                            "
                        >
                            <span class="mc"
                                ><span class="red"></span
                                ><span class="yellow"></span
                            ></span>
                        </div>
                    </div>
                    <span class="field-error" id="errCardNumber"></span>
                </div>

                <div class="grid-2" style="margin-bottom: 20px">
                    <div>
                        <label class="card-label">Expires On</label>
                        <input
                            type="text"
                            name="card_expiry"
                            id="cardExpiry"
                            inputmode="numeric"
                            maxlength="5"
                            placeholder="MM/YY"
                            value="{{ old('card_expiry') }}"
                            class="card-input"
                        />
                        <span class="field-error" id="errCardExpiry"></span>
                    </div>
                    <div>
                        <label class="card-label">CVC/CVV</label>
                        <input
                            type="text"
                            name="card_cvc"
                            id="cardCvc"
                            inputmode="numeric"
                            maxlength="4"
                            placeholder="123"
                            value="{{ old('card_cvc') }}"
                            class="card-input"
                        />
                        <span class="field-error" id="errCardCvc"></span>
                    </div>
                </div>

                <div>
                    <label class="card-label">Name on Card</label>
                    <input
                        type="text"
                        name="card_name"
                        id="cardName"
                        placeholder="Full name as shown on card"
                        value="{{ old('card_name', auth()->user()->name) }}"
                        class="card-input"
                    />
                    <span class="field-error" id="errCardName"></span>
                </div>
            </div>
        </div>

        {{-- ============ ABA KHQR PANEL ============ --}}
        <div class="pay-section" id="panelAba" style="display: none">
            <h3>ABA KHQR</h3>

            <div class="khqr-card">
                <div class="khqr-banner">KHQR</div>
                <div class="khqr-body">
                    <div class="khqr-name" id="khqrName">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="khqr-amount" id="khqrAmount">$15.00</div>
                    <div class="khqr-divider"></div>
                    <div class="khqr-qr-wrap">
                        {{-- Replace the src below with your real KHQR image or generate one dynamically --}}
                        <img
                            src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=KHQR-DEMO-TENANT-{{ auth()->id() }}"
                            alt="KHQR Code"
                        />
                        <div class="khqr-logo-overlay">
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#fff"
                                stroke-width="2.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M12 2l3 6 6 1-4.5 4.5L18 20l-6-3-6 3 1.5-6.5L3 9l6-1z"
                                />
                            </svg>
                        </div>
                    </div>
                    <div class="khqr-help">
                        Open your ABA Mobile app → scan this QR<br />
                        to complete your payment.
                    </div>
                </div>
            </div>

            <p
                style="
                    text-align: center;
                    font-size: 13px;
                    color: #6b7280;
                    margin-top: 18px;
                "
            >
                After scanning, click <strong>Pay</strong> below to record your
                payment.
            </p>
        </div>

        {{-- ============ CASH PANEL ============ --}}
        <div class="pay-section" id="panelCash" style="display: none">
            <h3>Cash Payment</h3>
            <div class="cash-panel">
                <div class="cash-icon">💵</div>
                <h4>Pay in person</h4>
                <p>
                    Bring the exact amount in cash to your landlord or property
                    office. After paying, click <strong>Pay</strong> below to
                    record it here.
                </p>
            </div>
        </div>

        {{-- ============ TERMS ============ --}}
        <label class="terms" id="termsLabel">
            <input type="checkbox" id="fieldTerms" />
            <span>
                I agree to the <a href="#">Terms and Conditions</a>. The amount
                of any autopay will be your total outstanding account balance at
                the time the invoice(s) is (are) due, which will include
                tuition, fees and other charges. As a result, the amount and
                frequency of your autopay may vary.
            </span>
        </label>
        <span
            class="field-error"
            id="errTerms"
            style="margin-top: -14px; display: block"
        ></span>

        <p class="pay-note">
            Click on the 'Pay' to make a payment and wait until it completes. Do
            not refresh your browser or use the browser's 'Back' button, or
            duplicate payments could result.
        </p>

        {{-- ============ ACTIONS ============ --}}
        <div class="pay-actions">
            <a href="{{ route('tenant.dashboard') }}" class="btn-back">Back</a>
            <button type="submit" class="btn-pay" id="payBtn">
                Pay <span id="payAmountLabel">$15.00</span>
            </button>
        </div>
    </form>
</div>

{{-- ============ SUCCESS OVERLAY ============ --}}
<div class="success-overlay" id="successOverlay">
    <div class="success-card">
        <div class="success-ring">
            <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" /></svg>
        </div>
        <div class="success-title">Payment Successful</div>
        <div class="success-text">Your rent payment has been recorded.</div>
        <button
            type="button"
            class="success-btn"
            onclick="window.location='{{ route('tenant.dashboard') }}'"
        >
            Back to Dashboard
        </button>
    </div>
</div>

<script>
    // ---------- helpers ----------
    function setError(inputId, errId, message) {
        var input = document.getElementById(inputId);
        var err = document.getElementById(errId);
        if (message) {
            input.classList.add("is-error");
            err.textContent = message;
        } else {
            input.classList.remove("is-error");
            err.textContent = "";
        }
    }

    // ---------- Payment method switching ----------
    var methodField = document.getElementById("methodField");
    var panelCard = document.getElementById("panelCard");
    var panelAba = document.getElementById("panelAba");
    var panelCash = document.getElementById("panelCash");
    var methodCards = document.querySelectorAll(".method-card");

    methodCards.forEach(function (card) {
        card.addEventListener("click", function () {
            methodCards.forEach(function (c) {
                c.classList.remove("selected");
            });
            card.classList.add("selected");

            var m = card.getAttribute("data-method");
            methodField.value = m;

            panelCard.style.display = m === "card" ? "block" : "none";
            panelAba.style.display = m === "aba_khqr" ? "block" : "none";
            panelCash.style.display = m === "cash" ? "block" : "none";
        });
    });

    // ---------- Auto-format card number ----------
    var cardNumber = document.getElementById("cardNumber");
    cardNumber.addEventListener("input", function () {
        var digits = this.value.replace(/\D/g, "").slice(0, 16);
        this.value = digits.replace(/(.{4})/g, "$1 ").trim();
        setError("cardNumber", "errCardNumber", "");
    });

    var cardExpiry = document.getElementById("cardExpiry");
    cardExpiry.addEventListener("input", function () {
        var digits = this.value.replace(/\D/g, "").slice(0, 4);
        this.value =
            digits.length >= 3
                ? digits.slice(0, 2) + "/" + digits.slice(2)
                : digits;
        setError("cardExpiry", "errCardExpiry", "");
    });

    var cardCvc = document.getElementById("cardCvc");
    cardCvc.addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, "").slice(0, 4);
        setError("cardCvc", "errCardCvc", "");
    });

    document.getElementById("cardName").addEventListener("input", function () {
        setError("cardName", "errCardName", "");
    });

    // ---------- Live amount update (button + KHQR card) ----------
    var amountInput = document.getElementById("fieldAmount");
    var amountLabel = document.getElementById("payAmountLabel");
    var khqrAmount = document.getElementById("khqrAmount");

    function updateAmountLabel() {
        var v = parseFloat(amountInput.value);
        var text = "$" + (isNaN(v) ? "0.00" : v.toFixed(2));
        amountLabel.textContent = text;
        khqrAmount.textContent = text;
    }
    amountInput.addEventListener("input", function () {
        updateAmountLabel();
        setError("fieldAmount", "errAmount", "");
    });
    updateAmountLabel();

    document
        .getElementById("fieldPaidOn")
        .addEventListener("input", function () {
            setError("fieldPaidOn", "errPaidOn", "");
        });

    document
        .getElementById("fieldTerms")
        .addEventListener("change", function () {
            document.getElementById("errTerms").textContent = "";
            document.getElementById("termsLabel").classList.remove("is-error");
        });

    // ---------- Submit with inline validation ----------
    document.getElementById("payForm").addEventListener("submit", function (e) {
        e.preventDefault();
        var form = this;
        var method = methodField.value;

        var amount = form.querySelector('input[name="amount"]').value.trim();
        var paidOn = form.querySelector('input[name="paid_on"]').value.trim();
        var terms = document.getElementById("fieldTerms").checked;
        var hasError = false;
        var firstErrorField = null;

        // Reset card errors
        setError("fieldAmount", "errAmount", "");
        setError("fieldPaidOn", "errPaidOn", "");
        setError("cardNumber", "errCardNumber", "");
        setError("cardExpiry", "errCardExpiry", "");
        setError("cardCvc", "errCardCvc", "");
        setError("cardName", "errCardName", "");
        document.getElementById("errTerms").textContent = "";

        if (!amount || parseFloat(amount) < 1) {
            setError(
                "fieldAmount",
                "errAmount",
                "Please enter a valid amount.",
            );
            hasError = true;
            firstErrorField = firstErrorField || "fieldAmount";
        }
        if (!paidOn) {
            setError(
                "fieldPaidOn",
                "errPaidOn",
                "Please choose a payment date.",
            );
            hasError = true;
            firstErrorField = firstErrorField || "fieldPaidOn";
        }

        // Card-only validation
        if (method === "card") {
            var cardNum = document.getElementById("cardNumber").value.trim();
            var cardExp = document.getElementById("cardExpiry").value.trim();
            var cardCvc = document.getElementById("cardCvc").value.trim();
            var cardName = document.getElementById("cardName").value.trim();
            var digitsOnly = cardNum.replace(/\D/g, "");

            if (digitsOnly.length < 13) {
                setError(
                    "cardNumber",
                    "errCardNumber",
                    "Enter a valid card number (13–16 digits).",
                );
                hasError = true;
                firstErrorField = firstErrorField || "cardNumber";
            }
            if (!/^\d{2}\/\d{2}$/.test(cardExp)) {
                setError("cardExpiry", "errCardExpiry", "Use MM/YY format.");
                hasError = true;
                firstErrorField = firstErrorField || "cardExpiry";
            }
            if (cardCvc.length < 3) {
                setError("cardCvc", "errCardCvc", "At least 3 digits.");
                hasError = true;
                firstErrorField = firstErrorField || "cardCvc";
            }
            if (!cardName) {
                setError(
                    "cardName",
                    "errCardName",
                    "Enter the name on the card.",
                );
                hasError = true;
                firstErrorField = firstErrorField || "cardName";
            }
        }

        if (!terms) {
            document.getElementById("errTerms").textContent =
                "Please agree to the Terms and Conditions.";
            document.getElementById("termsLabel").classList.add("is-error");
            hasError = true;
            firstErrorField = firstErrorField || "fieldTerms";
        }

        if (hasError) {
            if (firstErrorField)
                document.getElementById(firstErrorField).focus();
            return;
        }

        document.getElementById("successOverlay").classList.add("show");
        setTimeout(function () {
            form.submit();
        }, 2200);
    });
</script>

@endsection
