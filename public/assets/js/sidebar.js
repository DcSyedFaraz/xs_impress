$(document).ready(function(){
    $('.ltr-sidebar-toggler').on('click', function(){
        var leftNavigation = $('.ltr-navigation');
        var leftNavigationToggler = $(this);
        var navigationLabel = $('.sidebar-link-label');
        var angleLeft = $('.angle-left');
        var angleRight = $('.angle-right');
        var mainContent = $('.main-content');
        var formActions = $('.form-actions');

        if(leftNavigation.hasClass('ltr-sidebar-max')){
            leftNavigation.removeClass('ltr-sidebar-max');
            leftNavigation.addClass('ltr-sidebar-min');

            leftNavigationToggler.removeClass('ltr-sidebar-toggler-max');
            leftNavigationToggler.addClass('ltr-sidebar-toggler-min');

            mainContent.removeClass('mc-min');
            mainContent.addClass('mc-max');

            formActions.removeClass('fa-min');
            formActions.addClass('fa-max');

            navigationLabel.hide();
            angleLeft.hide();
            angleRight.removeClass('d-none');
        }else{
            leftNavigation.removeClass('ltr-sidebar-min');
            leftNavigation.addClass('ltr-sidebar-max');

            leftNavigationToggler.removeClass('ltr-sidebar-toggler-min');
            leftNavigationToggler.addClass('ltr-sidebar-toggler-max');

            mainContent.removeClass('mc-max');
            mainContent.addClass('mc-min');

            formActions.removeClass('fa-max');
            formActions.addClass('fa-min');

            navigationLabel.show();
            angleLeft.show();
            angleRight.addClass('d-none');
        }
    });
});
