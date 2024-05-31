$('.filter-toggler').on('click', function(){
    var filter = $('.filters');
    if(filter.hasClass('filter-show')){
        filter.removeClass('filter-show');
        filter.removeClass('mt-4');
    }else{
        filter.addClass('filter-show');
        filter.addClass('mt-4');
    }
});
