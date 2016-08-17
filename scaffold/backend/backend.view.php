<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<?php
\runner::route('head');
\runner::route(\runner::now("page"));
\runner::route('foot');
?>
<?php echo \runner::js_after(); ?>
</html>