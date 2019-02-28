function createXHR() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	}
	else if(window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	}
}

function getDocHeight() {
	var d=document, b = d.body, e=d.documentElement;
	return Math.max(
	b.scrollHeight, e.scrollHeight,	b.offsetHeight, e.offsetHeight,	b.clientHeight, e.clientHeight
	);
}

// Cross platform support for the inner height of the client window
function getWinHeight() {
	var w=window, d=document, e=d.documentElement,g=d.getElementsByTagName('body')[0],
	y = w.innerHeight || e.clientHeight || g.clientHeight;
	return y;
}

// Cross platform support to get the Y coordinate of the top of the visible part of the page
function getScrollPosition() {
	var w=window, d=document, e=d.documentElement;
	var scrollposition = (w.pageYOffset || e.scrollTop)  - (e.clientTop || 0);
	return scrollposition;
}
