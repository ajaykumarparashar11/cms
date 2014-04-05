<style>.startup-menu { display:none; }</style>
<p><h1>Welcome to QuickApps CMS</h1></p>
<p>&nbsp;</p>
<ul class="nav nav-pills nav-stacked languages">
	<?php foreach ($languages as $code => $link): ?>
	<li class="<?php echo $code === 'eng' ? "active locale-{$code}" : "locale-{$code}"; ?>"><?php echo $this->Html->link($link['action'], $link['url'], ['title' => $link['action'], 'data-welcome' => $link['welcome']]); ?></li>
	<?php endforeach; ?>
</ul>
<script type="text/javascript" charset="utf-8">
	function changeHeader() {
		active = $('ul.languages li.active');
		next = $(active).next();
		if (!next.length) {
			next = $('ul.languages li')[0];
		}
		$(active).toggleClass('active');
		$(next).toggleClass('active');
		$('.well h1').fadeOut(300, function() {
			$('.well h1').html($(next).children('a').attr('data-welcome'));
			$('.well h1').fadeIn(300);
		});
		window.setTimeout(changeHeader, 3000);
	}
	window.setTimeout(changeHeader, 3000);
</script>