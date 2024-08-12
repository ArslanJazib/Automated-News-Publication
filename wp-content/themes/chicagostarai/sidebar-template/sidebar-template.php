<div class="nav">

    <?php
    wp_nav_menu(
        array(
            'theme_location' => 'menu-1',
            'menu_id'        => 'primary-menu',
        )
    );
    ?>
    <!-- <ul>
        <li>
            <a href="" id="nav-toggler">Chicago Post <i class="fa-solid fa-caret-up"></i><i class="fa-solid fa-caret-down"></i></a>
        </li>
    </ul> -->
    <?php
    wp_nav_menu(
        array(
            'theme_location' => 'menu-2',
            'menu_id'        => 'secondary-menu',
        )
    );
    ?>

</div>



<!-- <div class="nav mt-4"> -->
    <!-- <ul> -->
        <!-- <h3 class="User-secheading">Account</h3> -->
        <!-- <li><a href=""><span><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                        <path d="M9.74485 1.9702C9.17473 1.38368 8.37844 1.06068 7.49954 1.06068C6.61595 1.06068 5.81702 1.38172 5.24954 1.96462C4.67591 2.55393 4.39642 3.35485 4.46204 4.2197C4.59212 5.92595 5.95472 7.31395 7.49954 7.31395C9.04436 7.31395 10.4046 5.92623 10.5367 4.22026C10.6032 3.36323 10.322 2.56398 9.74485 1.9702Z" fill="#005194" />
                        <path d="M12.6559 13.5666H2.34343C2.20845 13.5683 2.07477 13.5412 1.95213 13.4875C1.82949 13.4338 1.72096 13.3546 1.63444 13.2559C1.44402 13.039 1.36726 12.7428 1.42409 12.4432C1.67136 11.1362 2.44304 10.0382 3.65593 9.26747C4.73347 8.58324 6.09841 8.20665 7.49968 8.20665C8.90095 8.20665 10.2659 8.58352 11.3434 9.26747C12.5563 10.038 13.328 11.1359 13.5753 12.443C13.6321 12.7425 13.5553 13.0387 13.3649 13.2556C13.2784 13.3544 13.1699 13.4336 13.0473 13.4874C12.9246 13.5412 12.7909 13.5682 12.6559 13.5666Z" fill="#005194" />
                    </svg></span>Profile</a></li> -->
        <!-- <li><a href=""><span><svg width="15" height="15" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none"><path fill="#000000" fill-rule="evenodd" d="M6 2a3 3 0 0 0-3 3v14a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V5a3 3 0 0 0-3-3H6zm10.293 5.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414-1.414L18.586 13H10a1 1 0 1 1 0-2h8.586l-2.293-2.293a1 1 0 0 1 0-1.414z" clip-rule="evenodd"/></svg></span>Signout</a></li> -->
    <!-- </ul> -->
    <!-- <div class="need-help mt-5">
        <img src="<?php echo get_template_directory_uri() ?>/assets/images/icons/icon-documentation.svg" alt="">
        <div class="need-help-message">
            <h3 class="User-secheading m-0 p-0 text-center">Need help?</h3>
            <p>Please check our docs</p>
        </div>
        <button class="btn btn-dashboard">Documentaion</button>
    </div> -->
<!-- </div> -->