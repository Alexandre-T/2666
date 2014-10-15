/**
* phpBB3 forum functions
*/

/**
* Window popup
*/
function popup(url, width, height, name)
{
	if (!name)
	{
		name = '_popup';
	}

	window.open(url.replace(/&amp;/g, '&'), name, 'height=' + height + ',resizable=yes,scrollbars=yes, width=' + width);
	return false;
}

/**
* Jump to page
*/
//www.phpBB-SEO.com SEO TOOLKIT BEGIN
function jumpto() {
	var page = prompt(jump_page, on_page);

	if (page !== null && !isNaN(page) && page == Math.floor(page) && page > 0) {
		var seo_page = (page - 1) * per_page;
		var anchor = '';
		var anchor_parts = base_url.split('#');
		if ( anchor_parts[1] ) {
			base_url = anchor_parts[0];
			anchor = '#' + anchor_parts[1];
		}
		if ( seo_page > 0 ) {
			var phpEXtest = false;
			if ( base_url.indexOf('?') >= 0 || ( phpEXtest = base_url.match(/\.php$/i))) {
				document.location.href = base_url.replace(/&amp;/g, '&') + (phpEXtest ? '?' : '&') + 'start=' + seo_page + anchor;
			} else {
				var ext = base_url.match(/\.[a-z0-9]+$/i);
				if (ext) {
					// location.ext => location-xx.ext
					document.location.href = base_url.replace(/\.[a-z0-9]+$/i, '') + seo_delim_start + seo_page + ext + anchor;
				} else {
					// location and location/ to location/pagexx.html
					var slash = base_url.match(/\/$/) ? '' : '/';
					document.location.href = base_url + slash + seo_static_pagination + seo_page + seo_ext_pagination + anchor;
				}
			}
		} else {
			document.location.href = base_url + anchor;
		}
	}
}
// Open external links in new window in a XHTML 1.x compliant way.
/**
*  phpbb_seo_href()
*  Fixes href="#something" links with virtual directories
*  Optionally open external or marked with a css class links in a new window
*  in a XHTML 1.x compliant way.
*/
function phpbb_seo_href() {

	var current_domain = document.domain.toLowerCase();
	if (!current_domain || !document.getElementsByTagName) return;
	if (seo_external_sub && current_domain.indexOf('.') >= 0) {
		current_domain = current_domain.replace(new RegExp(/^[a-z0-9_-]+\.([a-z0-9_-]+\.([a-z]{2,6}|[a-z]{2,3}\.[a-z]{2,3}))$/i), '$1');
	}
	if (seo_ext_classes) {
		var extclass = new RegExp("(^|\s)(" + seo_ext_classes + ")(\s|$)");
	}
	if (seo_hashfix) {
		var basehref = document.getElementsByTagName('base')[0];
		if (basehref) {
			basehref = basehref.href;
			var hashtest = new RegExp("^(" + basehref + "|)#[a-z0-9_-]+$");
			var current_href = document.location.href.replace(/#[a-z0-9_-]+$/i, "");
		} else {
			seo_hashfix = false;
		}
	}
	var hrefels = document.getElementsByTagName("a");
	var hrefelslen = hrefels.length;
	for (var i = 0; i < hrefelslen; i++) {
		var el = hrefels[i];
		var hrefinner = el.innerHTML.toLowerCase();
		if (el.onclick || (el.href == '') || (el.href.indexOf('javascript') >=0 ) || (el.href.indexOf('mailto') >=0 ) || (hrefinner.indexOf('<a') >= 0) ) {
			continue;
		}
		if (seo_hashfix && el.hash && hashtest.test(el.href)) {
			el.href = current_href + el.hash;
		}
		if (seo_external) {
			if ((el.href.indexOf(current_domain) >= 0) && !(seo_ext_classes && extclass.test(el.className))) {
				continue;
			}
			el.onclick = function () { window.open(this.href); return false; };
		}
	}
}
// www.phpBB-SEO.com SEO TOOLKIT START (modified by AT for this style)
$(function() {
	if (seo_external || seo_hashfix) {
		phpbb_seo_href();
	}
});
// www.phpBB-SEO.com SEO TOOLKIT END

/**
* Mark/unmark checklist
* id = ID of parent container, name = name prefix, state = state [true/false]
*/
function marklist(id, name, state)
{
	$("input[name^='"+ name +"']","#"+id).prop('checked', state);
	return false;
}

/**
* Resize viewable area for attached image or topic review panel (possibly others to come)
* e = element
*/
function viewableArea(e, itself)
{
	if (!e) return;
	if (!itself)
	{
		e = e.parentNode;
	}
	
	if (!e.vaHeight)
	{
		// Store viewable area height before changing style to auto
		e.vaHeight = e.offsetHeight;
		e.vaMaxHeight = e.style.maxHeight;
		e.style.height = 'auto';
		e.style.maxHeight = 'none';
		e.style.overflow = 'visible';
	}
	else
	{
		// Restore viewable area height to the default
		e.style.height = e.vaHeight + 'px';
		e.style.overflow = 'auto';
		e.style.maxHeight = e.vaMaxHeight;
		e.vaHeight = false;
	}
}

function selectCode(a)
{
	// Get ID of code block
	var e = a.parentNode.parentNode.getElementsByTagName('PRE')[0];

	// Not IE and IE9+
	if (window.getSelection)
	{
		var s = window.getSelection();
		// Safari
		if (s.setBaseAndExtent)
		{
			s.setBaseAndExtent(e, 0, e, e.innerText.length - 1);
		}
		// Firefox and Opera
		else
		{
			// workaround for bug # 42885
			if (window.opera && e.innerHTML.substring(e.innerHTML.length - 4) == '<BR>')
			{
				e.innerHTML = e.innerHTML + '&nbsp;';
			}

			var r = document.createRange();
			r.selectNodeContents(e);
			s.removeAllRanges();
			s.addRange(r);
		}
	}
	// Some older browsers
	else if (document.getSelection)
	{
		var s = document.getSelection();
		var r = document.createRange();
		r.selectNodeContents(e);
		s.removeAllRanges();
		s.addRange(r);
	}
	// IE
	else if (document.selection)
	{
		var r = document.body.createTextRange();
		r.moveToElementText(e);
		r.select();
	}
}

/**
* Play quicktime file by determining it's width/height
* from the displayed rectangle area
*/
function play_qt_file(obj)
{
	var rectangle = obj.GetRectangle();

	if (rectangle)
	{
		rectangle = rectangle.split(',');
		var x1 = parseInt(rectangle[0]);
		var x2 = parseInt(rectangle[2]);
		var y1 = parseInt(rectangle[1]);
		var y2 = parseInt(rectangle[3]);

		var width = (x1 < 0) ? (x1 * -1) + x2 : x2 - x1;
		var height = (y1 < 0) ? (y1 * -1) + y2 : y2 - y1;
	}
	else
	{
		var width = 200;
		var height = 0;
	}

	obj.width = width;
	obj.height = height + 16;

	obj.SetControllerVisible(true);
	obj.Play();
}
//AT MOD_USER BEGIN
/**
 * Activation d'un formulaire dans les étapes 7 / 8 et 9 de la création de personnage
 * 
 * @param button or a (link) element
 */
function validerBouton(bouton){
	bouton.removeClass('btn-default');
	bouton.addClass('btn-success');
	changerBoutonFinal();
}
function changerBoutonFinal(){
	var ok = $("#buttonFiche").hasClass('btn-success')
	      && $("#buttonFichePublique").hasClass('btn-success')
	      && $("#buttonContact1").hasClass('btn-success')
	      && $("#buttonContact2").hasClass('btn-success')
	      && $("#buttonContact3").hasClass('btn-success')
	      && $("#buttonContact4").hasClass('btn-success');
	if (ok){
		$("#buttonFinal").removeClass('disabled');
	}
}

/**
 * Activation d'un formulaire dans les étapes 7 / 8 et 9 de la création de personnage
 */
function activation(bool){
	$("#nom").prop('required', bool);
	$("#nom").prop('disabled', !bool);
	$("#avatar").prop('required', bool);
	$("#avatar").prop('disabled', !bool);
	$("#uploadfile").prop('required', bool);
	$("#uploadfile").prop('disabled', !bool);
	$("#uploadurl").prop('required', bool);
	$("#uploadurl").prop('disabled', !bool);
	$("#description").prop('required', bool);
	$("#description").prop('disabled', !bool);
	$("#resume").prop('required', bool);
	$("#resume").prop('disabled', !bool);
	if(bool){
		$("#contact").removeClass('hidden');
	}else{
		$("#contact").addClass('hidden');
	}
}
//AT MOD_USER END
/**
* Detect JQuery existance. We currently do not deliver it, but some styles do, so why not benefit from it. ;)
*/
var jquery_present = typeof jQuery == 'function';
