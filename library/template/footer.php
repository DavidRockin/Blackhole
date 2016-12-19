<?php
global $config;
$memes = $config['memes'];
?>

		</div><!--/row-->
		
		<hr>
		
		<footer>
			<p>&copy; <?=date("Y")?> Tkachuk Tech &ndash; <?=$memes[rand() % count($memes)]?></p>
		</footer>
		
	</div><!--/.container-->
	
	
	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="/assets/js/jquery.min.js"></script>
	<script src="/assets/js/bootstrap.min.js"></script>
	<script src="/assets/js/offcanvas.js"></script>
</body>
</html>
