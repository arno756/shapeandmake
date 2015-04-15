/* This code is required to make changing the catalogue order a drag-and-drop affair */
jQuery(document).ready(function() {
		
		jQuery('.catalogue-list').sortable({
				items: '.list-item',
				opacity: 0.6,
				cursor: 'move',
				axis: 'y',
				update: function() {
						var order = jQuery(this).sortable('serialize') + '&action=catalogue_update_order';
						jQuery.post(ajaxurl, order, function(response) {});
				}
		});
});

function RecordView(Item_ID) {
		var data = 'Item_ID=' + Item_ID + '&action=record_view';
		jQuery.post(ajaxurl, data, function(response) {alert(response);});
		alert(data);
}