/*

This script was created by Ian Lloyd (http://lloydi.com/). I'd appreciate it 
if you could keep this comment in the script if you choose to use it (albeit 
in an amended form.

The original blog entry relating to this can be found here: http://tinyurl.com/p9tqb.

The addEvent function comes courtesy of Scott Andrew: http://tinyurl.com/qcmrd

*/
function addEvent(elm, evType, fn, useCapture)
{
		if(elm.addEventListener)
		{
			elm.addEventListener(evType, fn, useCapture);
			return true;
		}
		else if (elm.attachEvent)
		{
			var r = elm.attachEvent('on' + evType, fn);
			return r;
		}
		else
		{
			elm['on' + evType] = fn;
		}
}

function addPrintLinks()
{
var el = document.getElementsByTagName("div");
for (i=0;i<el.length;i++)
	{
	if (el[i].className=="print_section")
		{
		var newLink = document.createElement("button");
		var newLinkText = document.createTextNode("打印");

		var newLinkPara = document.createElement("p");
		newLink.setAttribute("class","button");
		
		//set up the 'print this section' link
		newLink.setAttribute("class","button noprint");
		newLink.setAttribute("type","button");
		var btId = "printbut_" + el[i].id;
		newLink.setAttribute("id",btId);
		newLink.appendChild(newLinkText);
		newLink.setAttribute("href","#");
		newLinkPara.appendChild(newLink);

		//add the behaviours for the new links
		newLink.onclick = togglePrintDisplay;
		newLink.onkeypress = togglePrintDisplay;

		//insert the para and the two links into the DOM
		el[i].appendChild(newLinkPara);
		
		}
	}
}

function togglePrintDisplay(e)
{
var whatSection = this.id.split("_");
whatSection = whatSection[1];
var el = document.getElementsByTagName("div");
for (i=0;i<el.length;i++)
	{
	if (el[i].className.indexOf("section")!=-1)
		{
		el[i].removeAttribute("className");
		if (el[i].id==whatSection)
			{
			//show only this section for print
			el[i].setAttribute("className","print_section print");
			el[i].setAttribute("class","print_section print");
			}
		else
			{
			//hide the sections from print-out
			el[i].setAttribute("className","print_section noprint");
			el[i].setAttribute("class","print_section noprint");
			}
		}
	}

if (window.event) 
	{
	window.event.returnValue = false;
	window.event.cancelBubble = true;
	} 
else if (e) 
	{
	e.stopPropagation();
	e.preventDefault();
	}

window.print();
}

function printAll(e)
{
var el = document.getElementsByTagName("div");
for (i=0;i<el.length;i++)
	{
	if (el[i].className.indexOf("print_section")!=-1)
		{
		el[i].setAttribute("className","print_section print");
		el[i].setAttribute("class","print_section print");
		}
	}
if (window.event) 
	{
	window.event.returnValue = false;
	window.event.cancelBubble = true;
	} 
else if (e) 
	{
	e.stopPropagation();
	e.preventDefault();
	}
window.print();
}
addEvent(window, 'load', addPrintLinks, false);
