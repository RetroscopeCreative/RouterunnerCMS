<?php
$langs = array(
	"en" => "English",
	"hu" => "Magyar",
);
if (\runner::config('backend_language')) {
	$selected_code = \runner::config('backend_language');
	$selected_label = $langs[$selected_code];
	unset($langs[$selected_code]);
} else {
	$selected = array_shift($langs);
	$selected_code = key($selected);
	$selected_label = current($selected);
}
?>
<!-- BEGIN LANGUAGE DROPDOWN -->
<li class="dropdown dropdown-language dropdown-dark">
	<a href="admin?backend_lang=<?=$selected_code?>" class="dropdown-toggle" data-toggle="dropdown" data-close-others="true">
		<img alt="" src="<?php echo \runner::config("BACKEND_ROOT"); ?>metronic/assets/global/img/flags/<?=$selected_code?>.png">
		<span class="langname"><?=$selected_label?></span>
	</a>
	<ul class="dropdown-menu dropdown-menu-default">
		<?php
		foreach ($langs as $code => $label) {
			echo '		<li><a href="admin/?backend_lang=' . $code . '"><img alt="" src="' . \runner::config("BACKEND_ROOT") . 'metronic/assets/global/img/flags/' . $code . '.png"> ' . $label . ' </a></li>' . PHP_EOL;
		}
		?>
	</ul>
</li>
<!-- END LANGUAGE DROPDOWN -->