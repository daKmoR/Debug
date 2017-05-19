<?php

	function renderDebug() {
		Debug::render();
	}

	add_filter('template_finished', 'renderDebug', 10);