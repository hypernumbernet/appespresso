
(function change_color(color_num) {
	'use strict';
	var COLORS = ['red', 'orange', 'yellow', 'green', 'blue', 'navy', 'purple'];
	document.getElementById('p_message').style.color = COLORS[color_num];
	++color_num;
	if (color_num === COLORS.length) {
		color_num = 0;
	}
	setTimeout(change_color, 500, color_num);
}(0));
