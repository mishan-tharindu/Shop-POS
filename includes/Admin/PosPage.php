<?php

namespace Inc\Admin;

class PosPage {


    public function display(){
                ?>

        <div class="pos-container">
                    <div class="pos-header">
                        <h2>Point of Sale</h2>
                    </div>

                    <div class="pos-body">
                        <!-- Product Search and Cart Section -->
                        <div class="product-section">
                            <div class="product-search">
                                <!-- <input type="text" id="productSearch" placeholder="Search for a product...">
                                <button onclick="searchProduct()">Add</button> -->
                                <input type="text" id="productSearch" placeholder="Search for a product..." list="searchResultList">
                                <div id="searchResults" style="position: absolute; background: #fff; border: 1px solid #ccc; max-height: 200px; overflow-y: auto;"></div>
                                <button id="addProduct">Add</button>
                            </div>

                            <div class="cart">
                                <h3>Cart</h3>
                                <table id="cartTable">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Cart items will be dynamically added here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Payment and Summary Section -->
                        <div class="summary-section">
                            <div class="summary">
                                <h3>Summary</h3>
                                <p>Subtotal: $<span id="subtotal">0.00</span></p>
                                <p>Discount: <input type="number" id="discount" value="0" onchange="applyDiscount()">%</p>
                                <p>Tax (10%): $<span id="tax">0.00</span></p>
                                <p>Total: $<span id="total">0.00</span></p>
                            </div>

                            <div class="payment-method">
                                <h3>Payment Method</h3>
                                <select id="paymentMethod">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <button onclick="finalizeSale()">Finalize Sale</button>
                        </div>
                    </div>
                </div>

                <!-- Invoice Section -->
                <div id="invoice" style="display:none;">
                    <h2>Invoice</h2>
                    <p>Date: <span id="invoiceDate"></span></p>
                    <p>Payment Method: <span id="invoicePaymentMethod"></span></p>
                    <table id="invoiceTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Invoice items will be dynamically added here -->
                        </tbody>
                    </table>
                    <p>Subtotal: $<span id="invoiceSubtotal"></span></p>
                    <p>Discount: $<span id="invoiceDiscount"></span></p>
                    <p>Tax: $<span id="invoiceTax"></span></p>
                    <p>Total: $<span id="invoiceTotal"></span></p>

                    <button onclick="printInvoice()">Print Invoice</button>
                </div>


                <?php
    }



}