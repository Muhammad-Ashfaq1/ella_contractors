<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <div id="estimates-list-container">
            <!-- Estimates will be loaded here via AJAX -->
            <div class="text-center">
                <i class="fa fa-spinner fa-spin fa-2x"></i>
                <p>Loading estimates...</p>
            </div>
        </div>
    </div>
</div>

<style>
.estimate-card {
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 15px;
    background: #fff;
    transition: all 0.3s ease;
}

.estimate-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-color: #d0d0d0;
}

.estimate-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.estimate-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.estimate-id {
    color: #888;
    font-size: 14px;
}

.estimate-body {
    margin: 10px 0;
}

.estimate-info-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    border-bottom: 1px solid #f5f5f5;
}

.estimate-info-label {
    color: #666;
    font-weight: 500;
}

.estimate-info-value {
    color: #333;
    font-weight: 400;
}

.estimate-total {
    font-size: 18px;
    font-weight: 700;
    color: #2ecc71;
}

.estimate-footer {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.estimate-meta {
    font-size: 12px;
    color: #999;
}

.estimate-actions {
    display: flex;
    gap: 5px;
}

.estimate-empty {
    text-align: center;
    padding: 40px 20px;
    color: #999;
}

.estimate-empty i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}
</style>

