jQuery(function ($) {
    "use strict";
    mobileMenu('#mobile__menu', '#mobile__menu--button');
    jQuery(window).scrollTop() > 10 ?
        jQuery(".site-header").addClass('fixed-header') :
        jQuery(".site-header").removeClass('fixed-header');
    jQuery(window).scroll(function() {
        jQuery(window).scrollTop() > 10 ?
        jQuery(".site-header").addClass('fixed-header') :
        jQuery(".site-header").removeClass('fixed-header');
    });
});


function mobileMenu ( menu, button ) {
    jQuery( button ).on( 'click', function() {
        jQuery( button ).stop().toggleClass( 'open' );
        jQuery( menu ).stop().slideToggle( 500 );
        jQuery( 'body, html' ).stop().toggleClass( 'overflow-hidden' );
    } );
    jQuery( menu + ' .menu-item-has-children > a' ).on( 'click', function( e ) {
        e.preventDefault();
        jQuery( this ).parent().toggleClass( 'active' ).find( '.sub-menu' ).stop().slideToggle( 300 );
    } );
    jQuery('#nav-toggler').on( 'click', function( e ) {
        e.preventDefault();
        jQuery( '#secondary-menu' ).toggleClass( 'active' );
        jQuery( this ).toggleClass( 'active' );
    } );
    jQuery('.sidebar-toggler').on( 'click', function( e ) {
        e.preventDefault();
        jQuery( '.side-bar-nav' ).toggleClass( 'active' );
        jQuery( this ).toggleClass( 'active' );
    } );
}