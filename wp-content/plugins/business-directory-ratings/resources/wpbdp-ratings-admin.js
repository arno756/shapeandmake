// TODO: integrate this code in wpbdp-ratings.js (lots of duplication)

function wpbdp_raty_this(v) {
    var $obj = jQuery(v);
    var rating = $obj.attr('data-value');
    var readOnly = false;
    var scoreName = $obj.attr('data-field') ? $obj.attr('data-field') : 'score';

    if (typeof($obj.attr('data-readonly')) != 'undefined') readOnly = true;

    $obj.raty({number: WPBDP.ratings._config.number, halfShow: true, score: rating, readOnly: readOnly, path: WPBDP.ratings._config.path, scoreName: scoreName})
}

jQuery(function($){

    // stars inside the page
    var $stars = jQuery('.wpbdp-ratings-stars');
    $stars.each(function(i,v){ wpbdp_raty_this(v); });

    // delete link
    $('#wpbdp-ratings .row-actions a.delete').live('click', function(e){
        e.preventDefault();

        var $rating = $(this).parents('tr');
        var rating_id = $rating.attr('data-id');

        $.post(WPBDP.ratings._config.ajaxurl, {action: "wpbdp-ratings", a: "delete", id: rating_id}, function(res){
            if (res.success) {
                $rating.remove();

                if ($('#wpbdp-ratings table tr:not(.no-items)').length == 0) {
                    $('#wpbdp-ratings table tr.no-items').show();
                }
            }
        }, 'json');
    });

    // edit link
    // TODO: integrate with add-form below
    $('#wpbdp-ratings .row-actions a.edit').live('click', function(e){
        e.preventDefault();

        var $rating = $(this).parents('tr');
        var $editform = $('.comment-edit', $rating);
        $('.comment .comment', $rating).toggle();
        $editform.toggle();
    });

    // edit / cancel
    $('#wpbdp-ratings .comment-edit input.save-button').live('click', function(e){
        e.preventDefault();

        var $rating = $(this).parents('tr');
        var comment = $('.comment-edit textarea', $rating).val();

        $.post(WPBDP.ratings._config.ajaxurl, {action: "wpbdp-ratings", a: "edit", id: $rating.attr('data-id'), comment: comment}, function(res){
            if (res.success) {
                $('.comment-edit textarea', $rating).val(res.comment);
                $('.comment .comment', $rating).html(res.comment).show();
                $('.comment-edit', $rating).hide();
            } else {
                alert(res.msg);
            }
        }, 'json');
    });

    $('#wpbdp-ratings .comment-edit input.cancel-button').live('click', function(e){
        e.preventDefault();

        var $rating = $(this).parents('tr');
        jQuery('.comment .comment', $rating).show();
        jQuery('.comment-edit', $rating).hide();        
    });

    /* Add review form (admin) */
    $('#wpbdp-ratings-admin-post-review .add-review-link').click(function(e){
        e.preventDefault();
        $(this).hide();
        $('#wpbdp-ratings-admin-post-review .form').fadeIn();
    });

    $('#wpbdp-ratings-admin-post-review .form a.button-secondary').click(function(e){
        e.preventDefault();
        $('#wpbdp-ratings-admin-post-review .form').fadeOut( function(){
            $('#wpbdp-ratings-admin-post-review .add-review-link').show();
        });
    });

    $('#wpbdp-ratings-admin-post-review .form a.button-primary').click(function(e){
        e.preventDefault();

        var listing = $.trim($('input[name="wpbdp_ratings_rating[listing_id]"]').val());
        var author = $.trim($('input[name="wpbdp_ratings_rating[user_name]"]').val());
        var rating = $.trim($('input[name="wpbdp_ratings_rating[rating]"]').val());
        var comment = $.trim($('textarea[name="wpbdp_ratings_rating[comment]"]').val());

        var request = {
            "action": "wpbdp-ratings-add",
            "rating[listing_id]": listing,
            "rating[user_name]": author,
            "rating[rating]": rating,
            "rating[comment]": comment
        };

        $.post(ajaxurl, request, function(response){
            if (response.ok) {
                var $new = jQuery(response.html);

                $('#wpbdp-ratings table tr.no-items').hide();
                $('#wpbdp-ratings table').prepend($new);

                $('#wpbdp-ratings-admin-post-review .form input').val('');
                $('#wpbdp-ratings-admin-post-review .form textarea').val('');
                $('#wpbdp-ratings-admin-post-review .form .wpbdp-ratings-stars').raty('score', 0);
                $('#wpbdp-ratings-admin-post-review .form').hide();
                $('#wpbdp-ratings-admin-post-review .form a.button-secondary').click();
                wpbdp_raty_this($('.wpbdp-ratings-stars', $new));
            } else {
                alert(response.errormsg ? response.errormsg : 'Unknown Error');
            }
        }, 'json');

    });


});