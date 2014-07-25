<?php

include "sites.php";

$site_data = array();
foreach($sites as $site) {
	try {
		$result = file_get_contents("http://" . $site["domain"] . "/?json=1");
		$data = json_decode($result);
	} catch(Exception $e) {
		$data = new StdClass();
		$data->error = 1;
	}
	$data->name = $site["name"];
	$data->domain = $site["domain"];
	$data->id = sha1($site["domain"]);
	$data->ip = gethostbyname($data->domain);
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
		var servers = [];
		function update() {
			$.each(servers, function(i, server) {
				$.post('ping.php', {host: server}, function(data) {
					$server = $("#" + data.id);

					$server.find(".offline").remove();
					$server.find("meters").show();

					$server.find('.uptime').text(data.uptime);
					$server.find('.k-cpu').val(data.cpu).trigger("change");
					$server.find('.k-memory').val(data.memory).trigger("change");
					if(data.swap_total) {
						$server.find('.k-swap').val(data.swap).trigger("change");
					}

				}, 'json').fail(function() {
					$server.find("meters").hide();
					$("<b />").addClass("offline").text("Offline").appendTo($server);
				});
			});

			window.setTimeout(update, 5000);
		}
		$(document).ready(function() {
			// Show ring charts
			$(".k-disk, .k-memory, .k-swap, .k-cpu").knob({
				readOnly: true,
				width: 40,
				height: 40,
				thickness: 0.2,
				fontWeight: 'normal',
				bgColor: 'rgba(127,127,127,0.15)',
				fgColor: '#ccc'
			});
			// Start AJAX update loop
			update();
		});
	</script>
</head>
<body>
	<!-- <section class="header">
		<h1>Status</h1>
	</section> -->
	<section class="list">
		<?php foreach($site_data as $data) { ?>
			<div class="status" id="<?php echo $data->id; ?>">
				<div class="grey">
					<div class="left">
						<h4><?php echo $data->name; ?></h4>
						<span><?php echo $data->ip; ?></span>
					</div>
					<div class="right">
						<?php if(!empty($data->error)) { ?>
							<b class="offline">Offline</b>
						<?php } ?>
						<div class="meters" style="<?php if(!empty($data->error)) echo 'display: none;'; ?>">
							Uptime: <span class="uptime"><?php echo $data->uptime; ?></span>&emsp;
							Disk usage: <input class="k-disk" value="<?php echo $data->disk; ?>">&emsp;
							Memory: <input class="k-memory" value="<?php echo $data->memory; ?>">&emsp;
							<?php if($data->swap_total) { ?>
								Swap: <input class="k-swap" value="<?php echo $data->swap; ?>">&emsp;
							<?php } ?>
							CPU: <input class="k-cpu" value="<?php echo $data->cpu; ?>">&emsp;
						</div>
						<script>servers.push('<?php echo $data->domain; ?>');</script>
					</div>
				</div>
			</div>
		<?php } ?>
	</section>
</body>
</html>
