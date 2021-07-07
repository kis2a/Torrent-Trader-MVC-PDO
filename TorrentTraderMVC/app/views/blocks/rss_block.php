<?php
if ($_SESSION['loggedin'] == true) {
$feedUrl = "";
Style::block_begin(Lang::T("RSS"));
 if (!$feedUrl): ?>
            <p class="text-center">This would need editing with an rss feed of your choice.</p>
        <?php else: ?>
            <?php $xml = new RSS($feedUrl); ?>
        <?php endif; ?>

    <!-- end content -->

<?php
Style::block_end();
}