<?php

	include "sites.php";

	$site_data = array();
	foreach($sites as $site) {
		$data = json_decode(file_get_contents("http://" . $site["domain"] . "/?json=1"));
		$data->name = $site["name"];
		$data->ip = gethostbyname($site["domain"]);
		$site_data[] = $data;
	}

?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Status</title>
		<link rel="stylesheet" href="css/main.css" />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="js/jqknob.js"></script>
		<script>
			$(document).ready(function() {
				// Show ring charts
				$(".k-disk, .k-memory, .k-swap, .k-cpu").knob({
					readOnly: true,
					width: 40,
					height: 40,
					thickness: 0.2,
					fontWeight: 'normal',
					bgColor: 'rgba(127,127,127,0.15)', // 50% grey with a low opacity, should work with most backgrounds
					fgColor: '#ccc'
				});
			});
		</script>
	</head>
	<body>
		<section class="header">
			<h1>Server Status Page</h1>
		</section>
		<section class="list">
			<?php foreach($site_data as $data) { ?>
				<!--
<?php var_dump($data); ?>
				-->
				<div class="status">
					<div class="grey">
						<div class="left">
							<h4><?php echo $data->name; ?></h4>
							<span><?php echo $data->ip; ?></span>
						</div>
						<div class="right">
							Uptime: <span class="uptime"><?php echo $data->uptime; ?></span>&emsp;
							Disk usage: <input class="k-disk" value="<?php echo $data->disk; ?>">&emsp;
							Memory: <input class="k-memory" value="<?php echo $data->memory; ?>">&emsp;
							<?php if($data->swap_total) { ?>
								Swap: <input class="k-swap" value="<?php echo $data->swap; ?>">&emsp;
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>
		</section>
	</body>
</html>
