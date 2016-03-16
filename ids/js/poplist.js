function pophg(){
	var url2go = "hgsgAjax.php?mode=host&hg=" + document.myformhg.hgs.value;
	getRequest(
		url2go, // URL for the PHP file
		drawOutput,  // handle successful request
		drawError    // handle error
	);
	return false;
}
function popsg(){
	var url2go = "hgsgAjax.php?mode=service&sg=" + document.myformsg.sgs.value;
	getRequest(
		url2go, // URL for the PHP file
		drawOutput2,  // handle successful request
		drawError    // handle error
	);
	return false;
}

// Error Handler
function drawError() {
	alert('Bummer: there was an error!');
}

// handles the response, adds the html
function drawOutput(responseText) {
	var container = document.getElementById('output');
	container.innerHTML = responseText;
}

// handles the response, adds the html
function drawOutput2(responseText) {
	var container = document.getElementById('outputsg');
	container.innerHTML = responseText;
}

// helper function for cross-browser request object
function getRequest(url, success, error) {
	var req = false;
	try{
		// most browsers
		req = new XMLHttpRequest();
	} catch (e){
		// IE
		try{
			req = new ActiveXObject("Msxml2.XMLHTTP");
		} catch(e) {
		// try an older version
		try{
			req = new ActiveXObject("Microsoft.XMLHTTP");
		} catch(e) {
			return false;
		}
		}
	}
	if (!req) return false;
	if (typeof success != 'function') success = function () {};
	if (typeof error!= 'function') error = function () {};
	req.onreadystatechange = function(){
		if(req.readyState == 4) {
			return req.status === 200 ?
			success(req.responseText) : error(req.status);
		}
	}
	req.open("GET", url, true);
	req.send(null);
	return req;
}

// handles the response, adds the html
function drawOutput2(responseText) {
	var container = document.getElementById('outputsg');
	container.innerHTML = responseText;
}
