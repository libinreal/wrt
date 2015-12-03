(function() {
    var id = 'a' + new Date().getTime().toString(32);
    document.write('<div id="' + id + '"></div>');

    var replaceDom = document.getElementById(id);
    var ypath;
    var headerStr;
    var xmlhttp;

    if(window.localStorage){
        ypath = localStorage.getItem('ypath');
    }

    function loadXMLDoc(url) {
        xmlhttp = null;
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        if (xmlhttp != null) {
            xmlhttp.onreadystatechange = state_Change;
            xmlhttp.open("GET", url, true);
            xmlhttp.send(null);
        } else {
            alert("Your browser does not support XMLHTTP.");
        }
    }

    function state_Change() {
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status == 200) {
                headerStr = xmlhttp.responseText;
                if (ypath) {
                    headerStr = headerStr.replace(/href="(\S*)"/gi, 'href="' + ypath + '$1"');
                    headerStr = headerStr.replace(/src="(\S*)"/gi, 'src="' + ypath + '$1"');
                }
                replaceDom.innerHTML = headerStr;
            } else {
                replaceDom.innerHTML = '加载失败';
            }
        }
    }

    var url = 'com/footer.html';
    if(ypath){
    	url = ypath + url;
    }
    loadXMLDoc(url);
})();
