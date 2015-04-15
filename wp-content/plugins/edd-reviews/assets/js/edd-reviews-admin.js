/**
 * Reviews JS for Backend
 */
 (function() {
	tinymce.create('tinymce.plugins.edd_reviews', {
		init : function(ed, url) {
			ed.addCommand('edd_reviews_mcebutton', function() {
				ed.windowManager.open({
					url: '?edd_reviews_mce_dialog=true',
					width: 400 + parseInt(ed.getLang('edd_reviews_dialog.delta_width', 0)),
					height: 120 + parseInt(ed.getLang('edd_reviews_dialog.delta_height', 0)),
					inline: 1
				}, {
					plugin_url: url
				});
			});

			ed.addButton('edd_reviews', {
				title : 'Recent posts',
				cmd: 'edd_reviews_mcebutton',
				image : url + '/../images/reviews.png'
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
		 	return {
				longname : 'Easy Digital Downloads Reviews',
				author : 'Sunny Ratilal',
				version : '1.0'
			};
		}
	});

	tinymce.PluginManager.add('edd_reviews', tinymce.plugins.edd_reviews);
})();
