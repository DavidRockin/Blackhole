<?php
global $config;
$memes = $config['memes'];
?>

		</div><!--/row-->
		
		<hr>
		
		<footer>
			<div class="row">
				<div class="col-md-9" style="padding-top:60px">
					<p>&copy; <?=date("Y")?> Tkachuk Tech &ndash; <?=$memes[rand() % count($memes)]?></p>
					<p><a href="https://github.com/DavidRockin/Blackhole" target="_blank">View source code on Github</a></p>
				</div>
				<div class="col-md-3">
					<img src="/assets/images/Black-Hole.png" class="pull-right" style="max-width:100%" />
				</div>
			</div>
		</footer>
		
	</div><!--/.container-->
	
	
	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="/assets/js/jquery.min.js"></script>
	<script src="/assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/assets/js/dropzone.js"></script>
	<script src="/assets/js/main.js?<?=time()?>"></script>
</body>
</html>
