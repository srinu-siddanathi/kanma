import './bootstrap';
import $ from 'jquery';
import 'bootstrap';
import Swiper from 'swiper';

// Your custom JavaScript code
$(document).ready(function() {
    // Initialize Swiper
    const swiper = new Swiper('.main-swiper', {
        loop: true,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-next',
            prevEl: '.swiper-prev',
        },
    });

    // Example: Handle button clicks
    $('.btn-wishlist').on('click', function() {
        alert('Added to wishlist!');
    });
});
