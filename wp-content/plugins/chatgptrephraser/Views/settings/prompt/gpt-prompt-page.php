<style>
    .delete-field {
        margin-left: 20px !important;
    }

    .button-container {
        margin-top: 20px !important;
    }
</style>

<div class="container">
    <h1>Chat GPT Prompt Settings</h1>

    <!-- Tabs -->
    <h2 class="nav-tab-wrapper">
        <a href="#placeholders-tab" class="nav-tab <?= $active_tab === 'placeholders-tab' ? 'nav-tab-active' : '' ?>">Placeholders</a>
        <a href="#instructions-tab" class="nav-tab <?= $active_tab === 'instructions-tab' ? 'nav-tab-active' : '' ?>">Instructions</a>
        <a href="#format-tab" class="nav-tab <?= $active_tab === 'format-tab' ? 'nav-tab-active' : '' ?>">Format</a>
        <a href="#output-tab" class="nav-tab <?= $active_tab === 'output-tab' ? 'nav-tab-active' : '' ?>">Output</a>
    </h2>

    <!-- Tab Content -->
    <div class="tab-content">
        <?php
        $tab_views = array(
            'placeholders-tab' => 'gpt-prompt-placeholder-tab.php',
            'instructions-tab' => 'gpt-prompt-instructions-tab.php',
            'format-tab' => 'gpt-prompt-format-tab.php',
            'output-tab' => 'gpt-prompt-output-tab.php',
        );

        foreach($tab_views as $tab => $file){
            include(dirname(plugin_dir_path(__FILE__)) . "/prompt/tabs/" . $file);
        }        
        ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabLinks = document.querySelectorAll('.nav-tab');
        const tabContents = document.querySelectorAll('.tab-content > div');

        tabLinks.forEach(function (tabLink) {
            tabLink.addEventListener('click', function (event) {
                event.preventDefault();

                tabLinks.forEach(function (link) {
                    link.classList.remove('nav-tab-active');
                });

                tabLink.classList.add('nav-tab-active');
                const tabId = tabLink.getAttribute('href').substring(1);

                tabContents.forEach(function (content) {
                    content.style.display = 'none';
                });

                document.getElementById(tabId).style.display = 'block';
            });
        });

        // Initialize by showing the first tab
        tabLinks[0].click();
    });
</script>