<!-- Footer -->
<footer>
<hr />
  <ul class="list-unstyled text-center">
        <li><?php printf(Lang::T("POWERED_BY_TT"), VERSION);?></li>
        <li><?php $totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['tstart'];?></li>
        <li><?php printf(Lang::T("PAGE_GENERATED_IN"), $totaltime);?></li>
        <li><a href="https://torrenttrader.uk" target="_blank">torrenttrader.uk</a> -|- <a href='<?php echo URLROOT; ?>/rss'><i class="fa fa-rss-square"></i> <?php echo Lang::T("RSS_FEED"); ?></a> - <a href='<?php echo URLROOT; ?>/rss/custom'><?php echo Lang::T("FEED_INFO"); ?></a></li>
        <li>Bootstrap v5.1.0 -|- jQuery 3.4.1</li>
		<li>Update By: <a href="https://github.com/M-jay84/Torrent-Trader-MVC-PDO-OOP" target="_blank">M-jay</a> 2020</li>
      </ul>
</footer>

</div>
</section>

</div>
</div>

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
$(document).ready(function(){
	$("#search-box").keyup(function(){
		$.ajax({
		type: "POST",
		url: "<?php echo URLROOT; ?>/ajax",
		data:'keyword='+$(this).val(),
		beforeSend: function(){
			$("#search-box").css("background","#FFF url(<?php echo URLROOT; ?>/LoaderIcon.gif) no-repeat 165px");
		},
		success: function(data){
			$("#suggesstion-box").show();
			$("#suggesstion-box").html(data);
			$("#search-box").css("background","#242424");
		}
		});
	});
});

function userCountry(val) {
$("#search-box").val(val);
$("#suggesstion-box").hide();
}
</script>
<script>
  let arrow = document.querySelectorAll(".arrow");
  for (var i = 0; i < arrow.length; i++) {
    arrow[i].addEventListener("click", (e)=>{
   let arrowParent = e.target.parentElement.parentElement;//selecting main parent of arrow
   arrowParent.classList.toggle("showMenu");
    });
  }
  let sidebar = document.querySelector(".sidebar");
  let sidebarBtn = document.querySelector(".bx-menu");
  console.log(sidebarBtn);
  sidebarBtn.addEventListener("click", ()=>{
    sidebar.classList.toggle("close");
  });
  </script>
  </body>
</html>
<?php ob_end_flush();?>