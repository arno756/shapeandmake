jQuery(document).ready(function(){
	addClickHandlers();
	jQuery(".jquery-prod-cat-value").change(function(){
		var CatValues = [];
		jQuery('.jquery-prod-cat-value').each(function() {if (jQuery(this).prop('checked')) {CatValues.push(jQuery(this).val());}});
		jQuery('#upcp-selected-categories').val(CatValues);
	});
	jQuery(".jquery-prod-sub-cat-value").change(function(){
		var SubCatValues = [];
		jQuery('.jquery-prod-sub-cat-value').each(function() {if (jQuery(this).prop('checked')) {SubCatValues.push(jQuery(this).val());}});
		jQuery('#upcp-selected-subcategories').val(SubCatValues);
	});
	jQuery(".jquery-prod-tag-value").change(function(){
		var TagValues = [];
		jQuery('.jquery-prod-tag-value').each(function() {if (jQuery(this).prop('checked')) {TagValues.push(jQuery(this).val());}});
		jQuery('#upcp-selected-tags').val(TagValues);
	});
	jQuery(".jquery-prod-name-text").keyup(function(){
		var prod_name = jQuery(this).val();
		jQuery('#upcp-selected-prod-name').val(prod_name);
	});
});

function addClickHandlers() {
	if (typeof maintain_filtering === 'undefined' || maintain_filtering === null) {maintain_filtering = "Yes";}

	if (maintain_filtering != "No") {
		jQuery(".upcp-catalogue-link").click(function(event){
			event.preventDefault();
    		var link = jQuery(this).attr('href');
    		jQuery("#upcp-hidden-filtering-form").attr('action', link);
    		jQuery("#upcp-hidden-filtering-form").submit();
		});
	}
}

function FieldFocus (Field) {
		if (Field.value == Field.defaultValue){
			  Field.value = '';
		}
}

function FieldBlur(Field) {
		if (Field.value == '') {
			  Field.value = Field.defaultValue;
		}
}

function UPCPHighlight(Field, Color) {
		if (jQuery(Field.parentNode).hasClass('highlight'+Color)) {
			  jQuery(Field.parentNode).removeClass('highlight'+Color);
		}
		else {
				jQuery(Field.parentNode).addClass('highlight'+Color);
		}
}

function UPCP_DisplayPage(PageNum) {
	jQuery('#upcp-current-page').html(PageNum);
	UPCP_Ajax_Filter();
}

var RequestCount = 0;
function UPCP_Ajax_Filter() {
	var CatValues = [];
	var SubCatValues = [];
	var TagBoxValues = [];
	var CFBoxValues = [];
	
	var id = jQuery('#upcp-catalogue-id').html();
	var sidebar = jQuery('#upcp-catalogue-sidebar').html();
	var start_layout = jQuery('#upcp-starting-layout').html();
	var current_layout = jQuery('#upcp-current-layout').html();
	var excluded_layouts = jQuery('#upcp-excluded-layouts').html();
	var current_page = jQuery('#upcp-current-page').html();
	var base_url = jQuery('#upcp-base-url').html();
	var default_search_text = jQuery('#upcp-default-search-text').html();
	
	jQuery('.jquery-prod-cat-value').each(function() {if (jQuery(this).prop('checked')) {CatValues.push(jQuery(this).val());}});
	jQuery('.jquery-prod-sub-cat-value').each(function() {if (jQuery(this).prop('checked')) {SubCatValues.push(jQuery(this).val());}});
	jQuery('.jquery-prod-tag-value').each(function() {if (jQuery(this).prop('checked')) {TagBoxValues.push(jQuery(this).val());}});
	jQuery('.jquery-prod-cf-value').each(function() {if (jQuery(this).prop('checked')) {
		Array_Value = jQuery(this).parent().parent().data('cfid') + "=>" + jQuery(this).val();
		CFBoxValues.push(Array_Value);
	}});
	
	if (jQuery('.prod-cat-sidebar').css('display') == "none") {var SelectedProdName = jQuery('#upcp-mobile-search').val();}
	else {var SelectedProdName = jQuery('#upcp-name-search').val();}

	if (SelectedProdName == undefined) {SelectedProdName = default_search_text;}

	jQuery('.prod-cat-inner').html('<h3>Updating results...</h3>');
	RequestCount = RequestCount + 1;
	var data = 'id=' + id + '&sidebar=' + sidebar + '&start_layout=' + current_layout + '&excluded_layouts=' + excluded_layouts + '&ajax_url=' + base_url + '&current_page=' + current_page + '&default_search_text=' + default_search_text + '&ajax_reload=Yes' + '&Prod_Name=' + SelectedProdName + '&Category=' + CatValues + '&SubCategory=' + SubCatValues + '&Tags=' + TagBoxValues + '&Custom_Fields=' + CFBoxValues + '&request_count=' + RequestCount + '&action=update_catalogue';
	jQuery.post(ajaxurl, data, function(response) {
		response = response.substring(0, response.length - 1);
		var parsed_response = jQuery.parseJSON(response);
		if (parsed_response.request_count == RequestCount) {
			jQuery('.prod-cat-inner').html(parsed_response.message);
			UPCP_Sort_By();
			adjustCatalogueHeight();
			addClickHandlers();
			ResetFancyBoxes();
		}
	});
}

function UPCP_Filer_Results() {
		
		Fail = false;
		var CatBoxValues = [];
		CatBoxValues.length = 0;
		var SubBoxValues = [];
		SubBoxValues.length = 0;
		var TagBoxValues = [];
		TagBoxValues.length = 0;
		
		jQuery('.upcp-clear').each(function() {jQuery(this).remove();});
		var TargetProdName = "";
		jQuery('.jquery-prod-name-text').each(function(){
				if (jQuery('.jquery-prod-name-text').val() != '' && jQuery('.jquery-prod-name-text').val() != 'Name...') {TargetProdName = jQuery('.jquery-prod-name-text').val();}
		});
		jQuery('.jquery-prod-cat-value').each(function() {if (jQuery(this).prop('checked')) {CatBoxValues.push(jQuery(this).val());}});
		jQuery('.jquery-prod-sub-cat-value').each(function() {if (jQuery(this).prop('checked')) {SubBoxValues.push(jQuery(this).val());}});
		jQuery('.jquery-prod-tag-value').each(function() {if (jQuery(this).prop('checked')) {TagBoxValues.push(jQuery(this).val());}});
		jQuery('.prod-cat-item').each(function() {
				var Fail = false;
				Any_Cat = false;
				Any_SubCat = false;
				var Categories = jQuery(this).children(".prod-cat-category-jquery").text();
				var SubCats = jQuery(this).children(".prod-cat-subcategory-jquery").text();
				var Tags = jQuery(this).children(".prod-cat-tag-jquery").text();
				var ProdNameUp = jQuery(this).children('.prod-cat-title-jquery').text();
				var ProdName = ProdNameUp.toLowerCase();
				if (TargetProdName != "") {if (ProdName.indexOf(TargetProdName.toLowerCase()) == -1) {Fail = true;}}
				jQuery.each(CatBoxValues, function(indexTwo, secondValue) {if (Categories.indexOf(" "+secondValue+",") != -1) {Any_Cat = true;}});
				jQuery.each(SubBoxValues, function(indexTwo, secondValue) {if (SubCats.indexOf(" "+secondValue+",") != -1) {Any_SubCat = true;}});
				jQuery.each(TagBoxValues, function(indexTwo, secondValue) {if (Tags.indexOf(" "+secondValue+",") == -1) {Fail = true;}});
				if (CatBoxValues.length > 0) {if (Any_Cat == false) {Fail = true;}}
				if (SubBoxValues.length > 0) {if (Any_SubCat == false) {Fail = true;}}
				if (Fail == true) {jQuery(this).addClass('Hide-Item');}
				else {jQuery(this).removeClass('Hide-Item'); Counter++;}
		});
		if (CatBoxValues.length > 0 ||  SubBoxValues.length > 0 || TagBoxValues.length > 0 || TargetProdName != "") {
				jQuery('.prod-cat-category-label').each(function() {jQuery(this).addClass('Hide-Item');});
		}
		else {
				jQuery('.prod-cat-category-label').each(function() {jQuery(this).removeClass('Hide-Item');});
		}
}

function UPCP_Filer_Results_OR() {
		
		Fail = false;
		Any_Tag = false;
		var CatBoxValues = [];
		var SubBoxValues = [];
		var TagBoxValues = [];
		
		jQuery('.upcp-clear').each(function() {jQuery(this).remove();});
		var TargetProdName = "";
		jQuery('.jquery-prod-name-text').each(function(){
				if (jQuery('.jquery-prod-name-text').val() != '' && jQuery('.jquery-prod-name-text').val() != 'Name...') {TargetProdName = jQuery('.jquery-prod-name-text').val();}
		});
		jQuery('.jquery-prod-cat-value').each(function() {if (jQuery(this).prop('checked')) {CatBoxValues.push(jQuery(this).val());}});
		jQuery('.jquery-prod-sub-cat-value').each(function() {if (jQuery(this).prop('checked')) {SubBoxValues.push(jQuery(this).val());}});
		jQuery('.jquery-prod-tag-value').each(function() {if (jQuery(this).prop('checked')) {TagBoxValues.push(jQuery(this).val());}});
		jQuery('.prod-cat-item').each(function() {
				Fail = false;
				Any_Cat = false;
				Any_SubCat = false;
				Any_Tag = false;
				Categories = jQuery(this).children(".prod-cat-category-jquery").text();
				SubCats = jQuery(this).children(".prod-cat-subcategory-jquery").text();
				Tags = jQuery(this).children(".prod-cat-tag-jquery").text();
				ProdName = jQuery(this).children('.prod-cat-title-jquery').text();
				ProdName = ProdName.toLowerCase();
				if (TargetProdName != "") {if (ProdName.indexOf(TargetProdName.toLowerCase()) == -1) {Fail = true;}}
				jQuery.each(CatBoxValues, function(indexTwo, secondValue) {if (Categories.indexOf(" "+secondValue+",") != -1) {Any_Cat = true;}});
				jQuery.each(SubBoxValues, function(indexTwo, secondValue) {if (SubCats.indexOf(" "+secondValue+",") != -1) {Any_SubCat = true;}});
				jQuery.each(TagBoxValues, function(indexTwo, secondValue) {if (Tags.indexOf(" "+secondValue+",") != -1) {Any_Tag = true;}});
				if (CatBoxValues.length > 0) {if (Any_Cat == false) {Fail = true;}}
				if (SubBoxValues.length > 0) {if (Any_SubCat == false) {Fail = true;}}
				if (TagBoxValues.length > 0) {if (Any_Tag == false) {Fail = true;}}
				if (Fail == true) {jQuery(this).addClass('Hide-Item');}
				else {jQuery(this).removeClass('Hide-Item'); Counter++;}
		});
		if (CatBoxValues.length > 0 ||  SubBoxValues.length > 0 || TagBoxValues.length > 0 || TargetProdName != "") {
				jQuery('.prod-cat-category-label').each(function() {jQuery(this).addClass('Hide-Item');});
				jQuery('.prod-cat-category').each(function() {jQuery(this).addClass('No-Clear');});
		}
		else {
				jQuery('.prod-cat-category-label').each(function() {jQuery(this).removeClass('Hide-Item');});
				jQuery('.prod-cat-category').each(function() {jQuery(this).removeClass('No-Clear');});
		}
}

/* Used in the bare-list layout to show or hide extra product details */
function ToggleItem(Item_ID) {
	if (jQuery('#prod-cat-details-'+Item_ID).css('display') == "none") {
		jQuery('#prod-cat-details-'+Item_ID).removeClass('hidden-field');
		adjustCatalogueHeight();
	}
	else {
		jQuery('#prod-cat-details-'+Item_ID).addClass('hidden-field');
		adjustCatalogueHeight();
	}
}

/* Used to track the number of times that a product is clicked in all catalogues */
function RecordView(Item_ID) {
		var data = 'Item_ID=' + Item_ID + '&action=record_view';
		jQuery.post(ajaxurl, data, function(response) {});
}

function ToggleView(DisplayType) {
		
		if (DisplayType == "Thumbnail" && jQuery('.thumb-display').hasClass('hidden-field')) {
			  jQuery('.list-display').animate({opacity: 0}, 500);
				jQuery('.detail-display').animate({opacity: 0}, 500);
				setTimeout(function(){jQuery('.thumb-display').animate({opacity: 1}, 500);})
				jQuery('.thumb-display').removeClass('hidden-field');
				jQuery('#upcp-current-layout').html('Thumbnail');
				setTimeout(function(){jQuery('.list-display').addClass('hidden-field');})
				setTimeout(function(){jQuery('.detail-display').addClass('hidden-field');})
		}
		if (DisplayType == "List" && jQuery('.list-display').hasClass('hidden-field')) {
			  jQuery('.thumb-display').animate({opacity: 0}, 500);
				jQuery('.detail-display').animate({opacity: 0}, 500);
				setTimeout(function(){jQuery('.list-display').animate({opacity: 1}, 500);})
				jQuery('.list-display').removeClass('hidden-field');
				jQuery('#upcp-current-layout').html('List');
				setTimeout(function(){jQuery('.thumb-display').addClass('hidden-field');})
				setTimeout(function(){jQuery('.detail-display').addClass('hidden-field');})
		}
		if (DisplayType == "Detail" && jQuery('.detail-display').hasClass('hidden-field')) {
			  jQuery('.list-display').animate({opacity: 0}, 500);
				jQuery('.thumb-display').animate({opacity: 0}, 500);
				setTimeout(function(){jQuery('.detail-display').animate({opacity: 1}, 500);})
				jQuery('.detail-display').removeClass('hidden-field');
				jQuery('#upcp-current-layout').html('Detail');
				setTimeout(function(){jQuery('.list-display').addClass('hidden-field');})
				setTimeout(function(){jQuery('.thumb-display').addClass('hidden-field');})
		}
		
		setTimeout(function(){adjustCatalogueHeight();})
		
		return false;
}

function ZoomImage(ProdID, ItemID) {
		if (ItemID == 0) {
			  PhotoSRC = jQuery('#prod-cat-addt-details-thumb-P'+ProdID).attr('src');
		}
		else {
				PhotoSRC = jQuery('#prod-cat-addt-details-thumb-'+ItemID).attr('src');
		}
		jQuery('.prod-cat-addt-details-main').each(function() {jQuery(this).attr('src', PhotoSRC)});
		html = '<div style="width:auto;height:auto;overflow: auto;position:relative;">';
		html += jQuery('#prod-cat-addt-details-'+ProdID).html();
		html += "</div>";
		jQuery("#fancybox-content").html(html);
}

function OpenProduct(ProdID) {
		setTimeout(function(){jQuery("#hidden_FB_link-"+ProdID).fancybox().trigger('click');}, 1000);
		//jQuery("#hidden_FB_link"+ProdID)[0].trigger('click');
}

jQuery(document).ready(function() 
{
     adjustCatalogueHeight();
});

function adjustCatalogueHeight() {
 		var objHeight = 0;
    jQuery.each(jQuery('.prod-cat-inner').children(), function(){
           if (!jQuery(this).hasClass('hidden-field')) {
					 		 objHeight = Math.max(jQuery(this).height(), objHeight);
					 }
    });
		objHeight = objHeight + 120;
    jQuery('.prod-cat-inner').height(objHeight);
}

/* Sort by price or by name */
jQuery.fn.sortElements = (function(){
 
    var sort = [].sort;
 
    return function(comparator, getSortable) {
 
        getSortable = getSortable || function(){return this;};
 
        var placements = this.map(function(){
 
            var sortElement = getSortable.call(this),
                parentNode = sortElement.parentNode,
 
                // Since the element itself will change position, we have
                // to have some way of storing its original position in
                // the DOM. The easiest way is to have a 'flag' node:
                nextSibling = parentNode.insertBefore(
                    document.createTextNode(''),
                    sortElement.nextSibling
                );
 
            return function() {
 
                if (parentNode === this) {
                    throw new Error(
                        "You can't sort elements if any one is a descendant of another."
                    );
                }
 
                // Insert before flag:
                parentNode.insertBefore(this, nextSibling);
                // Remove flag:
                parentNode.removeChild(nextSibling);
 
            };
 
        });
 
        return sort.call(this, comparator).each(function(i){
            placements[i].call(getSortable.call(this));
        });
 
    };
 
})();

function UPCP_Sort_By() {
		jQuery('.prod-cat-category-label').each(function() {jQuery(this).addClass('Hide-Item');});
		var SortBy = jQuery('#upcp-sort-by').val();
		if (SortBy == "name_asc") {SortByNameASC();}
		else if (SortBy == "name_desc") {SortByNameDESC();}
		else if (SortBy == "price_asc") {SortByPriceASC();}
		else {SortByPriceDESC();}
}

function SortByNameASC() {            
        jQuery('.thumb-display div .prod-cat-title').sortElements(function(a, b){
						return jQuery(a).text() > jQuery(b).text() ? 1 : -1;
        }, function() {
						return this.parentNode; //here
				});
				jQuery('.list-display div .prod-cat-title').sortElements(function(a, b){
						return jQuery(a).text() > jQuery(b).text() ? 1 : -1;
        }, function() {
						return this.parentNode;
				});
				jQuery('.detail-display div .prod-cat-title').sortElements(function(a, b){
						return jQuery(a).text() > jQuery(b).text() ? 1 : -1;
        }, function() {
						return this.parentNode.parentNode; //here
				});
}

function SortByNameDESC() {            
        jQuery('.thumb-display div .prod-cat-title').sortElements(function(a, b){
						return jQuery(a).text() < jQuery(b).text() ? 1 : -1;
        }, function() {
						return this.parentNode; //here
				});
				jQuery('.list-display div .prod-cat-title').sortElements(function(a, b){
						return jQuery(a).text() < jQuery(b).text() ? 1 : -1;
        }, function() {
						return this.parentNode; 
				});
				jQuery('.detail-display div .prod-cat-title').sortElements(function(a, b){
						return jQuery(a).text() < jQuery(b).text() ? 1 : -1;
        }, function() {
						return this.parentNode.parentNode; //here
				});
}

function SortByPriceASC() {            
        var first;
				var second;
				jQuery('.thumb-display div .prod-cat-price').sortElements(function(a, b){
						first = jQuery(a).text().replace(/\D/g,'');
						second = jQuery(b).text().replace(/\D/g,'');
						return Number(first) > Number(second) ? 1 : -1;
        }, function() {
						return this.parentNode;
				});
				jQuery('.list-display div .prod-cat-price').sortElements(function(a, b){
						first = jQuery(a).text().replace(/\D/g,'');
						second = jQuery(b).text().replace(/\D/g,'');
						return Number(first) > Number(second) ? 1 : -1;
        }, function() {
						return this.parentNode;
				});
				jQuery('.detail-display div .prod-cat-price').sortElements(function(a, b){
						first = jQuery(a).text().replace(/\D/g,'');
						second = jQuery(b).text().replace(/\D/g,'');
						return Number(first) > Number(second) ? 1 : -1;
        }, function() {
						return this.parentNode.parentNode; 
				});
}

function SortByPriceDESC() {
				var first;
				var second;
				jQuery('.thumb-display div .prod-cat-price').sortElements(function(a, b){
						first = jQuery(a).text().replace(/\D/g,'');
						second = jQuery(b).text().replace(/\D/g,'');
						return Number(first) < Number(second) ? 1 : -1;
        }, function() {
						return this.parentNode;
				});
				jQuery('.list-display div .prod-cat-price').sortElements(function(a, b){
						first = jQuery(a).text().replace(/\D/g,'');
						second = jQuery(b).text().replace(/\D/g,'');
						return Number(first) < Number(second) ? 1 : -1;
        }, function() {
						return this.parentNode;
				});
				jQuery('.detail-display div .prod-cat-price').sortElements(function(a, b){
						first = jQuery(a).text().replace(/\D/g,'');
						second = jQuery(b).text().replace(/\D/g,'');
						return Number(first) < Number(second) ? 1 : -1;
        }, function() {
						return this.parentNode.parentNode; 
				});
}


function ResetFancyBoxes() {

		jQuery.fn.getTitle = function() { // Copy the title of every IMG tag and add it to its parent A so that fancybox can show titles
				var arr = jQuery("a.fancybox");
				jQuery.each(arr, function() {
						var title = jQuery(this).children("img").attr("title");
						jQuery(this).attr('title',title);
				})
		}

		// Supported file extensions
		var thumbnails = jQuery("a:has(img)").not(".nolightbox").filter( function() { return /\.(jpe?g|png|gif|bmp)$/i.test(jQuery(this).attr('href')) });

		thumbnails.addClass("fancybox").getTitle();
		jQuery("a.fancybox").fancybox({
				'cyclic': false,
				'autoScale': true,
				'padding': 10,
				'opacity': true,
				'speedIn': 500,
				'speedOut': 500,
				'changeSpeed': 300,
				'overlayShow': true,
				'overlayOpacity': "0.3",
				'overlayColor': "#666666",
				'titleShow': true,
				'titlePosition': 'inside',
				'enableEscapeButton': true,
				'showCloseButton': true,
				'showNavArrows': true,
				'hideOnOverlayClick': true,
				'hideOnContentClick': false,
				'width': 560,
				'height': 340,
				'transitionIn': "fade",
				'transitionOut': "fade",
				'centerOnScroll': true
		});
}
