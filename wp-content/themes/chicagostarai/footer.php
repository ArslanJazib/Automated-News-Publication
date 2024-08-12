<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package chicagostarai
 */

//  get_template_part( 'template-parts/content', 'banner_cta' );
?>

<footer class="footer">

    <a id="button"></a>

    <style>
        #button {
            display: inline-block;
            background-color: #005194;
            width: 40px;
            height: 40px;
            text-align: center;
            border-radius: 4px;
            position: fixed;
            bottom: 40px;
            right: 20px;
            transition: background-color .3s,
                opacity .5s, visibility .5s;
            opacity: 0;
            visibility: hidden;
            z-index: 1000;
        }

        #button::after {
            content: "\f077";
            font-family: FontAwesome;
            font-weight: normal;
            font-style: normal;
            font-size: 1.3em;
            line-height: 50px;
            color: #fff;
            transform: translate(-50%, -50%);
            position: absolute;
            top: 50%;
            left: 50%;
        }

        #button:hover {
            cursor: pointer;
            background-color: #333;
        }

        #button:active {
            background-color: #555;
        }

        #button.show {
            opacity: 1;
            visibility: visible;
        }

        @media screen and (max-width:767px) {
            #button {
                width: 30px;
                height: 30px;
                right: 15px;
            }

            #button::after {
                font-size: 1em;
            }
        }
    </style>

</footer>

<!-- <footer class="footer">
        <div class="container">
            <div class="inner">
                <div class="footer__top row py-4 align-items-center">
                    <div class="col-md-12">
                        <?php the_custom_logo(); ?>
                    </div>
                   <div class="col-md-6">
                        <div class="footer__social--links">
                            <a href="" class="fa-brands fa-facebook"></a>
                            <a href="" class="fa-brands fa-instagram"></a>
                            <a href="" class="fa-brands fa-twitter"></a>
                            <a href="" class="fa-brands fa-youtube"></a>
                            <a href="" class="fa-brands fa-linkedin"></a>
                        </div>
                    </div> 
                </div>
                <div class="footer__bottom text-center py-4">
                    <p class="copyright__text mb-0">Copyright &copy; 2023 chicagostarai. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer> -->


</div> <!-- Site div closing tag  -->
<?php wp_footer(); ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>

<script>
    var btn = jQuery('#button');

    jQuery(window).scroll(function() {
        if (jQuery(window).scrollTop() > 300) {
            btn.addClass('show');
        } else {
            btn.removeClass('show');
        }
    });

    btn.on('click', function(e) {
        e.preventDefault();
        jQuery('html, body').animate({
            scrollTop: 0
        }, '300');
    });
</script>
</body>

</html>