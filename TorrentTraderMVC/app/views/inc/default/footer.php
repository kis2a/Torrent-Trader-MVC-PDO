<?php if (MIDDLENAV) {?>
  <?php Block::middle();?>
  <?php }?>
</div>
<!-- END MIDDLE COLUMN -->
<!-- START RIGHT COLUMN -->
<?php if (RIGHTNAV) {?>
  <div class="col-sm-2">
        <?php Block::right();?>
    </div>
    <?php }?>
    <!-- END RIGHT COLUMN -->
</div>
</div>
<!-- END MAIN -->
<!-- Footer -->
<footer>
<hr />
  <ul class="list-unstyled text-center">
        <li><?php printf(Lang::T("POWERED_BY_TT"), VERSION);?></li>
        <li><?php $totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['tstart'];?></li>
        <li><?php printf(Lang::T("PAGE_GENERATED_IN"), $totaltime);?></li>
        <li><a href="https://torrenttrader.uk" target="_blank">torrenttrader.uk</a> -|- <a href='<?php echo URLROOT; ?>/rss'><i class="fa fa-rss-square"></i> <?php echo Lang::T("RSS_FEED"); ?></a> - <a href='<?php echo URLROOT; ?>/rss/custom'><?php echo Lang::T("FEED_INFO"); ?></a></li>
        <li>Bootstrap 4.3.1 -|- jQuery 3.4.1</li>
		<li>Update By: <a href="https://github.com/M-jay84/Torrent-Trader-MVC-PDO-OOP" target="_blank">M-jay</a> 2020</li>
      </ul>
</footer>
    <!-- Dont Change -->
    <script src="<?php echo URLROOT; ?>/assets/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo URLROOT; ?>/assets/js/popper.js"></script>
    <script src="<?php echo URLROOT; ?>/assets/js/bootstrap.min.js"></script>
    <script src="<?php echo URLROOT; ?>/assets/js/java_klappe.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/highlight.min.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>
    <script>
        function updateShouts(){
            // Assuming we have #shoutbox
            $('#shoutbox').load('shoutbox/chat');
        }
        setInterval( "updateShouts()", 15000 );
		updateShouts();
    </script>
<script>
function myFunction() {
  var x = document.getElementById("myDIVsmileytog");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
</script>

  </body>
</html>
<?php ob_end_flush();?>