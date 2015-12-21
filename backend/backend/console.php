<?php
/**
 * Created by PhpStorm.
 * User: csibi
 * Date: 2014.09.08.
 * Time: 15:05
 */

?>
<div class="console-bar bg-yellow-saffron">
	<div class="console-content">
		<p>Valami szerkesztese</p>
	</div>
	<ul class="nav navbar-nav pull-right apply-bar">
		<!-- BEGIN USER LOGIN DROPDOWN -->
		<li>
			<a href="javascript: rr_edit.save_model();">
				<i class="fa fa-check-circle"></i><span class="">Mentés</span>
			</a>
		</li>
		<li>
			<a href="javascript: rr_edit.save_draft();">
				<i class="fa fa-code-fork"></i><span class="">Vázlat</span>
			</a>
		</li>
		<li class="warn">
			<a href="javascript: rr_edit.destruct({revert: true});">
				<i class="fa fa-times-circle"></i><span class="">Visszavonás</span>
			</a>
		</li>
		<!-- END USER LOGIN DROPDOWN -->
	</ul>

</div>
