$(function() {
    $("#search").autocomplete({
        source: function(request, response) {
            
            $.ajax({
                url: "/api/search",
                dataType: "json",
                data: {
                    query: request.term
                },
                success: function(data) {
                    var names = data.map(function(item) {
                        return item.name;
                    });
                    response(names);
                }
            });
        },
        minLength: 2
    });

    $("#ratingId", "#ratingModalId").rating({min: 0, max: 5, step: 0.1, stars: 5});

    $('#exampleModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        $('#video').attr('src', button.data('link'));
        $('#ratingModalId').attr('data-movieid', button.data('movieid'));
        $('#overview').html(button.data('overview'));
        $('#vote_count').html(button.data('vote'));  
        $('#ratingModalId').rating('update', button.data('average'));    
    });
    
    $('#exampleModal').on('hide.bs.modal', function(event) {
        $('#video').attr('src', '');
        $('#ratingModalId').attr('data-movieid', '');
        
        $('#overview').html('');      
    });

    $('#ratingModalId').on('change', function(event) {
        var inputValue = $(this).val();
        $.ajax({               
            url: '/api/rating',
            method: 'POST',                     
            data: {
                movie_id: $(this).attr('data-movieid'),
                score: inputValue
            },                    
            success: function(response) {
                //console.log(response);
            },
            error: function(xhr, status, error) {
                //console.error(error);
            }
        });
    });
  
    
});